import { useQuery, keepPreviousData } from '@tanstack/react-query';
import { fetchClient } from './client';
import type { paths } from './schema';

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

export const useGetTimelinesPublic = (
    query?: paths['/timelines/public']['get']['parameters']['query'],
    keepPrevious: boolean = false,
) =>
    useQuery({
        queryKey: ['timelines/public', query],
        queryFn: () =>
            fetchClient.GET('/timelines/public', { params: { query } }).then(
                (response) =>
                    response.data && {
                        totalCount: totalCount(response.response),
                        data: response.data,
                    },
            ),
        placeholderData: keepPrevious ? keepPreviousData : undefined,
    });

export const useGetMetadata = (url: string) =>
    useQuery({
        queryKey: ['checkin/card', url],
        queryFn: () =>
            fetchClient.GET('/checkin/card', { params: { query: { url } } }).then((response) => response.data),
    });
