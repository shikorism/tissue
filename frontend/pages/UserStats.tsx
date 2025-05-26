import React from 'react';
import { Outlet, useLoaderData, useNavigate, useParams } from 'react-router';
import { LoaderData } from './UserStats.loader';
import { useSuspenseQuery } from '@tanstack/react-query';
import { getUserStatsCheckinOldestQuery } from '../api/query';

export const UserStats: React.FC = () => {
    const navigate = useNavigate();
    const { year, month } = useParams();
    const { username } = useLoaderData<LoaderData>();
    const { data: oldestData } = useSuspenseQuery(getUserStatsCheckinOldestQuery(username));

    const years: (number | 'all')[] = ['all'];
    const months: (number | 'all')[] = ['all'];
    if (oldestData.oldest_checkin_date) {
        const [oldestYear, oldestMonth] = oldestData.oldest_checkin_date.split('-');
        const now = new Date();

        // 年セレクタの作成
        for (let y = parseInt(oldestYear, 10); y <= now.getFullYear(); y++) {
            years.push(y);
        }

        // 年が指定されている場合のみ、月セレクタの作成
        if (year) {
            const startMonth = year === oldestYear ? parseInt(oldestMonth, 10) : 1;
            const maxMonth = year === String(now.getFullYear()) ? now.getMonth() + 1 : 12;
            for (let m = startMonth; m <= maxMonth; m++) {
                months.push(m);
            }
        }
    }

    return (
        <div className="flex flex-col lg:flex-row grow-1">
            <div className="p-4 pt-0 lg:w-[280px] border-b-1 lg:border-b-0 lg:border-r-1 border-gray-border">
                <div className="flex mt-2 mb-4 pb-2 text-secondary border-b-1 border-gray-border">集計条件</div>
                <div className="flex lg:flex-col gap-4">
                    <div className="flex-1">
                        <label htmlFor="stats-year" className="text-secondary">
                            年
                        </label>
                        <select
                            id="stats-year"
                            className="w-full mt-2 p-2 rounded border-1 border-gray-border"
                            value={year || 'all'}
                            onChange={(e) => {
                                const v = e.target.value;
                                if (v === 'all') {
                                    navigate(`/user/${username}/stats`);
                                } else {
                                    navigate(`/user/${username}/stats/${v}`);
                                }
                            }}
                        >
                            {years.map((year) => (
                                <option key={year} value={year}>
                                    {year === 'all' ? '全期間' : `${year}年`}
                                </option>
                            ))}
                        </select>
                    </div>
                    <div className="flex-1">
                        <label htmlFor="stats-month" className="text-secondary">
                            月
                        </label>
                        <select
                            id="stats-month"
                            className="w-full mt-2 p-2 rounded border-1 border-gray-border disabled:text-secondary disabled:bg-neutral-100"
                            value={month || 'all'}
                            onChange={(e) => {
                                const v = e.target.value;
                                if (v === 'all') {
                                    navigate(`/user/${username}/stats/${year}`);
                                } else {
                                    navigate(`/user/${username}/stats/${year}/${v}`);
                                }
                            }}
                            disabled={!year}
                        >
                            {months.map((month) => (
                                <option key={month} value={month}>
                                    {month === 'all' ? '全期間' : `${month}月`}
                                </option>
                            ))}
                        </select>
                    </div>
                </div>
            </div>
            <div className="flex-1">
                <Outlet />
            </div>
        </div>
    );
};
