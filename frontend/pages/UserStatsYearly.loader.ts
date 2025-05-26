import { QueryClient } from '@tanstack/react-query';
import { LoaderFunctionArgs } from 'react-router';
import { getUserStatsCheckinDailyQuery, getUserStatsCheckinHourlyQuery, getUserStatsTagsQuery } from '../api/query';

export interface LoaderData {
    username: string;
    query: {
        since: string;
        until: string;
    };
    prevQuery?: {
        since: string;
        until: string;
    };
}

export const loader =
    (queryClient: QueryClient) =>
    async ({ params, request }: LoaderFunctionArgs) => {
        const username = params.username!;
        const year = params.year!;

        const url = new URL(request.url);
        const compare = url.searchParams.get('compare');

        const query = {
            since: `${year}-01-01`,
            until: `${year}-12-31`,
        };
        const prevQuery =
            compare === 'prev'
                ? { since: `${parseInt(year, 10) - 1}-01-01`, until: `${parseInt(year, 10) - 1}-12-31` }
                : undefined;

        const promises = [
            queryClient.ensureQueryData(getUserStatsCheckinDailyQuery(username, query)),
            queryClient.ensureQueryData(getUserStatsCheckinHourlyQuery(username, query)),
            queryClient.ensureQueryData(getUserStatsTagsQuery(username, query)),
            queryClient.ensureQueryData(getUserStatsTagsQuery(username, { ...query, includes_metadata: true })),
        ];
        if (prevQuery) {
            promises.push(queryClient.ensureQueryData(getUserStatsCheckinDailyQuery(username, prevQuery)));
            promises.push(queryClient.ensureQueryData(getUserStatsCheckinHourlyQuery(username, prevQuery)));
        }
        await Promise.all(promises);

        return { username, query, prevQuery } satisfies LoaderData;
    };
