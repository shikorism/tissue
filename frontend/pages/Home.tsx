import React from 'react';
import { subSeconds, format } from 'date-fns';
import { useSuspenseQuery } from '@tanstack/react-query';
import { getInformationLatestQuery, getMeQuery } from '../api/query';
import { formatOrDefault, formatNumber, formatInterval } from '../lib/formatter';
import { Container } from '../components/Container';
import { categories } from '../features/info/categories';
import { cn } from '../lib/cn';

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
                                                - {format(info.created_at, 'M月d日')}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                            );
                        })}
                    </ul>
                </div>
            )}

            <Container>
                <div className="p-3 flex flex-col md:flex-row border-1 border-gray-border rounded">
                    <div className="flex-1 text-center md:text-start">
                        <h1 className="text-lg font-bold">現在のセッション</h1>
                        <p className="my-2 text-xl">
                            {formatOrDefault(me?.checkin_summary?.current_session_elapsed, formatInterval)}
                        </p>
                        <p className="text-sm">
                            {me
                                ? me.checkin_summary
                                    ? `${format(
                                          subSeconds(Date.now(), me.checkin_summary.current_session_elapsed),
                                          'yyyy/MM/dd HH:mm',
                                      )} にリセット`
                                    : '計測がまだ始まっていません'
                                : '\u{2015}'}
                        </p>
                    </div>
                    <table className="flex-1 text-sm mt-2 md:mt-0">
                        <tbody>
                            <tr>
                                <th className="pr-2 py-1 text-right w-2/5 after:content-[':']">通算回数</th>
                                <td>{formatOrDefault(me?.checkin_summary?.total_checkins, formatNumber)}回</td>
                            </tr>
                            <tr>
                                <th className="pr-2 py-1 text-right after:content-[':']">平均記録</th>
                                <td>{formatOrDefault(me?.checkin_summary?.average_interval, formatInterval)}</td>
                            </tr>
                            <tr>
                                <th className="pr-2 py-1 text-right after:content-[':']">中央値</th>
                                <td>{formatOrDefault(me?.checkin_summary?.median_interval, formatInterval)}</td>
                            </tr>
                        </tbody>
                    </table>
                    <table className="flex-1 text-sm">
                        <tbody>
                            <tr>
                                <th className="pr-2 py-1 text-right w-2/5 after:content-[':']">最長記録</th>
                                <td>{formatOrDefault(me?.checkin_summary?.longest_interval, formatInterval)}</td>
                            </tr>
                            <tr>
                                <th className="pr-2 py-1 text-right after:content-[':']">最短記録</th>
                                <td>{formatOrDefault(me?.checkin_summary?.shortest_interval, formatInterval)}</td>
                            </tr>
                            <tr>
                                <th className="pr-2 py-1 text-right after:content-[':']">合計時間</th>
                                <td>{formatOrDefault(me?.checkin_summary?.total_times, formatInterval)}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </Container>
        </>
    );
};

export default Home;
