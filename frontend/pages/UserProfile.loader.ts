import { QueryClient } from '@tanstack/react-query';
import { LoaderFunctionArgs } from 'react-router';
import { getUserCheckinsQuery, getUserQuery, getUserStatsCheckinDailyQuery, getUserStatsTagsQuery } from '../api/query';
import { startOfMonth, endOfMonth, subMonths, formatDate } from 'date-fns';

export interface LoaderData {
    username: string;
    statsCheckinDailyQuery: {
        since: string;
        until: string;
    };
}

export const loader =
    (queryClient: QueryClient) =>
    async ({ params }: LoaderFunctionArgs) => {
        const username = params.username!;

        const now = new Date();
        const statsCheckinDailyQuery = {
            since: formatDate(subMonths(startOfMonth(now), 11), 'yyyy-MM-dd'),
            until: formatDate(endOfMonth(now), 'yyyy-MM-dd'),
        };

        const user = await queryClient.ensureQueryData(getUserQuery(username));
        if (!user.is_protected) {
            await Promise.all([
                queryClient.ensureQueryData(getUserStatsTagsQuery(username)),
                queryClient.ensureQueryData(getUserStatsCheckinDailyQuery(username, statsCheckinDailyQuery)),
                queryClient.ensureQueryData(getUserCheckinsQuery(username)),
            ]);
        }
        return { username, statsCheckinDailyQuery } satisfies LoaderData;
    };
