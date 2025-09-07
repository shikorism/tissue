import React, { useState } from 'react';
import { Link, useLoaderData, useNavigate, useRouteError } from 'react-router';
import { LoaderData, PER_PAGE } from './UserCollection.loader';
import { useSuspenseQuery } from '@tanstack/react-query';
import { getCollectionItemsQuery, getCollectionQuery } from '../api/query';
import { Button } from '../components/Button';
import { useCurrentUser } from '../components/AuthProvider';
import { Pagination } from '../components/Pagination';
import { CollectionItem } from '../features/collections/CollectionItem';
import { ResponseError } from '../api/errors';
import {
    CollectionEditModal,
    CollectionFormErrors,
    CollectionFormValidationError,
    CollectionFormValues,
} from '../features/collections/CollectionEditModal';
import { useDeleteCollection, usePutCollection } from '../api/mutation';
import { toast } from 'sonner';
import { Modal, ModalBody, ModalFooter, ModalHeader } from '../components/Modal';
import { ProgressButton } from '../components/ProgressButton';

export const UserCollection: React.FC = () => {
    const { user: me } = useCurrentUser();
    const navigate = useNavigate();
    const { collectionId, query } = useLoaderData<LoaderData>();
    const { data: collection } = useSuspenseQuery(getCollectionQuery(collectionId));
    const {
        data: { data, totalCount },
    } = useSuspenseQuery(getCollectionItemsQuery(collectionId, query));
    const [isOpenEditModal, setIsOpenEditModal] = useState(false);
    const putCollection = usePutCollection();
    const [isOpenDeleteModal, setIsOpenDeleteModal] = useState(false);
    const deleteCollection = useDeleteCollection();

    const handleSubmit = async (values: CollectionFormValues) => {
        try {
            await putCollection.mutateAsync({ collectionId, body: values });
            toast.success('更新しました');
            setIsOpenEditModal(false);
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

    const handleClickDelete = async () => {
        deleteCollection.mutate(collectionId, {
            onSuccess: () => {
                setIsOpenDeleteModal(false);
                toast.success('削除しました');
                navigate('../', { relative: 'path' });
            },
            onError: () => {
                toast.error('削除中にエラーが発生しました');
            },
        });
    };

    return (
        <div className="grow-1">
            <div className="flex-1 px-4">
                <div className="flex justify-between items-center mt-2 pb-2 text-secondary border-b-1 border-gray-border">
                    <Link to=".." relative="path">
                        <i className="ti ti-chevron-left mr-1" />
                        <span className="hidden lg:inline">コレクション</span>一覧
                    </Link>
                    {collection.user_name === me?.name && (
                        <div className="flex gap-2">
                            <Button as={Link} variant="primary" to={`/collect?collection=${collection.id}`}>
                                <i className="ti ti-plus mr-2" />
                                <span className="hidden lg:inline">オカズを</span>追加
                            </Button>
                            <Button onClick={() => setIsOpenEditModal(true)}>
                                <i className="ti ti-edit mr-2" />
                                設定
                            </Button>
                            <Button onClick={() => setIsOpenDeleteModal(true)}>
                                <i className="ti ti-trash mr-2" />
                                削除
                            </Button>
                        </div>
                    )}
                </div>
                <div className="py-4 border-b-1 border-gray-border">
                    <h2 className="text-xl font-bold break-all">{collection.title}</h2>
                    <div className="mt-2 text-sm text-secondary">
                        {collection.is_private ? (
                            <>
                                <i className="ti ti-lock mr-1" />
                                非公開コレクション
                            </>
                        ) : (
                            <>
                                <i className="ti ti-lock-open mr-1" />
                                公開コレクション
                            </>
                        )}
                    </div>
                </div>
                <div className="grid grid-cols-1 lg:grid-cols-2 2xl:grid-cols-3">
                    {data.map((item) => (
                        <CollectionItem
                            key={item.id}
                            collection={collection}
                            item={item}
                            className="px-2 border-b-1 border-gray-border"
                        />
                    ))}
                </div>
                {totalCount ? (
                    <Pagination className="my-4" totalCount={totalCount} perPage={PER_PAGE} />
                ) : (
                    <div className="py-4">このコレクションにはまだオカズが登録されていません。</div>
                )}
            </div>
            <CollectionEditModal
                mode="edit"
                initialValues={{ title: collection.title, is_private: collection.is_private }}
                onSubmit={handleSubmit}
                isOpen={isOpenEditModal}
                onClose={() => setIsOpenEditModal(false)}
            />
            <Modal isOpen={isOpenDeleteModal} onClose={() => setIsOpenDeleteModal(false)}>
                <ModalHeader closeButton>削除確認</ModalHeader>
                <ModalBody>コレクションを削除してもよろしいですか？</ModalBody>
                <ModalFooter>
                    <Button onClick={() => setIsOpenDeleteModal(false)}>キャンセル</Button>
                    <ProgressButton
                        label="削除"
                        variant="danger"
                        inProgress={deleteCollection.isPending}
                        disabled={deleteCollection.isPending}
                        onClick={handleClickDelete}
                    />
                </ModalFooter>
            </Modal>
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
