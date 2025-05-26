import React from 'react';
import { Line } from 'react-chartjs-2';
import { lineChartDatasetDefaults, lineChartDatasetDefaults2, lineChartOptions } from './chart';

type Stats = { date: string; count: number }[];

interface Props {
    year: string;
    dailyStats: Stats;
    compareDailyStats?: Stats;
}

export const MonthlyChart: React.FC<Props> = ({ year, dailyStats, compareDailyStats }) => {
    const labels = [];
    for (let month = 1; month <= 12; month++) {
        const ym = `${year}/${month < 10 ? `0${month}` : month}`;
        labels.push(ym);
    }

    const datasets = [{ ...lineChartDatasetDefaults, data: summarize(dailyStats) }];
    if (compareDailyStats) {
        datasets.push({ ...lineChartDatasetDefaults2, data: summarize(compareDailyStats) });
    }

    return (
        <Line
            data={{
                labels,
                datasets,
            }}
            options={lineChartOptions}
        />
    );
};

const summarize = (stats: Stats): number[] => {
    const sum: number[] = new Array(12).fill(0);
    for (const daily of stats) {
        const month = parseInt(daily.date.substring(5, 7), 10) - 1;
        sum[month] += daily.count;
    }

    return sum;
};
