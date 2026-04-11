import React, { useState } from 'react';
import { Link, useLoaderData, useNavigate, useRouteError } from 'react-router';
import { LoaderData } from './UserCollections.loader';
import { useSuspenseQuery } from '@tanstack/react-query';
import { getUserCollectionsQuery } from '../api/query';
import { Button } from '../components/ui/Button';
import { useCurrentUser } from '../components/AuthProvider';
import { ResponseError } from '../api/errors';
import {
    CollectionEditModal,
    CollectionFormValues,
    CollectionFormErrors,
    CollectionFormValidationError,
} from '../features/collections/CollectionEditModal';
import { usePostCollections } from '../api/mutation';
import { toast } from 'sonner';
import { Container } from '../components/Container';
import { ColumnHeader } from '../components/ColumnHeader';
import { SortKeySelect } from '../features/collections/SortKeySelect';
import { SearchInput } from '../components/ui/SearchInput';
import { SortKey, sortAndFilteredCollections } from '../features/collections/search';

export const UserCollections: React.FC = () => {
    const { user: me } = useCurrentUser();
    const navigate = useNavigate();
    const { username } = useLoaderData<LoaderData>();
    const { data } = useSuspenseQuery(getUserCollectionsQuery(username));
    const [isOpenCreateModal, setIsOpenCreateModal] = useState(false);
    const postCollections = usePostCollections();

    const [isOpenSearchArea, setIsOpenSearchArea] = useState(false);
    const [filter, setFilter] = useState('');
    const [sort, setSort] = useState<SortKey>('id:asc');

    const handleSubmit = async (values: CollectionFormValues) => {
        try {
            const response = await postCollections.mutateAsync(values);
            toast.success('作成しました');
            navigate(`/user/${username}/collections/${response.id}`);
        } catch (e) {
            if (e instanceof ResponseError && e.response.status === 422) {
                if (e.error?.violations) {
                    const errors: CollectionFormErrors = {};
                    for (const violation of e.error.violations) {
                        const field = violation.field as keyof CollectionFormErrors;
                        (errors[field] || (errors[field] = [])).push(violation.message);
                    }
                    throw new CollectionFormValidationError(errors);
                } else if (e.error?.message) {
                    toast.error(e.error.message);
                    return;
                }
            }
        }
    };

    return (
        <div className="grow-1">
            <Container size="md" className="flex-1 py-0">
                <ColumnHeader className="flex justify-between items-center">
                    コレクション一覧
                    <div className="flex gap-2">
                        <Button onClick={() => setIsOpenSearchArea((v) => !v)}>
                            <i className="ti ti-search" />
                            <span className="mx-1 border-r-1 border-current/50" />
                            <i className="ti ti-sort-ascending-letters" />
                        </Button>
                        {username === me?.name && (
                            <Button onClick={() => setIsOpenCreateModal(true)}>
                                <i className="ti ti-plus mr-2" />
                                新規作成
                            </Button>
                        )}
                    </div>
                </ColumnHeader>
                {isOpenSearchArea && (
                    <div className="p-2 border-b-1 border-gray-border bg-gray-back *:bg-white flex flex-col gap-2">
                        <SearchInput
                            type="search"
                            name="q"
                            className="rounded-sm"
                            value={filter}
                            onChange={(e) => setFilter(e.target.value)}
                            placeholder="名前で絞り込み..."
                            small
                        />
                        <SortKeySelect className="w-auto" value={sort} onChange={setSort} />
                    </div>
                )}
                <ul className="flex flex-col">
                    {sortAndFilteredCollections(data, sort, filter).map((collection) => (
                        <li key={collection.id}>
                            <Link
                                to={`/user/${username}/collections/${collection.id}`}
                                className="p-2 block border-b-1 border-gray-border break-all hover:bg-neutral-100"
                            >
                                <div className="flex gap-2">
                                    <i className="ti ti-folder mt-1" />
                                    <div>{collection.title}</div>
                                </div>
                                <div className="flex gap-2 mt-1 items-baseline text-xs text-secondary">
                                    {collection.is_private ? (
                                        <>
                                            <i className="ti ti-lock ml-1" />
                                            非公開コレクション
                                        </>
                                    ) : (
                                        <>
                                            <i className="ti ti-lock-open ml-1" />
                                            公開コレクション
                                        </>
                                    )}
                                </div>
                            </Link>
                        </li>
                    ))}
                    {data.length === 0 && <li className="py-4">コレクションがありません。</li>}
                </ul>
            </Container>
            <CollectionEditModal
                mode="create"
                initialValues={{ title: '', is_private: true }}
                onSubmit={handleSubmit}
                isOpen={isOpenCreateModal}
                onClose={() => setIsOpenCreateModal(false)}
            />
        </div>
    );
};

export const ErrorBoundary: React.FC = () => {
    const error = useRouteError();

    if (error instanceof ResponseError && error.response.status === 403) {
        return <div className="p-4">このユーザはチェックイン履歴を公開していません。</div>;
    }

    throw error;
};

export default UserCollections;
