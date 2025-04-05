import { useQuery } from '@tanstack/react-query';
import { fetchClient } from './client';

const totalCount = (response: Response): number | undefined => {
    const total = response.headers.get('X-Total-Count');
    return total ? parseInt(total, 10) : undefined;
};

export const useGetMe = ({ refetchOnMount } = { refetchOnMount: false }) =>
    useQuery({
        queryKey: ['me'],
        queryFn: () => fetchClient.GET('/me').then((response) => response.data),
        staleTime: 60000,
        refetchOnMount,
    });

export const useGetTimelinesPublic = () =>
    useQuery({
        queryKey: ['timelines/public'],
        queryFn: () =>
            fetchClient.GET('/timelines/public').then((response) => ({
                totalCount: totalCount(response.response),
                data: response.data,
            })),
        staleTime: 60000,
    });
