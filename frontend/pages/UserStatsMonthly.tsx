import React from 'react';
import { useLoaderData, useParams } from 'react-router';
import { useSuspenseQuery } from '@tanstack/react-query';
import { LoaderData } from './UserStatsMonthly.loader';
import { getUserStatsCheckinDailyQuery, getUserStatsCheckinHourlyQuery, getUserStatsTagsQuery } from '../api/query';
import { HourlyChart } from '../features/user-stats/HourlyChart';
import { DayOfWeekChart } from '../features/user-stats/DayOfWeekChart';
import { TagRanking } from '../features/user-stats/TagRanking';
import { Pill } from '../components/Pill';

export const UserStatsMonthly = () => {
    const { year, month } = useParams();
    const { username, query } = useLoaderData<LoaderData>();
    const { data: dailyData } = useSuspenseQuery(getUserStatsCheckinDailyQuery(username, query));
    const { data: hourlyData } = useSuspenseQuery(getUserStatsCheckinHourlyQuery(username, query));
    const { data: mostlyUsedTags } = useSuspenseQuery(getUserStatsTagsQuery(username, query));
    const { data: mostlyUsedTagsIncludesMeta } = useSuspenseQuery(
        getUserStatsTagsQuery(username, { ...query, includes_metadata: true }),
    );

    return (
        <div className="px-4 lg:max-w-[800px]">
            <div className="mt-2 pb-2 text-secondary border-b-1 border-gray-border">
                {year}年{month}月の統計
            </div>
            <div className="flex flex-col py-4 *:not-first:mt-4 *:not-first:pt-4 *:not-first:border-t-1 *:not-first:border-gray-border">
                <div>
                    <h2 className="text-xl font-bold mb-4">時間別チェックイン回数</h2>
                    <HourlyChart hourlyStats={hourlyData} />
                </div>
                <div>
                    <h2 className="text-xl font-bold mb-4">曜日別チェックイン回数</h2>
                    <DayOfWeekChart dailyStats={dailyData} />
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

export default UserStatsMonthly;
