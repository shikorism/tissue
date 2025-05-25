import React from 'react';
import { Line } from 'react-chartjs-2';
import { lineChartDatasetDefaults, lineChartOptions } from './chart';

interface Props {
    startDate: string;
    endDate: string;
    dailyStats: { date: string; count: number }[];
}

export const MonthlyChart: React.FC<Props> = ({ startDate, endDate, dailyStats }) => {
    const start = new Date(startDate);
    const end = new Date(endDate);

    const sum: Record<string, number> = {};
    for (const d = new Date(start); d.getTime() <= end.getTime(); d.setMonth(d.getMonth() + 1)) {
        const ym = d.toISOString().replace('-', '/').substring(0, 7);
        sum[ym] = 0;
    }
    for (const daily of dailyStats) {
        const ym = daily.date.replace('-', '/').substring(0, 7);
        sum[ym] += daily.count;
    }

    return (
        <Line
            data={{
                labels: Object.keys(sum),
                datasets: [{ ...lineChartDatasetDefaults, data: Object.values(sum) }],
            }}
            options={lineChartOptions}
        />
    );
};
