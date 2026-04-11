import { useQuery } from '@tanstack/react-query';
import { fetchClient } from './client';

export const useGetMe = ({ refetchOnMount } = { refetchOnMount: false }) =>
    useQuery({
        queryKey: ['/me'],
        queryFn: () => fetchClient.GET('/me').then((response) => response.data),
        refetchOnMount,
    });

export const useGetMetadata = (url: string) =>
    useQuery({
        queryKey: ['checkin/card', url],
        queryFn: () =>
            fetchClient.GET('/checkin/card', { params: { query: { url } } }).then((response) => response.data),
    });
