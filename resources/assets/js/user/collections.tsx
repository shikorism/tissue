import React, { useEffect, useState } from 'react';
import ReactDOM from 'react-dom';
import { BrowserRouter, Link, Outlet, Route, Routes, useNavigate, useParams } from 'react-router-dom';
import { Button } from 'react-bootstrap';
import { useQueryClient } from 'react-query';
import { useCollectionsQuery, useMyProfileQuery } from '../api';
import { showToast } from '../tissue';
import { fetchPostJson, ResponseError } from '../fetch';
import { QueryClientProvider } from '../query';
import { Collection } from './collection';
import {
    CollectionEditModal,
    CollectionFormErrors,
    CollectionFormValidationError,
    CollectionFormValues,
} from '../components/collections/CollectionEditModal';

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
};

const Sidebar: React.FC<SidebarProps> = ({ collections }) => {
    const { data: me } = useMyProfileQuery();
    const { username } = useParams();
    const navigate = useNavigate();
    const queryClient = useQueryClient();
    const [showCreateModal, setShowCreateModal] = useState(false);

    const handleSubmit = async (values: CollectionFormValues) => {
        try {
            const response = await fetchPostJson('/api/collections', values);
            if (response.status === 201) {
                const createdItem = await response.json();
                showToast('作成しました', { color: 'success', delay: 5000 });
                queryClient.invalidateQueries('MyCollections');
                queryClient.invalidateQueries(['Collections', createdItem.user_name]);
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
    const collectionsQuery = useCollectionsQuery(username as string);

    return (
        <div className="container">
            <div className="row">
                <div className="col-lg-4">
                    <Sidebar collections={collectionsQuery.data} />
                </div>
                <div className="col-lg-8">
                    {collectionsQuery.error?.response?.status === 403 ? (
                        <p className="mt-4">
                            <span className="oi oi-lock-locked" /> このユーザはチェックイン履歴を公開していません。
                        </p>
                    ) : (
                        <Outlet />
                    )}
                </div>
            </div>
        </div>
    );
};

const Index: React.FC = () => {
    const { username } = useParams();
    const navigate = useNavigate();
    const collectionsQuery = useCollectionsQuery(username as string);

    useEffect(() => {
        // リスト先頭のコレクションに自動遷移
        if (!collectionsQuery.isLoading && collectionsQuery.data && collectionsQuery.data.length > 0) {
            const first = collectionsQuery.data[0];
            navigate(`/user/${first.user_name}/collections/${first.id}`);
        }
    }, [collectionsQuery.isLoading]);

    if (!collectionsQuery.isLoading && collectionsQuery.data?.length === 0) {
        return <p className="mt-4">コレクションがありません。</p>;
    }

    return null;
};

ReactDOM.render(
    <QueryClientProvider>
        <BrowserRouter>
            <Routes>
                <Route path="/user/:username/collections" element={<Collections />}>
                    <Route index element={<Index />} />
                    <Route path=":id" element={<Collection />} />
                </Route>
            </Routes>
        </BrowserRouter>
    </QueryClientProvider>,
    document.getElementById('app')
);
