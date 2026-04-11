import React from 'react';
import { Link, Outlet, useLoaderData, useLocation, useRouteError } from 'react-router';
import { useSuspenseQuery } from '@tanstack/react-query';
import { getUserQuery } from '../api/query';
import { ResponseError } from '../api/errors';
import { NotFound } from './NotFound';
import { Tab, Tabs } from '../components/ui/Tabs';
import { LoaderData } from './User.loader';

export const User: React.FC = () => {
    const location = useLocation();
    const { username } = useLoaderData<LoaderData>();
    const { data: user } = useSuspenseQuery(getUserQuery(username));

    return (
        <div className="flex flex-col md:h-screen">
            <div className="px-4 flex flex-col gap-2 border-b-1 border-gray-border md:hidden">
                <Tabs className="flex-nowrap overflow-auto">
                    <Tab active={location.pathname.startsWith(`/user/${user.name}/checkins`)}>
                        <Link to={`/user/${user.name}/checkins`} className="block px-4 md:px-5 py-3">
                            チェックイン
                        </Link>
                    </Tab>
                    <Tab active={location.pathname.startsWith(`/user/${user.name}/stats`)}>
                        <Link to={`/user/${user.name}/stats`} className="block px-4 md:px-5 py-3">
                            グラフ
                        </Link>
                    </Tab>
                    <Tab active={location.pathname.startsWith(`/user/${user.name}/collections`)}>
                        <Link to={`/user/${user.name}/collections`} className="block px-4 md:px-5 py-3">
                            コレクション
                        </Link>
                    </Tab>
                </Tabs>
            </div>
            <Outlet />
        </div>
    );
};

export const ErrorBoundary: React.FC = () => {
    const error = useRouteError();

    if (error instanceof ResponseError && error.response.status === 404) {
        return <NotFound />;
    }

    throw error;
};

export default User;
