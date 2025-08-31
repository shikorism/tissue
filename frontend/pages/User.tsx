import React from 'react';
import { Link, Outlet, useLoaderData, useLocation, useRouteError } from 'react-router';
import { useSuspenseQuery } from '@tanstack/react-query';
import { getUserQuery } from '../api/query';
import { ResponseError } from '../api/errors';
import { NotFound } from './NotFound';
import { Tab, Tabs } from '../components/Tabs';
import { LoaderData } from './User.loader';

export const User: React.FC = () => {
    const location = useLocation();
    const { username } = useLoaderData<LoaderData>();
    const { data: user } = useSuspenseQuery(getUserQuery(username));

    return (
        <div className="flex flex-col md:h-screen">
            <div className="px-4 pt-4 flex flex-col gap-2 border-b-1 border-gray-border">
                <div className="flex items-end gap-1">
                    <img
                        className="rounded inline-block mr-1"
                        src={user.profile_image_url}
                        alt={`${user.display_name}'s Avatar`}
                        width={48}
                        height={48}
                    />
                    <div className="flex flex-col overflow-hidden truncate">
                        <div className="text-lg font-medium">{user.display_name}</div>
                        <div className="text-xs text-secondary">
                            @{user.name}
                            {user.is_protected && <i className="ti ti-lock text-muted ml-0.5" />}
                        </div>
                    </div>
                </div>
                <div className="">
                    <Tabs className="flex-nowrap overflow-auto">
                        <Tab active={location.pathname === `/user/${user.name}`}>
                            <Link to={`/user/${user.name}`} className="block px-4 md:px-5 py-3">
                                プロフィール
                            </Link>
                        </Tab>
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
                        <Tab active={location.pathname.startsWith(`/user/${user.name}/likes`)}>
                            <Link to={`/user/${user.name}/likes`} className="block px-4 md:px-5 py-3">
                                いいね
                            </Link>
                        </Tab>
                        <Tab active={location.pathname.startsWith(`/user/${user.name}/collections`)}>
                            <Link to={`/user/${user.name}/collections`} className="block px-4 md:px-5 py-3">
                                コレクション
                            </Link>
                        </Tab>
                    </Tabs>
                </div>
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
