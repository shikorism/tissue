import React, { useState, useEffect } from 'react';
import { startOfMonth, endOfMonth, getDaysInMonth, addDays, formatDate, isSameDay, addMonths } from 'date-fns';
import { Link, useLoaderData, useParams, useSearchParams } from 'react-router';
import { useQuery, useSuspenseQuery } from '@tanstack/react-query';
import { getUserCheckinsQuery, getUserStatsCheckinDailyQuery } from '../api/query';
import { LoaderData, PER_PAGE } from './UserCheckins.loader';
import { Checkin } from '../features/checkins/Checkin';
import { Pagination } from '../components/ui/Pagination';
import { cn } from '../lib/cn';
import { Container } from '../components/Container';
import { ColumnHeader } from '../components/ColumnHeader';
import { Checkbox } from '../components/ui/Checkbox';
import { TZDate } from '@date-fns/tz';
import { SERVER_TZ } from '../lib/time';

export const UserCheckins: React.FC = () => {
    const params = useParams();
    const [searchParams, setSearchParams] = useSearchParams();
    const { username, checkinsQuery } = useLoaderData<LoaderData>();
    const {
        data: { data, totalCount },
    } = useSuspenseQuery(getUserCheckinsQuery(username, checkinsQuery));

    let currentDate: TZDate | undefined;
    if (params.year && params.month && params.date) {
        currentDate = new TZDate(
            parseInt(params.year, 10),
            parseInt(params.month, 10) - 1,
            parseInt(params.date, 10),
            SERVER_TZ,
        );
    } else if (params.year && params.month) {
        currentDate = new TZDate(parseInt(params.year, 10), parseInt(params.month, 10) - 1, 1, SERVER_TZ);
    } else if (params.year) {
        currentDate = new TZDate(parseInt(params.year, 10), 0, 1, SERVER_TZ);
    }

    return (
        <Container size="md" className="p-0 flex flex-col lg:flex-row lg:mx-auto grow-1">
            <div className="p-4 pt-0 pb-8 lg:w-[280px] border-b-1 lg:border-b-0 lg:border-r-1 border-gray-border">
                <ColumnHeader className="mb-4">検索条件</ColumnHeader>
                <Calendar initialDate={currentDate} />
                <div className="mt-2">
                    <Checkbox
                        checked={searchParams.get('link') === '1'}
                        onChange={() =>
                            setSearchParams((prev) => {
                                prev.set('link', searchParams.get('link') === '1' ? '0' : '1');
                                return prev;
                            })
                        }
                    >
                        オカズ付きのみ
                    </Checkbox>
                </div>
            </div>
            <div className="flex-1 px-4">
                <ColumnHeader className="flex gap-2">
                    <div>
                        <Link
                            to={{
                                pathname: `/user/${username}/checkins`,
                                search: searchParams.toString(),
                            }}
                            className="hover:brightness-80 hover:underline"
                        >
                            チェックイン
                        </Link>
                    </div>
                    {params.year && (
                        <div>
                            <i className="ti ti-chevron-right mr-2" />
                            <Link
                                to={{
                                    pathname: `/user/${username}/checkins/${params.year}`,
                                    search: searchParams.toString(),
                                }}
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
                                to={{
                                    pathname: `/user/${username}/checkins/${params.year}/${params.month}`,
                                    search: searchParams.toString(),
                                }}
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
                                to={{
                                    pathname: `/user/${username}/checkins/${params.year}/${params.month}/${params.date}`,
                                    search: searchParams.toString(),
                                }}
                                className="hover:brightness-80 hover:underline"
                            >
                                {params.date}日
                            </Link>
                        </div>
                    )}
                </ColumnHeader>
                {data?.map((checkin) => (
                    <Checkin
                        key={checkin.id}
                        checkin={checkin}
                        className="border-b-1 border-gray-border"
                        intervalStyle="relative"
                        showActions
                    />
                ))}
                {totalCount ? (
                    <Pagination className="my-4" totalCount={totalCount} perPage={PER_PAGE} />
                ) : (
                    <div className="py-4">チェックインがありません。</div>
                )}
            </div>
        </Container>
    );
};

interface CalendarParams {
    initialDate?: TZDate;
}

const Calendar: React.FC<CalendarParams> = ({ initialDate }) => {
    const [searchParams] = useSearchParams();
    const { username } = useLoaderData<LoaderData>();
    const [currentDate, setCurrentDate] = useState(initialDate || TZDate.tz(SERVER_TZ));

    useEffect(() => {
        setCurrentDate(initialDate || TZDate.tz(SERVER_TZ));
    }, [initialDate?.getTime()]);

    const { data: countByDate } = useQuery({
        ...getUserStatsCheckinDailyQuery(username, {
            since: formatDate(startOfMonth(currentDate), 'yyyy-MM-dd'),
            until: formatDate(endOfMonth(currentDate), 'yyyy-MM-dd'),
        }),
        select: (data) => new Map(data.map((d) => [d.date, d.count])),
    });

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
                to={{
                    pathname: `/user/${username}/checkins/${date.getFullYear()}/${date.getMonth() + 1}/${date.getDate()}`,
                    search: searchParams.toString(),
                }}
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
                    className="flex-1 max-w-[48px] aspect-square rounded hover:bg-neutral-100"
                    onClick={() => setCurrentDate(addMonths(currentDate, -1))}
                >
                    <i className="ti ti-caret-left-filled text-lg" />
                </button>
                <div className="flex-4 text-center">
                    <Link
                        to={{
                            pathname: `/user/${username}/checkins/${currentDate.getFullYear()}/${currentDate.getMonth() + 1}`,
                            search: searchParams.toString(),
                        }}
                        className="hover:underline"
                    >
                        {formatDate(startOfMon, 'yyyy年M月')}
                    </Link>
                </div>
                <button
                    className="flex-1 max-w-[48px] aspect-square rounded hover:bg-neutral-100"
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

export default UserCheckins;
