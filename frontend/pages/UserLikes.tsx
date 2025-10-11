import React from 'react';
import { useLoaderData, useRouteError } from 'react-router';
import { useSuspenseQuery } from '@tanstack/react-query';
import { getUserLikesQuery } from '../api/query';
import { LoaderData, PER_PAGE } from './UserLikes.loader';
import { Checkin } from '../features/checkins/Checkin';
import { Pagination } from '../components/Pagination';
import { ResponseError } from '../api/errors';

export const UserLikes: React.FC = () => {
    const { username, likesQuery } = useLoaderData<LoaderData>();
    const {
        data: { data, totalCount },
    } = useSuspenseQuery(getUserLikesQuery(username, likesQuery));

    return (
        <div className="px-2">
            <div className="grid grid-cols-1 lg:grid-cols-2 2xl:grid-cols-3">
                {data?.map((checkin) => (
                    <Checkin
                        key={checkin.id}
                        checkin={checkin}
                        className="px-2 border-b-1 border-gray-border"
                        showActions
                    />
                ))}
            </div>
            {totalCount ? (
                <Pagination className="my-4" totalCount={totalCount} perPage={PER_PAGE} />
            ) : (
                <div className="px-2 py-4">いいねしたチェックインがありません。</div>
            )}
        </div>
    );
};

export const ErrorBoundary: React.FC = () => {
    const error = useRouteError();

    if (error instanceof ResponseError && error.response.status === 403) {
        return <div className="p-4">このユーザはいいね一覧を公開していません。</div>;
    }

    throw error;
};
