import * as Chart from 'chart.js';

const graph = document.getElementById('global-count-graph') as HTMLCanvasElement;
const labels = JSON.parse(document.getElementById('global-count-labels')!.textContent as string);
const data = JSON.parse(document.getElementById('global-count-data')!.textContent as string);

new Chart(graph.getContext('2d')!, {
    type: 'bar',
    data: {
        labels,
        datasets: [{
            data,
            backgroundColor: 'rgba(0, 0, 0, .1)',
            borderColor: 'rgba(0, 0, 0, .25)',
            borderWidth: 1
        }]
    },
    options: {
        maintainAspectRatio: false,
        legend: {
            display: false
        },
        elements: {
            line: {}
        },
        scales: {
            xAxes: [{
                display: false
            }],
            yAxes: [{
                display: false,
                ticks: {
                    beginAtZero: true
                }
            }]
        }
    }
});
