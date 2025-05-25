import React from 'react';
import { Line } from 'react-chartjs-2';
import { lineChartDatasetDefaults, lineChartOptions } from './chart';

interface Props {
    dailyStats: { date: string; count: number }[];
}

export const YearlyChart: React.FC<Props> = ({ dailyStats }) => {
    const yearlySum: Record<string, number> = {};
    for (const daily of dailyStats) {
        const year = daily.date.split('-')[0];
        yearlySum[year] ||= 0;
        yearlySum[year] += daily.count;
    }

    return (
        <Line
            data={{
                labels: Object.keys(yearlySum),
                datasets: [{ ...lineChartDatasetDefaults, data: Object.values(yearlySum) }],
            }}
            options={lineChartOptions}
        />
    );
};
