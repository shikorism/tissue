import CalHeatMap from 'cal-heatmap';
// eslint-disable-next-line @typescript-eslint/ban-ts-comment
// @ts-ignore
import LegendLite from 'cal-heatmap/plugins/LegendLite';
// eslint-disable-next-line @typescript-eslint/ban-ts-comment
// @ts-ignore
import Tooltip from 'cal-heatmap/plugins/Tooltip';
import type { Dayjs } from 'dayjs';
import 'cal-heatmap/cal-heatmap.css';
import { subMonths } from 'date-fns';

if (document.getElementById('cal-heatmap')) {
    new CalHeatMap().paint(
        {
            itemSelector: '#cal-heatmap',
            domain: { type: 'month', label: { text: 'YYYY/MM' } },
            subDomain: { type: 'day' },
            date: {
                start: subMonths(new Date(), 9),
                timezone: 'Asia/Tokyo',
                locale: { weekStart: 0 },
            },
            range: 10,
            scale: {
                color: {
                    scheme: 'YlGn',
                    type: 'ordinal',
                    domain: [0, 1, 2, 3, 4],
                },
            },
            data: {
                source: JSON.parse(document.getElementById('count-by-day')!.textContent as string),
                x: (d: { t: number }) => d.t * 1000,
                y: 'count',
            },
        },
        [
            [LegendLite],
            [
                Tooltip,
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
