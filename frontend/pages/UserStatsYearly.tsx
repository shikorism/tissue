import React from 'react';
import { useSuspenseQueries, useSuspenseQuery } from '@tanstack/react-query';
import { useLoaderData, useParams } from 'react-router';
import { getUserStatsCheckinDailyQuery, getUserStatsCheckinHourlyQuery, getUserStatsTagsQuery } from '../api/query';
import { LoaderData } from './UserStatsYearly.loader';
import { MonthlyChart } from '../features/user-stats/MonthlyChart';
import { HourlyChart } from '../features/user-stats/HourlyChart';
import { DayOfWeekChart } from '../features/user-stats/DayOfWeekChart';
import { TagRanking } from '../features/user-stats/TagRanking';
import { Pill } from '../components/Pill';
import { CheckinHeatmap } from '../features/user-stats/CheckinHeatmap';

export const UserStatsYearly: React.FC = () => {
    const { year } = useParams();
    const { username, query, prevQuery } = useLoaderData<LoaderData>();
    const [{ data: dailyData }, { data: prevDailyData } = { data: undefined }] = useSuspenseQueries({
        queries: [query, prevQuery].filter((q) => q).map((q) => getUserStatsCheckinDailyQuery(username, q)),
    });
    const [{ data: hourlyData }, { data: prevHourlyData } = { data: undefined }] = useSuspenseQueries({
        queries: [query, prevQuery].filter((q) => q).map((q) => getUserStatsCheckinHourlyQuery(username, q)),
    });
    const { data: mostlyUsedTags } = useSuspenseQuery(getUserStatsTagsQuery(username, query));
    const { data: mostlyUsedTagsIncludesMeta } = useSuspenseQuery(
        getUserStatsTagsQuery(username, { ...query, includes_metadata: true }),
    );

    return (
        <div className="px-4 lg:max-w-[850px]">
            <div className="mt-2 pb-2 text-secondary border-b-1 border-gray-border">{year}年の統計</div>
            <div className="flex flex-col py-4 *:not-first:mt-4 *:not-first:pt-4 *:not-first:border-t-1 *:not-first:border-gray-border">
                <div>
                    <h2 className="text-xl font-bold mb-4">アクティビティ</h2>
                    <div className="overflow-x-auto">
                        <CheckinHeatmap startDate={query.since} data={dailyData} />
                    </div>
                </div>
                <div>
                    <h2 className="text-xl font-bold mb-4">月間チェックイン回数</h2>
                    <MonthlyChart year={year!} dailyStats={dailyData} compareDailyStats={prevDailyData} />
                </div>
                <div>
                    <h2 className="text-xl font-bold mb-4">時間別チェックイン回数</h2>
                    <HourlyChart hourlyStats={hourlyData} compareHourlyStats={prevHourlyData} />
                </div>
                <div>
                    <h2 className="text-xl font-bold mb-4">曜日別チェックイン回数</h2>
                    <DayOfWeekChart dailyStats={dailyData} compareDailyStats={prevDailyData} />
                </div>
                <div>
                    <h2 className="text-xl font-bold mb-4">最も使用したタグ</h2>
                    <div className="flex flex-col md:flex-row gap-4">
                        <div className="flex-1">
                            <h3 className="text-center mb-2">
                                <Pill className="text-sm text-white bg-primary">チェックインタグ</Pill>
                            </h3>
                            <p className="text-center text-secondary text-sm mb-2">
                                チェックインに追加したタグの集計です。
                            </p>
                            <TagRanking className="w-full" tags={mostlyUsedTags} />
                        </div>
                        <div className="flex-1">
                            <h3 className="text-center mb-2">
                                <Pill className="text-sm text-white bg-primary">チェックインタグ</Pill>
                                <span className="mx-2">+</span>
                                <Pill className="text-sm text-white bg-secondary">オカズタグ</Pill>
                            </h3>
                            <p className="text-center text-secondary text-sm mb-2">
                                オカズ自体のタグも含めた集計です。
                            </p>
                            <TagRanking className="w-full" tags={mostlyUsedTagsIncludesMeta} />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default UserStatsYearly;
