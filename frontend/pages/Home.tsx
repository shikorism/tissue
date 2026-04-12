import React from 'react';
import { Link, useLoaderData } from 'react-router';
import { subSeconds, format } from 'date-fns';
import { useSuspenseQuery } from '@tanstack/react-query';
import {
    getInformationLatestQuery,
    getMeQuery,
    getStatsCheckinDailyQuery,
    getUserCheckinsQuery,
    getUserStatsCheckinDailyQuery,
} from '../api/query';
import { formatOrDefault, formatNumber, formatInterval } from '../lib/formatter';
import { Container } from '../components/Container';
import { categories } from '../features/info/categories';
import { cn } from '../lib/cn';
import { LoaderData } from './Home.loader';
import type { components } from '../api/schema';
import { CheckinHeatmap } from '../features/user-stats/CheckinHeatmap';
import { Checkin } from '../features/checkins/Checkin';
import { Bar } from 'react-chartjs-2';

import { BarController, BarElement, CategoryScale, Chart, LinearScale, Tooltip } from 'chart.js';
import { serverTz } from '../lib/time';

Chart.register([BarController, BarElement, CategoryScale, LinearScale, Tooltip]);

export const Home: React.FC = () => {
    const { data: me } = useSuspenseQuery(getMeQuery());
    const { data: information } = useSuspenseQuery(getInformationLatestQuery());

    return (
        <>
            {information.length > 0 && (
                <div className="pt-2 px-4 flex gap-2 border-b-1 border-gray-border">
                    <div className="shrink-0">
                        <i className="ti ti-info-circle" />
                    </div>
                    <ul className="grow text-sm/6">
                        {information.map((info) => {
                            const category = categories[info.category];
                            return (
                                <li key={info.id} className="mb-2">
                                    <a href={`/info/${info.id}`} className="group">
                                        <span
                                            className={cn(
                                                'mr-1 p-1 text-2xs font-bold rounded bg-gray-back',
                                                category?.className,
                                            )}
                                        >
                                            {category?.label}
                                        </span>
                                        <span className="group-hover:brightness-80 group-hover:underline">
                                            <span className="text-primary">{info.title}</span>
                                            <span className="text-2xs text-secondary">
                                                {' '}
                                                - {format(info.created_at, 'M月d日', { in: serverTz })}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                            );
                        })}
                    </ul>
                </div>
            )}

            {me && (
                <Container className="flex flex-col gap-4">
                    <h2 className="text-xl font-bold">アクティビティ</h2>
                    <CurrentSession user={me} />
                    <RecentActivity user={me} />
                    <GlobalStats />
                    <RecentCheckin user={me} />
                </Container>
            )}
        </>
    );
};

interface CurrentSessionProps {
    user: components['schemas']['User'];
}

const CurrentSession: React.FC<CurrentSessionProps> = ({ user }) => {
    return (
        <div className="p-3 flex flex-col md:flex-row border-1 border-gray-border rounded">
            <div className="flex-1 text-start">
                <h3 className="text-lg font-bold">現在のセッション</h3>
                <p className="my-2 text-xl">
                    {formatOrDefault(user.checkin_summary?.current_session_elapsed, formatInterval)}
                </p>
                <p className="text-sm">
                    {user.checkin_summary
                        ? `${format(
                              subSeconds(Date.now(), user.checkin_summary.current_session_elapsed),
                              'yyyy/MM/dd HH:mm',
                              { in: serverTz },
                          )} にリセット`
                        : '計測がまだ始まっていません'}
                </p>
            </div>
            <table className="flex-1 text-sm mt-4 md:mt-0 self-start md:self-auto">
                <tbody>
                    <tr>
                        <th className="pr-2 py-1 text-right md:w-2/5 after:content-[':']">通算回数</th>
                        <td>{formatOrDefault(user.checkin_summary?.total_checkins, formatNumber)}回</td>
                    </tr>
                    <tr>
                        <th className="pr-2 py-1 text-right after:content-[':']">平均記録</th>
                        <td>{formatOrDefault(user.checkin_summary?.average_interval, formatInterval)}</td>
                    </tr>
                    <tr>
                        <th className="pr-2 py-1 text-right after:content-[':']">中央値</th>
                        <td>{formatOrDefault(user.checkin_summary?.median_interval, formatInterval)}</td>
                    </tr>
                </tbody>
            </table>
            <table className="flex-1 text-sm self-start md:self-auto">
                <tbody>
                    <tr>
                        <th className="pr-2 py-1 text-right md:w-2/5 after:content-[':']">最長記録</th>
                        <td>{formatOrDefault(user.checkin_summary?.longest_interval, formatInterval)}</td>
                    </tr>
                    <tr>
                        <th className="pr-2 py-1 text-right after:content-[':']">最短記録</th>
                        <td>{formatOrDefault(user.checkin_summary?.shortest_interval, formatInterval)}</td>
                    </tr>
                    <tr>
                        <th className="pr-2 py-1 text-right after:content-[':']">合計時間</th>
                        <td>{formatOrDefault(user.checkin_summary?.total_times, formatInterval)}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    );
};

