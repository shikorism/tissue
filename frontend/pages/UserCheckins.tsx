import React, { useState, useEffect } from 'react';
import { startOfMonth, endOfMonth, getDaysInMonth, addDays, formatDate, isSameDay, addMonths } from 'date-fns';
import { Link, useLoaderData, useParams } from 'react-router';
import { useQuery, useSuspenseQuery } from '@tanstack/react-query';
import { getUserCheckinsQuery, getUserStatsCheckinDailyQuery } from '../api/query';
import { LoaderData, PER_PAGE } from './UserCheckins.loader';
import { Checkin } from '../components/Checkin';
import { Pagination } from '../components/Pagination';
import { useScrollToTop } from '../hooks/useScrollToTop';
import { cn } from '../lib/cn';

export const UserCheckins: React.FC = () => {
    const params = useParams();
    const { username, checkinsQuery } = useLoaderData<LoaderData>();
    const {
        data: { data, totalCount },
    } = useSuspenseQuery(getUserCheckinsQuery(username, checkinsQuery));
    useScrollToTop([checkinsQuery?.page]);

    let currentDate: Date | undefined;
    if (params.year && params.month && params.date) {
        currentDate = new Date(parseInt(params.year, 10), parseInt(params.month, 10) - 1, parseInt(params.date, 10));
    } else if (params.year && params.month) {
        currentDate = new Date(parseInt(params.year, 10), parseInt(params.month, 10) - 1, 1);
    } else if (params.year) {
        currentDate = new Date(parseInt(params.year, 10), 0, 1);
    }

    return (
        <div className="flex grow-1">
            <div className="w-[280px] p-4 lg:border-r-1 border-gray-border">
                <Calendar initialDate={currentDate} />
            </div>
            <div className="flex-1 px-4">
                {(params.year || params.month || params.date) && (
                    <div className="flex gap-2 mt-2 pb-2 text-secondary border-b-1 border-gray-border">
                        {params.year && (
                            <div>
                                <Link
                                    to={`/user/${username}/checkins/${params.year}`}
                                    className="hover:brightness-80 hover:underline"
                                >
                                    {params.year}年
                                </Link>
                            </div>
                        )}
                        {params.month && (
                            <div>
                                <i className="ti ti-chevron-right mr-2" />
                                <Link
                                    to={`/user/${username}/checkins/${params.year}/${params.month}`}
                                    className="hover:brightness-80 hover:underline"
                                >
                                    {params.month}月
                                </Link>
                            </div>
                        )}
                        {params.date && (
                            <div>
                                <i className="ti ti-chevron-right mr-2" />
                                <Link
                                    to={`/user/${username}/checkins/${params.year}/${params.month}/${params.date}`}
                                    className="hover:brightness-80 hover:underline"
                                >
                                    {params.date}日
                                </Link>
                            </div>
                        )}
                    </div>
                )}
                {data?.map((checkin) => (
                    <Checkin key={checkin.id} checkin={checkin} className="border-b-1 border-gray-border" showActions />
                ))}
                {totalCount ? (
                    <Pagination className="my-4" totalCount={totalCount} perPage={PER_PAGE} />
                ) : (
                    <div className="py-4">チェックインがありません。</div>
                )}
            </div>
        </div>
    );
};

interface CalendarParams {
    initialDate?: Date;
}

const Calendar: React.FC<CalendarParams> = ({ initialDate }) => {
    const { username } = useLoaderData<LoaderData>();
    const [currentDate, setCurrentDate] = useState(initialDate || new Date());

    useEffect(() => {
        setCurrentDate(initialDate || new Date());
    }, [initialDate?.getTime()]);

    const { data: countByDate } = useQuery({
        ...getUserStatsCheckinDailyQuery(username, {
            since: formatDate(startOfMonth(currentDate), 'yyyy-MM-dd'),
            until: formatDate(endOfMonth(currentDate), 'yyyy-MM-dd'),
        }),
        select: (data) => new Map(data.map((d) => [d.date, d.count])),
    });

    // TODO: この実装だとクライアントのTZの影響を受ける。JST基準で描画したい。
    const cells: React.ReactNode[] = [];
    const startOfMon = startOfMonth(currentDate);
    const days = getDaysInMonth(startOfMon);
    const dayOfFirst = startOfMonth(startOfMon).getDay();
    for (let i = 0; i < dayOfFirst; i++) {
        cells.push(<div key={-i} />);
    }
    for (let forward = 0; forward < days; forward++) {
        const date = addDays(startOfMon, forward);
        const count = countByDate?.get(formatDate(date, 'yyyy-MM-dd')) || 0;
        const color = {
            0: 'bg-gray-back',
            1: 'bg-green-200',
            2: 'bg-green-300',
            3: 'bg-green-400',
        }[Math.min(3, count)];
        cells.push(
            <Link
                key={date.getDate()}
                to={`/user/${username}/checkins/${date.getFullYear()}/${date.getMonth() + 1}/${date.getDate()}`}
                title={`${formatDate(date, 'yyyy年M月d日')} (${count}回)`}
            >
                <div
                    className={cn(
                        'py-1.5 rounded border-2 border-transparent',
                        color,
                        initialDate && isSameDay(date, initialDate) && 'font-bold border-neutral-800',
                    )}
                >
                    {date.getDate()}
                </div>
            </Link>,
        );
    }

    return (
        <div>
            <div className="flex items-center gap-2">
                <button
                    className="flex-1 aspect-square rounded hover:bg-neutral-100"
                    onClick={() => setCurrentDate(addMonths(currentDate, -1))}
                >
                    <i className="ti ti-caret-left-filled text-lg" />
                </button>
                <div className="flex-4 text-center">
                    <Link
                        to={`/user/${username}/checkins/${currentDate.getFullYear()}/${currentDate.getMonth() + 1}`}
                        className="hover:underline"
                    >
                        {formatDate(startOfMon, 'yyyy年M月')}
                    </Link>
                </div>
                <button
                    className="flex-1 aspect-square rounded hover:bg-neutral-100"
                    onClick={() => setCurrentDate(addMonths(currentDate, 1))}
                >
                    <i className="ti ti-caret-right-filled text-lg" />
                </button>
            </div>
            <div className="mt-2 grid grid-cols-7 gap-px text-center text-sm">
                <div>日</div>
                <div>月</div>
                <div>火</div>
                <div>水</div>
                <div>木</div>
                <div>金</div>
                <div>土</div>
                {cells}
            </div>
        </div>
    );
};
