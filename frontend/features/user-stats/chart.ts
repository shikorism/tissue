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

export const lineChartDatasetDefaults = {
    backgroundColor: 'rgba(255, 99, 132, 0.2)',
    fill: true,
    borderColor: 'rgba(255, 99, 132, 1)',
    borderWidth: 1,
} as const;

export const lineChartOptions = {
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

export const barChartDatasetDefaults = {
    backgroundColor: 'rgba(255, 99, 132, 0.2)',
    borderColor: 'rgba(255, 99, 132, 1)',
    borderWidth: 1,
} as const;

export const barChartOptions = {
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
