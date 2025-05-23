import React from 'react';
import { useLoaderData } from 'react-router';
import { useSuspenseQuery } from '@tanstack/react-query';
import { getUserLikesQuery } from '../api/query';
import { LoaderData, PER_PAGE } from './UserLikes.loader';
import { Checkin } from '../components/Checkin';
import { Pagination } from '../components/Pagination';
import { useScrollToTop } from '../hooks/useScrollToTop';

export const UserLikes: React.FC = () => {
    const { username, likesQuery } = useLoaderData<LoaderData>();
    const {
        data: { data, totalCount },
    } = useSuspenseQuery(getUserLikesQuery(username, likesQuery));
    useScrollToTop([likesQuery?.page]);

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
                <div className="py-4">いいねしたチェックインがありません。</div>
            )}
        </div>
    );
};
