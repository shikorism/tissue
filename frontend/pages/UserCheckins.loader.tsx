import { QueryClient } from '@tanstack/react-query';
import { LoaderFunctionArgs } from 'react-router';
import { endOfMonth } from 'date-fns';
import { getUserCheckinsQuery, getUserStatsCheckinDailyQuery } from '../api/query';
import type { paths } from '../api/schema';

export const PER_PAGE = 20;

export interface LoaderData {
    username: string;
    checkinsQuery: paths['/users/{username}/checkins']['get']['parameters']['query'];
    statsQuery: paths['/users/{username}/stats/checkin/daily']['get']['parameters']['query'];
}

export const loader =
    (queryClient: QueryClient) =>
    async ({ params, request }: LoaderFunctionArgs) => {
        const username = params.username!;
        const { year, month, date } = params;

        const url = new URL(request.url);
        const page = parseInt(url.searchParams.get('page') ?? '1', 10);

        const checkinsQuery: LoaderData['checkinsQuery'] = {
            ...rangeFromDateComponents(year, month, date),
            page,
            per_page: PER_PAGE,
        };
        if (year || month || date) {
            checkinsQuery.order = 'asc';
        }
        const statsQuery = rangeFromDateComponents(year, month);

        await queryClient.ensureQueryData(getUserCheckinsQuery(username, checkinsQuery));
        await queryClient.ensureQueryData(getUserStatsCheckinDailyQuery(username, statsQuery));

        return { username, checkinsQuery, statsQuery } satisfies LoaderData;
    };

const rangeFromDateComponents = (year?: string, month?: string, date?: string) => {
    if (year && month && date) {
        return { since: `${year}-${pad2(month)}-${pad2(date)}`, until: `${year}-${pad2(month)}-${pad2(date)}` };
    } else if (year && month) {
        const end = endOfMonth(new Date(`${year}-${pad2(month)}-01T00:00:00+09:00`)).getDate();
        return { since: `${year}-${pad2(month)}-01`, until: `${year}-${pad2(month)}-${end}` };
    } else if (year) {
        return { since: `${year}-01-01`, until: `${year}-12-31` };
    }
    return {};
};

const pad2 = (s: string) => (s.length === 1 ? `0${s}` : s);
