import { QueryClient } from '@tanstack/react-query';
import { LoaderFunctionArgs } from 'react-router';
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

        const query = {
            since: `${year}-01-01`,
            until: `${year}-12-31`,
        };

        await Promise.all([
            queryClient.ensureQueryData(getUserStatsCheckinDailyQuery(username, query)),
            queryClient.ensureQueryData(getUserStatsCheckinHourlyQuery(username, query)),
            queryClient.ensureQueryData(getUserStatsTagsQuery(username, query)),
            queryClient.ensureQueryData(getUserStatsTagsQuery(username, { ...query, includes_metadata: true })),
        ]);

        return { username, query } satisfies LoaderData;
    };
