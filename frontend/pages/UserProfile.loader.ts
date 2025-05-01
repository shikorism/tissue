import { QueryClient } from '@tanstack/react-query';
import { LoaderFunctionArgs } from 'react-router';
import { getUserCheckinsQuery, getUserQuery, getUserStatsTagsQuery } from '../api/query';

export interface LoaderData {
    username: string;
}

export const loader =
    (queryClient: QueryClient) =>
    async ({ params }: LoaderFunctionArgs) => {
        const username = params.username!;
        const user = await queryClient.ensureQueryData(getUserQuery(username));
        if (!user.is_protected) {
            await Promise.all([
                queryClient.ensureQueryData(getUserStatsTagsQuery(username)),
                queryClient.ensureQueryData(getUserCheckinsQuery(username)),
            ]);
        }
        return { username } satisfies LoaderData;
    };
