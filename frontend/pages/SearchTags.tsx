import React from 'react';
import { Link, useLoaderData, useRouteError } from 'react-router';
import { LoaderData, PER_PAGE } from './SearchTags.loader';
import { useSuspenseQuery } from '@tanstack/react-query';
import { getSearchTagsQuery } from '../api/query';
import { Pagination } from '../components/Pagination';
import { EmptyQueryError } from '../features/search/EmptyQueryError';

export const SearchTags: React.FC = () => {
    const { query } = useLoaderData<LoaderData>();
    const { data } = useSuspenseQuery(getSearchTagsQuery(query));

    return (
        <div className="p-4">
            <p className="mb-4 text-secondary">
                <b>{data.totalCount}</b> 件見つかりました
            </p>
            <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                {data.data.map((tag) => (
                    <div key={tag.name}>
                        <Link
                            to={{ pathname: `/search`, search: `?q=${tag.name}` }}
                            className="block px-4 py-2 text-center bg-neutral-200 hover:bg-neutral-300 focus:bg-neutral-300 outline-none transition-colors [clip-path:polygon(20px_0%,100%_0%,100%_100%,20px_100%,0%_50%)]"
                        >
                            {tag.name}
                        </Link>
                    </div>
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

export default SearchTags;
