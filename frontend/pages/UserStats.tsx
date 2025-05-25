import React from 'react';
import { Outlet, useLoaderData } from 'react-router';
import { LoaderData } from './UserStats.loader';
import { useSuspenseQuery } from '@tanstack/react-query';
import { getUserStatsCheckinOldestQuery } from '../api/query';

export const UserStats: React.FC = () => {
    const { username } = useLoaderData<LoaderData>();
    const { data: oldestData } = useSuspenseQuery(getUserStatsCheckinOldestQuery(username));

    return (
        <div className="flex flex-col lg:flex-row grow-1">
            <div className="p-4 lg:w-[280px] border-b-1 lg:border-b-0 lg:border-r-1 border-gray-border">
                {JSON.stringify(oldestData)}
            </div>
            <div className="flex-1">
                <Outlet />
            </div>
        </div>
    );
};
