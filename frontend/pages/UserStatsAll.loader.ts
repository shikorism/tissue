import { QueryClient } from '@tanstack/react-query';
import { LoaderFunctionArgs } from 'react-router';
import { getUserStatsCheckinDailyQuery, getUserStatsCheckinHourlyQuery, getUserStatsTagsQuery } from '../api/query';

export interface LoaderData {
    username: string;
}

export const loader =
    (queryClient: QueryClient) =>
    async ({ params }: LoaderFunctionArgs) => {
        const username = params.username!;

        await Promise.all([
            queryClient.ensureQueryData(getUserStatsCheckinDailyQuery(username)),
            queryClient.ensureQueryData(getUserStatsCheckinHourlyQuery(username)),
            queryClient.ensureQueryData(getUserStatsTagsQuery(username)),
            queryClient.ensureQueryData(getUserStatsTagsQuery(username, { includes_metadata: true })),
        ]);

        return { username } satisfies LoaderData;
    };
