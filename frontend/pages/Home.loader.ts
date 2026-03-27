import { QueryClient } from '@tanstack/react-query';
import {
    getInformationLatestQuery,
    getMeQuery,
    getUserCheckinsQuery,
    getUserStatsCheckinDailyQuery,
    getUserStatsTagsQuery,
} from '../api/query';
import { endOfMonth, formatDate, startOfMonth, subMonths } from 'date-fns';

export interface LoaderData {
    statsCheckinDailyQuery: {
        since: string;
        until: string;
    };
}

export const loader = (queryClient: QueryClient) => async () => {
    const [me] = await Promise.all([
        queryClient.fetchQuery(getMeQuery()), // ステータス欄の情報を最新にするため、常に再読み込み
        queryClient.ensureQueryData(getInformationLatestQuery()),
    ]);
    if (!me) {
        return;
    }

    const now = new Date();
    const statsCheckinDailyQuery = {
        since: formatDate(subMonths(startOfMonth(now), 11), 'yyyy-MM-dd'),
        until: formatDate(endOfMonth(now), 'yyyy-MM-dd'),
    };
    await Promise.all([
        queryClient.ensureQueryData(getUserStatsTagsQuery(me.name)),
        queryClient.ensureQueryData(getUserStatsCheckinDailyQuery(me.name, statsCheckinDailyQuery)),
        queryClient.ensureQueryData(getUserCheckinsQuery(me.name)),
    ]);
    return { statsCheckinDailyQuery } satisfies LoaderData;
};
