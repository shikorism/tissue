import { QueryClient } from '@tanstack/react-query';
import { getMyCollectionsQuery } from '../api/query';

export const loader = (queryClient: QueryClient) => async () => {
    await queryClient.ensureQueryData(getMyCollectionsQuery());
};
