import React from 'react';
import { Bar } from 'react-chartjs-2';
import { barChartDatasetDefaults, barChartOptions } from './chart';

interface Props {
    dailyStats: { date: string; count: number }[];
}

export const DayOfWeekChart: React.FC<Props> = ({ dailyStats }) => {
    const dowSum: number[] = new Array(7).fill(0);
    for (const daily of dailyStats) {
        const dow = new Date(`${daily.date}T00:00:00+09:00`).getDay();
        dowSum[dow] += daily.count;
    }

    return (
        <Bar
            data={{
                labels: ['日', '月', '火', '水', '木', '金', '土'],
                datasets: [{ ...barChartDatasetDefaults, data: dowSum }],
            }}
            options={barChartOptions}
        />
    );
};
