import { QueryClient } from '@tanstack/react-query';
import { LoaderFunctionArgs } from 'react-router';
import { getUserQuery } from '../api/query';

export interface LoaderData {
    username: string;
}

export const loader =
    (queryClient: QueryClient) =>
    async ({ params }: LoaderFunctionArgs) => {
        const username = params.username!;
        await queryClient.ensureQueryData(getUserQuery(username));
        return { username } satisfies LoaderData;
    };