interface RecentActivityProps {
    user: components['schemas']['User'];
}

const RecentActivity: React.FC<RecentActivityProps> = ({ user }) => {
    const { statsCheckinDailyQuery } = useLoaderData<LoaderData>();
    const { data: checkinStats } = useSuspenseQuery(getUserStatsCheckinDailyQuery(user.name, statsCheckinDailyQuery));

    return (
        <div className="p-3 border-1 border-gray-border rounded">
            <div className="mb-2 flex items-baseline gap-4">
                <h3 className="text-lg font-bold">最近の活動</h3>
                <Link to={`/user/${user.name}/stats`} className="text-primary hover:brightness-80 hover:underline">
                    グラフを見る &raquo;
                </Link>
            </div>
            <div className="mt-2 overflow-x-auto">
                <CheckinHeatmap startDate={statsCheckinDailyQuery.since} data={checkinStats} />
            </div>
        </div>
    );
};

interface RecentCheckinProps {
    user: components['schemas']['User'];
}

const RecentCheckin: React.FC<RecentCheckinProps> = ({ user }) => {
    const { data: checkins, refetch } = useSuspenseQuery(getUserCheckinsQuery(user.name));
    if (checkins.data.length === 0) {
        return null;
    }

    return (
        <div className="p-3 border-1 border-gray-border rounded">
            <div className="flex items-baseline gap-4">
                <h3 className="text-lg font-bold">最新のチェックイン</h3>
                <Link to={`/user/${user.name}/checkins`} className="text-primary hover:brightness-80 hover:underline">
                    もっと見る &raquo;
                </Link>
            </div>
            <Checkin
                className="pt-2 pb-0"
                key={checkins.data[0].id}
                checkin={checkins.data[0]}
                intervalStyle="full"
                showActions
                onDelete={() => refetch()}
            />
        </div>
    );
};

const GlobalStats: React.FC = () => {
    const { data } = useSuspenseQuery(getStatsCheckinDailyQuery());
    const labels = data.map((d) => `${d.date.replaceAll('-', '/')} の総チェックイン数`);
    const values = data.map((d) => d.count);

    return (
        <div className="p-3 border-1 border-gray-border rounded">
            <h3 className="text-lg font-bold">みんなの活動</h3>
            <div className="border-b-1 border-gray-border h-[120px]">
                <Bar
                    data={{
                        labels,
                        datasets: [
                            {
                                data: values,
                                backgroundColor: 'rgba(0, 0, 0, .1)',
                                borderColor: 'rgba(0, 0, 0, .25)',
                                borderWidth: 1,
                            },
                        ],
                    }}
                    options={{
                        maintainAspectRatio: false,
                        elements: {
                            line: {},
                        },
                        scales: {
                            x: {
                                display: false,
                            },
                            y: {
                                display: false,
                                beginAtZero: true,
                            },
                        },
                        plugins: {
                            legend: {
                                display: false,
                            },
                        },
                    }}
                />
            </div>
        </div>
    );
};

export default Home;
