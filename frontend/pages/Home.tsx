import React from 'react';
import { subSeconds, format } from 'date-fns';
import { GlobalNavigation } from '../components/GlobalNavigation';
import { $api } from '../api/client';
import { formatOrDefault, formatNumber, formatInterval } from '../lib/formatter';

export const Home: React.FC = () => {
    const { data: me } = $api.useQuery('get', '/me', undefined, { refetchOnMount: 'always' });

    return (
        <>
            <GlobalNavigation />
            <div className="ml-(--global-nav-width) p-4">
                <div className="p-3 max-w-[1000px] flex bg-gray-back rounded">
                    <div className="flex-1">
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
                    <table className="flex-1 text-sm">
                        <tbody>
                            <tr>
                                <th className="pr-2 text-right w-2/5 after:content-[':']">通算回数</th>
                                <td>{formatOrDefault(me?.checkin_summary?.total_checkins, formatNumber)}回</td>
                            </tr>
                            <tr>
                                <th className="pr-2 text-right after:content-[':']">平均記録</th>
                                <td>{formatOrDefault(me?.checkin_summary?.average_interval, formatInterval)}</td>
                            </tr>
                            <tr>
                                <th className="pr-2 text-right after:content-[':']">中央値</th>
                                <td>{formatOrDefault(me?.checkin_summary?.median_interval, formatInterval)}</td>
                            </tr>
                        </tbody>
                    </table>
                    <table className="flex-1 text-sm">
                        <tbody>
                            <tr>
                                <th className="pr-2 text-right w-2/5 after:content-[':']">最長記録</th>
                                <td>{formatOrDefault(me?.checkin_summary?.longest_interval, formatInterval)}</td>
                            </tr>
                            <tr>
                                <th className="pr-2 text-right after:content-[':']">最短記録</th>
                                <td>{formatOrDefault(me?.checkin_summary?.shortest_interval, formatInterval)}</td>
                            </tr>
                            <tr>
                                <th className="pr-2 text-right after:content-[':']">合計時間</th>
                                <td>{formatOrDefault(me?.checkin_summary?.total_times, formatInterval)}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div className="mt-4">
                    <h1 className="text-xl">お惣菜コーナー</h1>
                    <p className="mt-2 text-sm text-secondary">
                        最近の公開チェックインから、オカズリンク付きのものを表示しています。
                    </p>
                </div>
            </div>
        </>
    );
};
