import React from 'react';
import { Link, useLoaderData, useNavigate, useRouteError } from 'react-router';
import { LoaderData } from './CheckinDetail.loader';
import { useSuspenseQuery } from '@tanstack/react-query';
import { getCheckinQuery } from '../api/query';
import { ResponseError } from '../api/errors';
import { Checkin } from '../features/checkins/Checkin';
import { NotFound } from './NotFound';

export const CheckinDetail: React.FC = () => {
    const navigate = useNavigate();
    const { id } = useLoaderData<LoaderData>();
    const { data: checkin } = useSuspenseQuery(getCheckinQuery(id));

    return (
        <div>
            <div className="p-4 flex flex-col gap-2 border-b-1 border-gray-border">
                <div className="flex items-end gap-1">
                    <img
                        className="rounded inline-block mr-1"
                        src={checkin.user.profile_image_url}
                        alt={`${checkin.user.display_name}'s Avatar`}
                        width={48}
                        height={48}
                    />
                    <div className="flex flex-col overflow-hidden truncate">
                        <div className="text-lg font-medium">
                            <Link to={`/user/${checkin.user.name}`} className="hover:underline">
                                {checkin.user.display_name}
                            </Link>
                        </div>
                        <div className="text-xs text-secondary">
                            <Link to={`/user/${checkin.user.name}`} className="hover:underline">
                                @{checkin.user.name}
                            </Link>
                            {checkin.user.is_protected && <i className="ti ti-lock text-muted ml-0.5" />}
                        </div>
                    </div>
                </div>
            </div>
            <Checkin
                className="p-4"
                checkin={checkin}
                intervalStyle="relative"
                showSource
                showActions
                onDelete={() => {
                    navigate(`/user/${checkin.user.name}`);
                }}
            />
        </div>
    );
};

export const ErrorBoundary: React.FC = () => {
    const error = useRouteError();

    if (error instanceof ResponseError) {
        if (error.response.status === 403) {
            const message = error.error?.message || 'このユーザはチェックイン履歴を公開していません。';
            return <div className="p-4">{message}</div>;
        }
        if (error.response.status === 404) {
            return <NotFound />;
        }
    }

    throw error;
};

export default CheckinDetail;
