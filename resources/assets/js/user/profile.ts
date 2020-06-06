import * as CalHeatMap from 'cal-heatmap';
import { subMonths } from 'date-fns';

if (document.getElementById('cal-heatmap')) {
    new CalHeatMap().init({
        itemSelector: '#cal-heatmap',
        domain: 'month',
        subDomain: 'day',
        domainLabelFormat: '%Y/%m',
        weekStartOnMonday: false,
        start: subMonths(new Date(), 9),
        range: 10,
        data: JSON.parse(document.getElementById('count-by-day')!.textContent as string),
        legend: [1, 2, 3, 4],
    });
}
