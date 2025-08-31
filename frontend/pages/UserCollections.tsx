import React from 'react';
import { Link, useLoaderData, useRouteError } from 'react-router';
import { LoaderData } from './UserCollections.loader';
import { useSuspenseQuery } from '@tanstack/react-query';
import { getUserCollectionsQuery } from '../api/query';
import { Button } from '../components/Button';
import { useCurrentUser } from '../components/AuthProvider';
import { ResponseError } from '../api/errors';

export const UserCollections: React.FC = () => {
    const { user: me } = useCurrentUser();
    const { username } = useLoaderData<LoaderData>();
    const { data } = useSuspenseQuery(getUserCollectionsQuery(username));

    return (
        <div className="grow-1">
            <div className="flex-1 px-4 mx-auto lg:max-w-[1080px]">
                <h2 className="flex justify-between items-center mt-2 pb-2 text-secondary border-b-1 border-gray-border">
                    コレクション一覧
                    {username === me?.name && (
                        <Button>
                            <i className="ti ti-plus mr-2" />
                            新規作成
                        </Button>
                    )}
                </h2>
                <ul className="flex flex-col">
                    {data.map((collection) => (
                        <li key={collection.id}>
                            <Link
                                to={`/user/${username}/collections/${collection.id}`}
                                className="p-2 block border-b-1 border-gray-border break-all hover:bg-neutral-100"
                            >
                                <div className="flex gap-2">
                                    <i className="ti ti-folder mt-1" />
                                    <div>{collection.title}</div>
                                </div>
                                <div className="flex gap-2 mt-1 items-baseline text-xs text-secondary">
                                    {collection.is_private ? (
                                        <>
                                            <i className="ti ti-lock ml-1" />
                                            非公開コレクション
                                        </>
                                    ) : (
                                        <>
                                            <i className="ti ti-lock-open ml-1" />
                                            公開コレクション
                                        </>
                                    )}
                                </div>
                            </Link>
                        </li>
                    ))}
                </ul>
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
