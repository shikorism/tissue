import { QueryClient } from '@tanstack/react-query';
import { LoaderFunctionArgs } from 'react-router';
import { getCheckinQuery } from '../api/query';

export interface LoaderData {
    id: number;
}

export const loader =
    (queryClient: QueryClient) =>
    async ({ params }: LoaderFunctionArgs) => {
        const id = parseInt(params.id!, 10);

        await queryClient.ensureQueryData(getCheckinQuery(id));

        return { id } satisfies LoaderData;
    };
