import React from 'react';
import { useSuspenseQueries, useSuspenseQuery } from '@tanstack/react-query';
import { Link, useLoaderData, useParams } from 'react-router';
import {
    getUserStatsCheckinDailyQuery,
    getUserStatsCheckinHourlyQuery,
    getUserStatsLinksQuery,
    getUserStatsTagsQuery,
} from '../api/query';
import { LoaderData } from './UserStatsYearly.loader';
import { MonthlyChart } from '../features/user-stats/MonthlyChart';
import { HourlyChart } from '../features/user-stats/HourlyChart';
import { DayOfWeekChart } from '../features/user-stats/DayOfWeekChart';
import { TagRanking } from '../features/user-stats/TagRanking';
import { Pill } from '../components/ui/Pill';
import { CheckinHeatmap } from '../features/user-stats/CheckinHeatmap';
import { ColumnHeader } from '../components/ColumnHeader';
import { LinkCard } from '../components/LinkCard';
import { ExternalLink } from '../components/ui/ExternalLink';

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
    const { data: mostlyUsedLinks } = useSuspenseQuery(getUserStatsLinksQuery(username, query));

    return (
        <div className="px-4 lg:w-[480px] xl:w-[740px]">
            <ColumnHeader>{year}年の統計</ColumnHeader>
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
                <div className="@container">
                    <h2 className="text-xl font-bold mb-4">最も使用したタグ</h2>
                    <div className="flex flex-col @lg:flex-row gap-4">
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
                <div>
                    <h2 className="text-xl font-bold mb-2">最も使ったオカズ</h2>
                    <p className="text-secondary text-sm mb-4">2回以上使用したオカズのみ集計しています。</p>
                    {mostlyUsedLinks.length > 0 ? (
                        <ul>
                            {mostlyUsedLinks.map((item, index) => (
                                <li key={item.link} className="flex flex-col gap-2 border-b border-gray-border py-3">
                                    <p>
                                        <span className="inline-block min-w-13 text-center px-3 py-1 rounded-lg bg-primary text-white text-2xl font-bold mr-3">
                                            {index + 1}
                                        </span>
                                        <span className="text-2xl font-bold mr-1">{item.count}</span>回
                                    </p>
                                    <LinkCard link={item.link} />
                                    <div className="flex items-baseline">
                                        <i className="ti ti-link mr-1" />
                                        <ExternalLink className="overflow-hidden" href={item.link}>
                                            {item.link}
                                        </ExternalLink>
                                    </div>
                                    <div className="flex">
                                        <Link
                                            to={{
                                                pathname: '/checkin',
                                                search: `?link=${encodeURIComponent(item.link)}`,
                                            }}
                                            className="px-4 py-2 text-xl text-secondary rounded outline-2 outline-primary/0 focus:outline-primary/40 active:outline-primary/40 cursor-pointer"
                                            title="同じオカズでチェックイン"
                                        >
                                            <i className="ti ti-reload" />
                                        </Link>
                                    </div>
                                </li>
                            ))}
                        </ul>
                    ) : (
                        <p className="text-secondary">
                            この期間のチェックインが無いか、2回以上使用したオカズがありません。
                        </p>
                    )}
                </div>
            </div>
        </div>
    );
};

export default UserStatsYearly;
