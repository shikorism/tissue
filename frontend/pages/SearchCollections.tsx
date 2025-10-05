import React from 'react';
import { useLoaderData, useRouteError } from 'react-router';
import { LoaderData, PER_PAGE } from './SearchCollections.loader';
import { useSuspenseQuery } from '@tanstack/react-query';
import { getSearchCollectionsQuery } from '../api/query';
import { useScrollToTop } from '../hooks/useScrollToTop';
import { CollectionItem } from '../features/collections/CollectionItem';
import { Pagination } from '../components/Pagination';
import { EmptyQueryError } from '../features/search/EmptyQueryError';

export const SearchCollections: React.FC = () => {
    const { query } = useLoaderData<LoaderData>();
    const { data } = useSuspenseQuery(getSearchCollectionsQuery(query));
    useScrollToTop([query.page]);

    return (
        <div className="p-4">
            <p className="mb-4 text-secondary">
                <b>{data.totalCount}</b> 件見つかりました
            </p>
            <div className="grid grid-cols-1 lg:grid-cols-2 2xl:grid-cols-3">
                {data.data.map((item) => (
                    <CollectionItem
                        key={item.id}
                        collection={item.collection}
                        item={item}
                        className="px-2 border-t-1 border-gray-border"
                        showCollectionInfo
                    />
                ))}
            </div>
            {!!data.totalCount && <Pagination className="mt-4" totalCount={data.totalCount} perPage={PER_PAGE} />}
        </div>
    );
};

export const ErrorBoundary: React.FC = () => {
    const error = useRouteError();

    if (error instanceof EmptyQueryError) {
        return <div className="p-4 text-secondary">キーワードを入力してください</div>;
    }

    throw error;
};
