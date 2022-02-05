import React, { useContext, useEffect, useState } from 'react';
import ReactDOM from 'react-dom';
import { BrowserRouter, Link, Outlet, Route, Routes, useNavigate, useParams } from 'react-router-dom';
import { Button } from 'react-bootstrap';
import { MyProfileContext, useMyProfile } from '../context';
import { useFetchMyProfile, useFetchCollections, useFetchMyCollections } from '../api';
import { showToast } from '../tissue';
import { fetchPostJson, ResponseError } from '../fetch';
import { Collection } from './collection';
import {
    CollectionEditModal,
    CollectionFormErrors,
    CollectionFormValidationError,
    CollectionFormValues,
} from '../components/collections/CollectionEditModal';

export const CollectionsContext = React.createContext<ReturnType<typeof useFetchCollections> | undefined>(undefined);
export const MyCollectionsContext = React.createContext<ReturnType<typeof useFetchMyCollections> | undefined>(
    undefined
);

type SidebarItemProps = {
    collection: Tissue.Collection;
};

const SidebarItem: React.FC<SidebarItemProps> = ({ collection }) => {
    const { id } = useParams();

    if (collection.id == id) {
        return (
            <li className="list-group-item d-flex justify-content-between align-items-center active">
                <div style={{ wordBreak: 'break-all' }}>
                    <span className="oi oi-folder mr-1" />
                    {collection.title}
                </div>
            </li>
        );
    } else {
        return (
            <Link
                to={`/user/${collection.user_name}/collections/${collection.id}`}
                className="list-group-item d-flex justify-content-between align-items-center text-dark"
            >
                <div style={{ wordBreak: 'break-all' }}>
                    <span className="oi oi-folder text-secondary mr-1" />
                    {collection.title}
                </div>
            </Link>
        );
    }
};

type SidebarProps = {
    collections?: Tissue.Collection[];
    reloadCollections: () => void;
};

const Sidebar: React.FC<SidebarProps> = ({ collections, reloadCollections }) => {
    const me = useMyProfile();
    const { username } = useParams();
    const navigate = useNavigate();
    const [showCreateModal, setShowCreateModal] = useState(false);

    const handleSubmit = async (values: CollectionFormValues) => {
        try {
            const response = await fetchPostJson('/api/collections', values);
            if (response.status === 201) {
                const createdItem = await response.json();
                showToast('作成しました', { color: 'success', delay: 5000 });
                reloadCollections();
                setShowCreateModal(false);
                navigate(`/user/${createdItem.user_name}/collections/${createdItem.id}`);
                return;
            }
            throw new ResponseError(response);
        } catch (e) {
            console.error(e);
            if (e instanceof ResponseError && e.response.status == 422) {
                const data = await e.response.json();
                if (data.error?.violations) {
                    const errors: CollectionFormErrors = {};
                    for (const violation of data.error.violations) {
                        const field = violation.field as keyof CollectionFormErrors;
                        (errors[field] || (errors[field] = [])).push(violation.message);
                    }
                    throw new CollectionFormValidationError(errors);
                }
            }
            showToast('エラーが発生しました', { color: 'danger', delay: 5000 });
        }
    };

    if (!collections) {
        return null;
    }

    return (
        <div className="card mb-4">
            <div className="card-header d-flex justify-content-between align-items-center">
                <span>コレクション</span>
                {username === me?.name && (
                    <Button
                        variant="link"
                        className="text-secondary"
                        size="sm"
                        title="追加"
                        onClick={() => setShowCreateModal(true)}
                    >
                        <span className="oi oi-plus" />
                    </Button>
                )}
            </div>
            <div className="list-group list-group-flush">
                {collections.map((collection) => (
                    <SidebarItem key={collection.id} collection={collection} />
                ))}
                {collections.length === 0 && (
                    <li className="list-group-item d-flex justify-content-between align-items-center" />
                )}
            </div>
            <CollectionEditModal
                mode="create"
                initialValues={{ title: '', is_private: true }}
                onSubmit={handleSubmit}
                show={showCreateModal}
                onHide={() => setShowCreateModal(false)}
            />
        </div>
    );
};

const Collections: React.FC = () => {
    const { username } = useParams();
    const fetchMyProfile = useFetchMyProfile();
    const fetchMyCollections = useFetchMyCollections();
    const fetchCollections = useFetchCollections({ username: username as string });

    return (
        <MyProfileContext.Provider value={fetchMyProfile.data}>
            <div className="container">
                <div className="row">
                    <div className="col-lg-4">
                        <Sidebar
                            collections={fetchCollections.data}
                            reloadCollections={() => {
                                fetchMyCollections.reload();
                                fetchCollections.reload();
                            }}
                        />
                    </div>
                    <div className="col-lg-8">
                        {fetchCollections.error?.response?.status === 403 ? (
                            <p className="mt-4">
                                <span className="oi oi-lock-locked" /> このユーザはチェックイン履歴を公開していません。
                            </p>
                        ) : (
                            <MyCollectionsContext.Provider value={fetchMyCollections}>
                                <CollectionsContext.Provider value={fetchCollections}>
                                    <Outlet />
                                </CollectionsContext.Provider>
                            </MyCollectionsContext.Provider>
                        )}
                    </div>
                </div>
            </div>
        </MyProfileContext.Provider>
    );
};

const Index: React.FC = () => {
    const navigate = useNavigate();
    const collections = useContext(CollectionsContext);

    useEffect(() => {
        // リスト先頭のコレクションに自動遷移
        if (collections && !collections.loading && collections.data && collections.data.length > 0) {
            const first = collections.data[0];
            navigate(`/user/${first.user_name}/collections/${first.id}`);
        }
    }, [collections?.loading]);

    if (collections && !collections.loading && collections.data?.length === 0) {
        return <p className="mt-4">コレクションがありません。</p>;
    }

    return null;
};

ReactDOM.render(
    <BrowserRouter>
        <Routes>
            <Route path="/user/:username/collections" element={<Collections />}>
                <Route index element={<Index />} />
                <Route path=":id" element={<Collection />} />
            </Route>
        </Routes>
    </BrowserRouter>,
    document.getElementById('app')
);
