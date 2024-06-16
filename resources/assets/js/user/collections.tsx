import React, { useEffect, useState } from 'react';
import { createRoot } from 'react-dom/client';
import { BrowserRouter, Link, Outlet, Route, Routes, useNavigate, useParams } from 'react-router-dom';
import { Button } from 'react-bootstrap';
import { useQueryClient } from '@tanstack/react-query';
import classNames from 'classnames';
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
    const isSelected = collection.id == id;

    return (
        <Link
            to={`/user/${collection.user_name}/collections/${collection.id}`}
            className={classNames(
                'list-group-item',
                'list-group-item-action',
                'd-flex',
                'justify-content-between',
                'align-items-center',
                isSelected ? 'active' : 'text-dark',
            )}
        >
            <div style={{ wordBreak: 'break-all' }}>
                <i className={classNames('ti', 'ti-folder', 'mr-1', { 'text-secondary': !isSelected })} />
                {collection.title}
            </div>
        </Link>
    );
};

type SidebarProps = {
    collections?: Tissue.Collection[];
};

const Sidebar: React.FC<SidebarProps> = ({ collections }) => {
    const { data: me } = useMyProfileQuery();
    const { username } = useParams();
    const navigate = useNavigate();
    const queryClient = useQueryClient();
    const [filter, setFilter] = useState('');
    const [showCreateModal, setShowCreateModal] = useState(false);
    const [order, setOrder] = useState<'asc' | 'desc'>('asc');

    const handleSubmit = async (values: CollectionFormValues) => {
        try {
            const response = await fetchPostJson('/api/collections', values);
            if (response.status === 201) {
                const createdItem = await response.json();
                showToast('作成しました', { color: 'success', delay: 5000 });
                queryClient.invalidateQueries(['MyCollections']);
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
                } else if (data.error?.message) {
                    showToast(data.error.message, { color: 'danger', delay: 5000 });
                    return;
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
            <div className="card-header d-flex justify-content-between align-items-center" style={{ gap: '1rem' }}>
                <div className="flex-grow-1">
                    <input
                        className="form-control"
                        type="search"
                        placeholder="検索"
                        value={filter}
                        onChange={(e) => setFilter(e.target.value)}
                    />
                </div>
                <div>
                    {username === me?.name && (
                        <Button
                            variant=""
                            className="text-secondary mr-2"
                            size="sm"
                            title="追加"
                            onClick={() => setShowCreateModal(true)}
                        >
                            <i className="ti ti-plus text-large" />
                        </Button>
                    )}
                    <Button
                        variant=""
                        className="text-secondary"
                        size="sm"
                        title="並べ替え"
                        onClick={() => setOrder((prev) => (prev === 'asc' ? 'desc' : 'asc'))}
                    >
                        {order === 'asc' ? (
                            <i className="ti ti-sort-ascending-letters text-large"></i>
                        ) : (
                            <i className="ti ti-sort-descending-letters text-large"></i>
                        )}
                    </Button>
                </div>
            </div>
            <div className="list-group list-group-flush">
                {collections
                    .sort((a, b) => (order === 'asc' ? a.id - b.id : b.id - a.id))
                    .filter((collection) =>
                        filter ? collection.title.toLowerCase().includes(filter.toLowerCase()) : true,
                    )
                    .map((collection) => (
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
                            <i className="ti ti-lock" /> このユーザはチェックイン履歴を公開していません。
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

createRoot(document.getElementById('app') as HTMLElement).render(
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
);
