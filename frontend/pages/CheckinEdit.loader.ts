import { QueryClient } from '@tanstack/react-query';
import { getCheckinQuery, getRecentTagsQuery, getUserStatsTagsQuery } from '../api/query';
import { components } from '../api/schema';
import { LoaderFunctionArgs } from 'react-router';

export interface LoaderData {
    id: number;
}

export const loader =
    (queryClient: QueryClient) =>
    async ({ params }: LoaderFunctionArgs) => {
        const id = parseInt(params.id!, 10);

        const promises: Promise<unknown>[] = [];
        promises.push(queryClient.ensureQueryData(getCheckinQuery(id)));

        const me = queryClient.getQueryData<components['schemas']['User']>(['/me']);
        if (me) {
            promises.push(
                queryClient.ensureQueryData(getUserStatsTagsQuery(me.name)),
                queryClient.ensureQueryData(getUserStatsTagsQuery(me.name, { includes_metadata: true })),
                queryClient.ensureQueryData(getRecentTagsQuery()),
            );
        }

        await Promise.all(promises);

        return { id } satisfies LoaderData;
    };
