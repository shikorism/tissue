import { QueryClient } from '@tanstack/react-query';
import { getRecentTagsQuery, getUserStatsTagsQuery } from '../api/query';
import { components } from '../api/schema';

export const loader = (queryClient: QueryClient) => async () => {
    const me = queryClient.getQueryData<components['schemas']['User']>(['/me']);
    if (me) {
        await Promise.all([
            queryClient.ensureQueryData(getUserStatsTagsQuery(me.name)),
            queryClient.ensureQueryData(getUserStatsTagsQuery(me.name, { includes_metadata: true })),
            queryClient.ensureQueryData(getRecentTagsQuery()),
        ]);
    }
};
