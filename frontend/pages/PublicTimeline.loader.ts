import { QueryClient } from '@tanstack/react-query';
import { LoaderFunctionArgs } from 'react-router';
import { getTimelinesPublicQuery } from '../api/query';
import type { paths } from '../api/schema';

export const PER_PAGE = 24;

export interface LoaderData {
    query: paths['/timelines/public']['get']['parameters']['query'];
}

export const loader =
    (queryClient: QueryClient) =>
    async ({ request }: LoaderFunctionArgs) => {
        const url = new URL(request.url);
        const page = parseInt(url.searchParams.get('page') ?? '1', 10);
        const query = { page, per_page: PER_PAGE };

        await queryClient.ensureQueryData(getTimelinesPublicQuery(query));

        return { query } satisfies LoaderData;
    };
