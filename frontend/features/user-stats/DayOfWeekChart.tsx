import React from 'react';
import { Bar } from 'react-chartjs-2';
import { barChartDatasetDefaults, barChartDatasetDefaults2, barChartOptions } from './chart';

type Stats = { date: string; count: number }[];

interface Props {
    dailyStats: Stats;
    compareDailyStats?: Stats;
}

export const DayOfWeekChart: React.FC<Props> = ({ dailyStats, compareDailyStats }) => {
    const datasets = [{ ...barChartDatasetDefaults, data: summarize(dailyStats) }];
    if (compareDailyStats) {
        datasets.push({ ...barChartDatasetDefaults2, data: summarize(compareDailyStats) });
    }

    return (
        <Bar
            data={{
                labels: ['日', '月', '火', '水', '木', '金', '土'],
                datasets,
            }}
            options={barChartOptions}
        />
    );
};

const summarize = (dailyStats: Stats) => {
    const dowSum: number[] = new Array(7).fill(0);
    for (const daily of dailyStats) {
        const dow = new Date(`${daily.date}T00:00:00+09:00`).getDay();
        dowSum[dow] += daily.count;
    }
    return dowSum;
};
