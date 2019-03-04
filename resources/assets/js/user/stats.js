import CalHeatMap from 'cal-heatmap';
import Chart from 'chart.js';

function createLineGraph(id, labels, data) {
    const context = document.getElementById(id).getContext('2d');
    new Chart(context, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            legend: {
                display: false
            },
            elements: {
                line: {
                    tension: 0
                }
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });
}

function createBarGraph(id, labels, data) {
    const context = document.getElementById(id).getContext('2d');
    new Chart(context, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            legend: {
                display: false
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });
}

// TODO: これはいくらなんでもひどすぎるだろ
const dailySum = JSON.parse(document.getElementById('graph-daily-sum').textContent);
const monthlyKey = JSON.parse(document.getElementById('graph-monthly-key').textContent);
const monthlySum = JSON.parse(document.getElementById('graph-monthly-sum').textContent);
const yearlyKey = JSON.parse(document.getElementById('graph-yearly-key').textContent);
const yearlySum = JSON.parse(document.getElementById('graph-yearly-sum').textContent);
const hourlyKey = JSON.parse(document.getElementById('graph-hourly-key').textContent);
const hourlySum = JSON.parse(document.getElementById('graph-hourly-sum').textContent);
const dowSum = JSON.parse(document.getElementById('graph-dow-sum').textContent);

new CalHeatMap().init({
    itemSelector: '#cal-heatmap',
    domain: 'month',
    subDomain: 'day',
    domainLabelFormat: '%Y/%m',
    weekStartOnMonday: false,
    start: new Date().setMonth(new Date().getMonth() - 9),
    range: 10,
    data: dailySum,
    legend: [1, 2, 3, 4]
});

createLineGraph('monthly-graph', monthlyKey, monthlySum);
createLineGraph('yearly-graph', yearlyKey, yearlySum);
createBarGraph('hourly-graph', hourlyKey, hourlySum);
createBarGraph('dow-graph', ['日', '月', '火', '水', '木', '金', '土'], dowSum);