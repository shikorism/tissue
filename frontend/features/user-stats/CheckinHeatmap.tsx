import React, { useState, useEffect, useRef } from 'react';
import CalHeatMap from 'cal-heatmap';
// eslint-disable-next-line @typescript-eslint/ban-ts-comment
// @ts-ignore
import LegendLite from 'cal-heatmap/plugins/LegendLite';
// eslint-disable-next-line @typescript-eslint/ban-ts-comment
// @ts-ignore
import CHTooltip from 'cal-heatmap/plugins/Tooltip';
import 'cal-heatmap/cal-heatmap.css';
import type { Dayjs } from 'dayjs';

let idSequence = 0;

interface CheckinHeatmapProps {
    startDate: string;
    data: { date: string; count: number }[];
}

export const CheckinHeatmap: React.FC<CheckinHeatmapProps> = ({ startDate, data }) => {
    const [id] = useState(() => `CheckinHeatmap_${idSequence++}`);
    const chmRef = useRef<CalHeatMap>(null);

    useEffect(() => {
        const chm = chmRef.current || new CalHeatMap();
        chm.paint(
            {
                itemSelector: `#${id}`,
                domain: { type: 'month', label: { text: 'YYYY/MM' } },
                subDomain: { type: 'day' },
                date: {
                    start: new Date(startDate),
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
                    source: data,
                    x: 'date',
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
        chmRef.current = chm;
    }, [id, startDate, data]);

    return <div id={id} />;
};
