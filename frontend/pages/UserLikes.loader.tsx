import { QueryClient } from '@tanstack/react-query';
import { LoaderFunctionArgs } from 'react-router';
import { getUserLikesQuery } from '../api/query';
import type { paths } from '../api/schema';

export const PER_PAGE = 24;

export interface LoaderData {
    username: string;
    likesQuery: paths['/users/{username}/likes']['get']['parameters']['query'];
}

export const loader =
    (queryClient: QueryClient) =>
    async ({ params, request }: LoaderFunctionArgs) => {
        const username = params.username!;

        const url = new URL(request.url);
        const page = parseInt(url.searchParams.get('page') ?? '1', 10);

        const likesQuery: LoaderData['likesQuery'] = {
            page,
            per_page: PER_PAGE,
        };

        await queryClient.ensureQueryData(getUserLikesQuery(username, likesQuery));

        return { username, likesQuery } satisfies LoaderData;
    };
