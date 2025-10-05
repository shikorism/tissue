import { QueryClient } from '@tanstack/react-query';
import { LoaderFunctionArgs } from 'react-router';
import type { paths } from '../api/schema';
import { getSearchTagsQuery } from '../api/query';
import { EmptyQueryError } from '../features/search/EmptyQueryError';

export const PER_PAGE = 60;

export interface LoaderData {
    query: paths['/search/tags']['get']['parameters']['query'];
}

export const loader =
    (queryClient: QueryClient) =>
    async ({ request }: LoaderFunctionArgs) => {
        const url = new URL(request.url);
        const q = url.searchParams.get('q');
        if (!q) {
            throw new EmptyQueryError();
        }
        const page = parseInt(url.searchParams.get('page') ?? '1', 10);
        const query = { q, page, per_page: PER_PAGE };

        await queryClient.ensureQueryData(getSearchTagsQuery(query));

        return { query } satisfies LoaderData;
    };
