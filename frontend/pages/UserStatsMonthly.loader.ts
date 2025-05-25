import { QueryClient } from '@tanstack/react-query';
import { LoaderFunctionArgs } from 'react-router';
import { endOfMonth } from 'date-fns';
import { getUserStatsCheckinDailyQuery, getUserStatsCheckinHourlyQuery, getUserStatsTagsQuery } from '../api/query';

export interface LoaderData {
    username: string;
    query: {
        since: string;
        until: string;
    };
}

export const loader =
    (queryClient: QueryClient) =>
    async ({ params }: LoaderFunctionArgs) => {
        const username = params.username!;
        const year = params.year!;
        const month = params.month!;

        const end = endOfMonth(new Date(`${year}-${pad2(month)}-01T00:00:00+09:00`)).getDate();
        const query = {
            since: `${year}-${pad2(month)}-01`,
            until: `${year}-${pad2(month)}-${end}`,
        };

        await Promise.all([
            queryClient.ensureQueryData(getUserStatsCheckinDailyQuery(username, query)),
            queryClient.ensureQueryData(getUserStatsCheckinHourlyQuery(username, query)),
            queryClient.ensureQueryData(getUserStatsTagsQuery(username, query)),
            queryClient.ensureQueryData(getUserStatsTagsQuery(username, { ...query, includes_metadata: true })),
        ]);

        return { username, query } satisfies LoaderData;
    };

const pad2 = (s: string) => (s.length === 1 ? `0${s}` : s);
