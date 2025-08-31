import React from 'react';
import { Link, useLoaderData, useRouteError } from 'react-router';
import { LoaderData, PER_PAGE } from './UserCollection.loader';
import { useSuspenseQuery } from '@tanstack/react-query';
import { getCollectionItemsQuery, getCollectionQuery } from '../api/query';
import { Button } from '../components/Button';
import { useCurrentUser } from '../components/AuthProvider';
import { Pagination } from '../components/Pagination';
import { CollectionItem } from '../features/collections/CollectionItem';
import { ResponseError } from '../api/errors';

export const UserCollection: React.FC = () => {
    const { user: me } = useCurrentUser();
    const { collectionId, query } = useLoaderData<LoaderData>();
    const { data: collection } = useSuspenseQuery(getCollectionQuery(collectionId));
    const {
        data: { data, totalCount },
    } = useSuspenseQuery(getCollectionItemsQuery(collectionId, query));

    return (
        <div className="grow-1">
            <div className="flex-1 px-4 mx-auto lg:max-w-[1080px]">
                <div className="flex justify-between items-center mt-2 pb-2 text-secondary border-b-1 border-gray-border">
                    <Link to=".." relative="path">
                        <i className="ti ti-chevron-left mr-1" />
                        コレクション一覧
                    </Link>
                    {collection.user_name === me?.name && (
                        <div className="flex gap-2">
                            <Button as={Link} variant="primary" to={`/collect?collection=${collection.id}`}>
                                <i className="ti ti-plus mr-2" />
                                オカズを追加
                            </Button>
                            <Button>
                                <i className="ti ti-edit mr-2" />
                                設定
                            </Button>
                            <Button>
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
                {data.map((item) => (
                    <CollectionItem
                        key={item.id}
                        collection={collection}
                        item={item}
                        className="border-b-1 border-gray-border"
                    />
                ))}
                {totalCount ? (
                    <Pagination className="my-4" totalCount={totalCount} perPage={PER_PAGE} />
                ) : (
                    <div className="py-4">このコレクションにはまだオカズが登録されていません。</div>
                )}
            </div>
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
