import CalHeatMap from 'cal-heatmap';
import Chart from 'chart.js';
import {addMonths, format, startOfMonth, subMonths} from 'date-fns';

const graphData = JSON.parse(document.getElementById('graph-data').textContent);

function createLineGraph(id, labels, data) {
    const context = document.getElementById(id).getContext('2d');
    return new Chart(context, {
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

/**
 * @param {Date} from
 */
function createMonthlyGraphData(from) {
    const keys = [];
    const values = [];

    for (let i = 0; i < 12; i++) {
        const current = addMonths(from, i);
        const yearAndMonth = format(current, 'YYYY/MM');
        keys.push(yearAndMonth);
        values.push(graphData.monthlySum[yearAndMonth] || 0);
    }

    return {keys, values};
}

new CalHeatMap().init({
    itemSelector: '#cal-heatmap',
    domain: 'month',
    subDomain: 'day',
    domainLabelFormat: '%Y/%m',
    weekStartOnMonday: false,
    start: new Date().setMonth(new Date().getMonth() - 9),
    range: 10,
    data: graphData.dailySum,
    legend: [1, 2, 3, 4]
});

// 直近1年の月間グラフのデータを準備
const monthlyTermFrom = subMonths(startOfMonth(new Date()), 11);
const {keys: monthlyKey, values: monthlySum} = createMonthlyGraphData(monthlyTermFrom);

const monthlyGraph = createLineGraph('monthly-graph', monthlyKey, monthlySum);
createLineGraph('yearly-graph', graphData.yearlyKey, graphData.yearlySum);
createBarGraph('hourly-graph', graphData.hourlyKey, graphData.hourlySum);
createBarGraph('dow-graph', ['日', '月', '火', '水', '木', '金', '土'], graphData.dowSum);

// 月間グラフの期間セレクターを準備
const monthlyTermSelector = document.getElementById('monthly-term');
for (let year = monthlyTermFrom.getFullYear(); year <= new Date().getFullYear(); year++) {
    const opt = document.createElement('option');
    opt.setAttribute('value', year);
    opt.textContent = `${year}年`;
    monthlyTermSelector.insertBefore(opt, monthlyTermSelector.firstChild);
}
if (monthlyTermSelector.children.length) {
    monthlyTermSelector.selectedIndex = 0;
}

monthlyTermSelector.addEventListener('change', function (e) {
    let monthlyTermFrom;
    if (e.target.selectedIndex === 0) {
        // 今年のデータを表示する時は、直近12ヶ月を表示
        monthlyTermFrom = subMonths(startOfMonth(new Date()), 11);
    } else {
        // 過去のデータを表示する時は、選択年の1〜12月を表示
        monthlyTermFrom = new Date(e.target.value, 0, 1);
    }

    const {keys, values} = createMonthlyGraphData(monthlyTermFrom);

    monthlyGraph.data.labels = keys;
    monthlyGraph.data.datasets[0].data = values;
    monthlyGraph.update();
});
