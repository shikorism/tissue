import CalHeatMap from 'cal-heatmap';

if (document.getElementById('cal-heatmap')) {
    new CalHeatMap().init({
        itemSelector: '#cal-heatmap',
        domain: 'month',
        subDomain: 'day',
        domainLabelFormat: '%Y/%m',
        weekStartOnMonday: false,
        start: new Date().setMonth(new Date().getMonth() - 9),
        range: 10,
        data: JSON.parse(document.getElementById('count-by-day').textContent),
        legend: [1, 2, 3, 4]
    });
}
