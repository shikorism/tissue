import React from 'react';
import { useLoaderData, useRouteError } from 'react-router';
import { LoaderData, PER_PAGE } from './SearchCheckins.loader';
import { useSuspenseQuery } from '@tanstack/react-query';
import { getSearchCheckinsQuery } from '../api/query';
import { Checkin } from '../features/checkins/Checkin';
import { Pagination } from '../components/Pagination';
import { EmptyQueryError } from '../features/search/EmptyQueryError';

export const SearchCheckins: React.FC = () => {
    const { query } = useLoaderData<LoaderData>();
    const { data } = useSuspenseQuery(getSearchCheckinsQuery(query));

    return (
        <div className="p-4">
            <p className="mb-4 text-secondary">
                <b>{data.totalCount}</b> 件見つかりました
            </p>
            <div className="grid grid-cols-1 lg:grid-cols-2 2xl:grid-cols-3">
                {data.data.map((checkin) => (
                    <Checkin
                        key={checkin.id}
                        checkin={checkin}
                        className="px-2 border-t-1 border-gray-border"
                        showActions
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
