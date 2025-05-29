import { QueryClient } from '@tanstack/react-query';
import { LoaderFunctionArgs } from 'react-router';
import { getTags } from '../api/query';
import type { paths } from '../api/schema';

export interface LoaderData {
    query: paths['/tags']['get']['parameters']['query'];
}

export const loader =
    (queryClient: QueryClient) =>
    async ({ request }: LoaderFunctionArgs) => {
        const url = new URL(request.url);
        const page = parseInt(url.searchParams.get('page') ?? '1', 10);
        const query = { page };

        await queryClient.ensureQueryData(getTags(query));

        return { query } satisfies LoaderData;
    };
