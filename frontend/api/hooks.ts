import { useQuery } from '@tanstack/react-query';
import { fetchClient } from './client';

export const useGetMe = ({ refetchOnMount } = { refetchOnMount: false }) =>
    useQuery({
        queryKey: ['me'],
        queryFn: () => fetchClient.GET('/me').then((response) => response.data),
        staleTime: 60000,
        refetchOnMount,
    });
