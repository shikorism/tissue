import React from 'react';
import { Bar } from 'react-chartjs-2';
import { barChartDatasetDefaults, barChartDatasetDefaults2, barChartOptions } from './chart';

type Stats = { hour: number; count: number }[];

interface Props {
    hourlyStats: Stats;
    compareHourlyStats?: Stats;
}

export const HourlyChart: React.FC<Props> = ({ hourlyStats, compareHourlyStats }) => {
    const datasets = [{ ...barChartDatasetDefaults, data: summarize(hourlyStats) }];
    if (compareHourlyStats) {
        datasets.push({ ...barChartDatasetDefaults2, data: summarize(compareHourlyStats) });
    }

    return (
        <Bar
            data={{
                labels: Object.keys(new Array(24).fill(0)),
                datasets,
            }}
            options={barChartOptions}
        />
    );
};

const summarize = (stats: Stats): number[] => {
    const sum: number[] = new Array(24).fill(0);
    for (const hourly of stats) {
        sum[hourly.hour] += hourly.count;
    }
    return sum;
};
