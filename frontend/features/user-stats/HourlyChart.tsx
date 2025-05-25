import React from 'react';
import { Bar } from 'react-chartjs-2';
import { barChartDatasetDefaults, barChartOptions } from './chart';

interface Props {
    hourlyStats: { hour: number; count: number }[];
}

export const HourlyChart: React.FC<Props> = ({ hourlyStats }) => {
    const hourlySum: number[] = new Array(24).fill(0);
    for (const hourly of hourlyStats) {
        hourlySum[hourly.hour] += hourly.count;
    }

    return (
        <Bar
            data={{
                labels: Object.keys(hourlySum),
                datasets: [{ ...barChartDatasetDefaults, data: hourlySum }],
            }}
            options={barChartOptions}
        />
    );
};
