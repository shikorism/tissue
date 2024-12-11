import CalHeatMap from 'cal-heatmap';
// eslint-disable-next-line @typescript-eslint/ban-ts-comment
// @ts-ignore
import LegendLite from 'cal-heatmap/plugins/LegendLite';
// eslint-disable-next-line @typescript-eslint/ban-ts-comment
// @ts-ignore
import CHTooltip from 'cal-heatmap/plugins/Tooltip';
import 'cal-heatmap/cal-heatmap.css';
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
import { addMonths, format } from 'date-fns';
import type { Dayjs } from 'dayjs';

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

const graphData = JSON.parse(document.getElementById('graph-data')!.textContent as string);
const compareDataJson = document.getElementById('compare-data')?.textContent;
const compareData = compareDataJson ? JSON.parse(compareDataJson) : null;

function createLineGraph(id: string, labels: string[], data: any, compareData: any = null) {
    const context = (document.getElementById(id) as HTMLCanvasElement).getContext('2d');
    const datasets = [
        {
            data: data,
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            fill: true,
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 1,
        },
    ];
    if (compareData) {
        datasets.splice(0, 0, {
            data: compareData,
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            fill: true,
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1,
        });
    }
    return new Chart(context!, {
        type: 'line',
        data: {
            labels: labels,
            datasets: datasets,
        },
        options: {
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
        },
    });
}

function createBarGraph(id: string, labels: string[], data: any, compareData: any = null) {
    const context = (document.getElementById(id) as HTMLCanvasElement).getContext('2d');
    const datasets = [
        {
            data: data,
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 1,
        },
    ];
    if (compareData) {
        datasets.splice(0, 0, {
            data: compareData,
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1,
        });
    }
    new Chart(context!, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: datasets,
        },
        options: {
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
        },
    });
}

function createMonthlyGraphData(from: Date, data: any) {
    const keys = [];
    const values = [];

    for (let i = 0; i < 12; i++) {
        const current = addMonths(from, i);
        const yearAndMonth = format(current, 'yyyy/MM');
        keys.push(yearAndMonth);
        values.push(data.monthlySum[yearAndMonth] || 0);
    }

    return { keys, values };
}

function getCurrentYear(): number {
    const year = location.pathname.split('/').pop() || '';
    if (/^(20[0-9]{2})$/.test(year)) {
        return parseInt(year, 10);
    } else {
        throw 'Invalid year';
    }
}

if (document.getElementById('cal-heatmap')) {
    new CalHeatMap().paint(
        {
            itemSelector: '#cal-heatmap',
            domain: { type: 'month', label: { text: 'YYYY/MM' } },
            subDomain: { type: 'day' },
            date: {
                start: Date.UTC(getCurrentYear(), 0, 1, 9, 0, 0, 0),
                timezone: 'Asia/Tokyo',
                locale: { weekStart: 0 },
            },
            range: 12,
            scale: {
                color: {
                    scheme: 'YlGn',
                    type: 'ordinal',
                    domain: [0, 1, 2, 3, 4],
                },
            },
            data: {
                source: graphData.dailySum,
                x: (d: { t: number }) => d.t * 1000,
                y: 'count',
            },
        },
        [
            [LegendLite],
            [
                CHTooltip,
                {
                    // eslint-disable-next-line @typescript-eslint/ban-ts-comment
                    // @ts-ignore
                    text: (timestamp: number, value: number, dayjsDate: Dayjs) =>
                        `${dayjsDate.format('YYYY/MM/DD')} - ${value || 0}回`,
                },
            ],
        ],
    );
}

if (document.getElementById('monthly-graph')) {
    const { keys: monthlyKey, values: monthlySum } = createMonthlyGraphData(
        new Date(getCurrentYear(), 0, 1, 0, 0, 0, 0),
        graphData,
    );
    createLineGraph(
        'monthly-graph',
        monthlyKey,
        monthlySum,
        compareData
            ? createMonthlyGraphData(new Date(getCurrentYear() - 1, 0, 1, 0, 0, 0, 0), compareData).values
            : null,
    );
}
if (document.getElementById('yearly-graph')) {
    createLineGraph('yearly-graph', graphData.yearlyKey, graphData.yearlySum);
}
if (document.getElementById('hourly-graph')) {
    createBarGraph('hourly-graph', graphData.hourlyKey, graphData.hourlySum, compareData?.hourlySum);
}
if (document.getElementById('dow-graph')) {
    createBarGraph('dow-graph', ['日', '月', '火', '水', '木', '金', '土'], graphData.dowSum, compareData?.dowSum);
}

const compare = document.getElementById('compare') as HTMLInputElement | null;
if (compare) {
    const params = new URLSearchParams(location.search);
    compare.checked = params.get('compare') === 'prev';
    compare.addEventListener('change', () => {
        location.search = compare.checked ? '?compare=prev' : '';
    });
}
