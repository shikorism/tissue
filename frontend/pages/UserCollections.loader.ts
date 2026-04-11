import { QueryClient } from '@tanstack/react-query';
import { LoaderFunctionArgs } from 'react-router';
import { getUserCollectionsQuery } from '../api/query';

export interface LoaderData {
    username: string;
}

export const loader =
    (queryClient: QueryClient) =>
    async ({ params }: LoaderFunctionArgs) => {
        const username = params.username!;

        await queryClient.ensureQueryData(getUserCollectionsQuery(username));

        return { username } satisfies LoaderData;
    };
