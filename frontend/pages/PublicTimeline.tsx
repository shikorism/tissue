import React from 'react';
import { useSearchParams } from 'react-router';
import { useGetTimelinesPublic } from '../api/hooks';
import { Checkin } from '../components/Checkin';
import { Pagination } from '../components/Pagination';
import { useScrollToTop } from '../hooks/useScrollToTop';

const PER_PAGE = 20;

export const PublicTimeline: React.FC = () => {
    const [searchParams] = useSearchParams();
    const page = parseInt(searchParams.get('page') ?? '1', 10);
    const { data: timeline } = useGetTimelinesPublic({ page, per_page: PER_PAGE }, true);
    useScrollToTop([page]);

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
