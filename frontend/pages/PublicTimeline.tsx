import React from 'react';
import { LoaderFunctionArgs, useLoaderData } from 'react-router';
import { QueryClient, useSuspenseQuery } from '@tanstack/react-query';
import { getTimelinesPublicQuery } from '../api/query';
import { Checkin } from '../components/Checkin';
import { Pagination } from '../components/Pagination';
import { useScrollToTop } from '../hooks/useScrollToTop';
import type { paths } from '../api/schema';

const PER_PAGE = 20;

interface LoaderData {
    query: paths['/timelines/public']['get']['parameters']['query'];
}

export const loader =
    (queryClient: QueryClient) =>
    async ({ request }: LoaderFunctionArgs) => {
        const url = new URL(request.url);
        const page = parseInt(url.searchParams.get('page') ?? '1', 10);
        const query = { page, per_page: PER_PAGE };

        await queryClient.ensureQueryData(getTimelinesPublicQuery(query));

        return { query } satisfies LoaderData;
    };

export const PublicTimeline: React.FC = () => {
    const { query } = useLoaderData<LoaderData>();
    const { data: timeline } = useSuspenseQuery(getTimelinesPublicQuery(query));
    useScrollToTop([query?.page]);

    return (
        <div className="p-4">
            <h1 className="text-3xl">お惣菜コーナー</h1>
            <p className="my-3 text-sm text-secondary">
                最近の公開チェックインから、オカズリンク付きのものを表示しています。
            </p>
            <div className="grid grid-cols-1 lg:grid-cols-2 2xl:grid-cols-3">
                {timeline?.data?.map((checkin) => (
                    <Checkin
                        key={checkin.id}
                        checkin={checkin}
                        className="px-2 border-t-1 border-gray-border"
                        showActions
                    />
                ))}
            </div>
            {timeline?.totalCount && (
                <Pagination className="mt-4" totalCount={timeline.totalCount} perPage={PER_PAGE} />
            )}
        </div>
    );
};
