import { QueryClient } from '@tanstack/react-query';
import { LoaderFunctionArgs, redirect } from 'react-router';
import { getCollectionItemsQuery, getCollectionQuery } from '../api/query';
import type { paths } from '../api/schema';

export const PER_PAGE = 24;

export interface LoaderData {
    collectionId: number;
    query: paths['/collections/{collection_id}/items']['get']['parameters']['query'];
}

export const loader =
    (queryClient: QueryClient) =>
    async ({ params, request }: LoaderFunctionArgs) => {
        const username = params.username!;
        const collectionId = parseInt(params.collectionId!, 10);

        const url = new URL(request.url);
        const page = parseInt(url.searchParams.get('page') ?? '1', 10);

        const query: LoaderData['query'] = {
            page,
            per_page: PER_PAGE,
        };

        const [collection] = await Promise.all([
            queryClient.ensureQueryData(getCollectionQuery(collectionId)),
            queryClient.ensureQueryData(getCollectionItemsQuery(collectionId, query)),
        ]);

        if (collection.user_name !== username) {
            throw redirect(`/user/${collection.user_name}/collections/${collectionId}`);
        }

        return { collectionId, query } satisfies LoaderData;
    };
