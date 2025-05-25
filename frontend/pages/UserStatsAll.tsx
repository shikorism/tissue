import React from 'react';
import { useSuspenseQuery } from '@tanstack/react-query';
import { Link, useLoaderData } from 'react-router';
import { getUserStatsCheckinDailyQuery, getUserStatsCheckinHourlyQuery, getUserStatsTagsQuery } from '../api/query';
import { LoaderData } from './UserStatsAll.loader';
import {
    BarController,
    BarElement,
    CategoryScale,
    Chart,
    LineController,
    LineElement,
    LinearScale,
    PointElement,
    Filler,
    Tooltip,
} from 'chart.js';
import { Bar, Line } from 'react-chartjs-2';

Chart.register([
    LineController,
    LineElement,
    PointElement,
    CategoryScale,
    LinearScale,
    BarController,
    BarElement,
    Filler,
    Tooltip,
]);

const lineChartDatasetDefaults = {
    backgroundColor: 'rgba(255, 99, 132, 0.2)',
    fill: true,
    borderColor: 'rgba(255, 99, 132, 1)',
    borderWidth: 1,
} as const;

const lineChartOptions = {
    elements: {
        line: {
            tension: 0,
        },
    },
    scales: {
        y: {
            beginAtZero: true,
        },
    },
    plugins: {
        legend: {
            display: false,
        },
        tooltip: {
            mode: 'index',
            intersect: false,
        },
    },
} as const;

const barChartDatasetDefaults = {
    backgroundColor: 'rgba(255, 99, 132, 0.2)',
    borderColor: 'rgba(255, 99, 132, 1)',
    borderWidth: 1,
} as const;

const barChartOptions = {
    scales: {
        y: {
            beginAtZero: true,
        },
    },
    plugins: {
        legend: {
            display: false,
        },
        tooltip: {
            mode: 'index',
            intersect: false,
        },
    },
} as const;

export const UserStatsAll: React.FC = () => {
    const { username } = useLoaderData<LoaderData>();
    const { data: dailyData } = useSuspenseQuery(getUserStatsCheckinDailyQuery(username));
    const { data: hourlyData } = useSuspenseQuery(getUserStatsCheckinHourlyQuery(username));
    const { data: mostlyUsedTags } = useSuspenseQuery(getUserStatsTagsQuery(username));
    const { data: mostlyUsedTagsIncludesMeta } = useSuspenseQuery(
        getUserStatsTagsQuery(username, { includes_metadata: true }),
    );

    const yearlySum: Record<string, number> = {};
    const dowSum: number[] = new Array(7).fill(0);
    for (const daily of dailyData) {
        const year = daily.date.split('-')[0];
        yearlySum[year] ||= 0;
        yearlySum[year] += daily.count;

        const dow = new Date(`${daily.date}T00:00:00+09:00`).getDay();
        dowSum[dow] += daily.count;
    }

    const hourlySum: number[] = new Array(24).fill(0);
    for (const hourly of hourlyData) {
        hourlySum[hourly.hour] += hourly.count;
    }

    return (
        <div className="px-4 lg:max-w-[800px]">
            <div className="flex mt-2 pb-2 text-secondary border-b-1 border-gray-border">全期間の統計</div>
            <div className="flex flex-col py-4 *:not-first:mt-4 *:not-first:pt-4 *:not-first:border-t-1 *:not-first:border-gray-border">
                <div>
                    <h2 className="text-xl font-bold mb-4">年間チェックイン回数</h2>
                    <Line
                        data={{
                            labels: Object.keys(yearlySum),
                            datasets: [{ ...lineChartDatasetDefaults, data: Object.values(yearlySum) }],
                        }}
                        options={lineChartOptions}
                    />
                </div>
                <div>
                    <h2 className="text-xl font-bold mb-4">時間別チェックイン回数</h2>
                    <Bar
                        data={{
                            labels: Object.keys(hourlySum),
                            datasets: [{ ...barChartDatasetDefaults, data: hourlySum }],
                        }}
                        options={barChartOptions}
                    />
                </div>
                <div>
                    <h2 className="text-xl font-bold mb-4">曜日別チェックイン回数</h2>
                    <Bar
                        data={{
                            labels: ['日', '月', '火', '水', '木', '金', '土'],
                            datasets: [{ ...barChartDatasetDefaults, data: dowSum }],
                        }}
                        options={barChartOptions}
                    />
                </div>
                <div>
                    <h2 className="text-xl font-bold mb-4">最も使用したタグ</h2>
                    <div className="flex flex-col md:flex-row gap-4">
                        <div className="flex-1">
                            <h3 className="text-center mb-2">
                                <span className="px-3 py-1 bg-primary text-sm text-white whitespace-nowrap align-baseline rounded-full">
                                    チェックインタグ
                                </span>
                            </h3>
                            <p className="text-center text-secondary text-sm mb-2">
                                チェックインに追加したタグの集計です。
                            </p>
                            <table className="w-full">
                                <tbody>
                                    {mostlyUsedTags.map((tag) => (
                                        <tr key={tag.name} className="border-1 border-gray-border odd:bg-gray-back">
                                            <td>
                                                <Link
                                                    to={{ pathname: `/search`, search: `?q=${tag.name}` }}
                                                    className="block px-4 py-3 break-all group"
                                                >
                                                    <i className="ti ti-tag text-secondary mr-2"></i>
                                                    <span className="group-hover:underline">{tag.name}</span>
                                                </Link>
                                            </td>
                                            <td>
                                                <Link
                                                    to={{ pathname: `/search`, search: `?q=${tag.name}` }}
                                                    className="block px-4 py-3 text-end"
                                                >
                                                    {tag.count}
                                                </Link>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                        <div className="flex-1">
                            <h3 className="text-center mb-2">
                                <span className="px-3 py-1 bg-primary text-sm text-white whitespace-nowrap align-baseline rounded-full">
                                    チェックインタグ
                                </span>
                                <span className="mx-2">+</span>
                                <span className="px-3 py-1 bg-secondary text-sm text-white whitespace-nowrap align-baseline rounded-full">
                                    オカズタグ
                                </span>
                            </h3>
                            <p className="text-center text-secondary text-sm mb-2">
                                オカズ自体のタグも含めた集計です。
                            </p>
                            <table className="w-full">
                                <tbody>
                                    {mostlyUsedTagsIncludesMeta.map((tag) => (
                                        <tr key={tag.name} className="border-1 border-gray-border odd:bg-gray-back">
                                            <td>
                                                <Link
                                                    to={{ pathname: `/search`, search: `?q=${tag.name}` }}
                                                    className="block px-4 py-3 break-all group"
                                                >
                                                    <i className="ti ti-tag text-secondary mr-2"></i>
                                                    <span className="group-hover:underline">{tag.name}</span>
                                                </Link>
                                            </td>
                                            <td>
                                                <Link
                                                    to={{ pathname: `/search`, search: `?q=${tag.name}` }}
                                                    className="block px-4 py-3 text-end"
                                                >
                                                    {tag.count}
                                                </Link>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};
