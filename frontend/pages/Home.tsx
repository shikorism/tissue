import React from 'react';
import { subSeconds, format } from 'date-fns';
import { Link } from 'react-router';
import { useSuspenseQuery } from '@tanstack/react-query';
import { getMeQuery, getTimelinesPublicQuery } from '../api/query';
import { Checkin } from '../components/Checkin';
import { formatOrDefault, formatNumber, formatInterval } from '../lib/formatter';

export const Home: React.FC = () => {
    const { data: me } = useSuspenseQuery(getMeQuery());
    const { data: timeline } = useSuspenseQuery(getTimelinesPublicQuery({ per_page: 24 }));

    return (
        <div className="p-4">
            <div className="p-3 max-w-[1000px] flex flex-col md:flex-row bg-gray-back rounded">
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

            <div className="mt-4">
                <h1 className="text-xl">お惣菜コーナー</h1>
                <p className="mt-2 mb-2 text-sm text-secondary">
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
                {timeline && (timeline.totalCount || 0) > (timeline.data.length || 0) && (
                    <Link to="/timeline/public?page=2" className="group">
                        <div className="p-3 border-t-1 border-t-gray-border text-right">
                            <span className="text-primary group-hover:brightness-80 group-hover:underline">
                                もっと見る &raquo;
                            </span>
                        </div>
                    </Link>
                )}
            </div>
        </div>
    );
};
