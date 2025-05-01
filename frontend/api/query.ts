import { keepPreviousData, queryOptions } from '@tanstack/react-query';
import { fetchClient } from './client';
import type { paths } from './schema';

const ensure = <T>(value: T | undefined | null): T => {
    if (value === undefined || value === null) {
        throw new Error('Value is undefined or null');
    }
    return value;
};

const totalCount = (response: Response): number | undefined => {
    const total = response.headers.get('X-Total-Count');
    return total ? parseInt(total, 10) : undefined;
};

export const getMeQuery = ({ refetchOnMount } = { refetchOnMount: false }) =>
    queryOptions({
        queryKey: ['/me'],
        queryFn: () => fetchClient.GET('/me').then((response) => response.data),
        refetchOnMount,
    });

export const getUserQuery = (username: string) =>
    queryOptions({
        queryKey: ['/users/{username}', username],
        queryFn: () =>
            fetchClient
                .GET('/users/{username}', { params: { path: { username } } })
                .then((response) => ensure(response.data)),
    });

export const getUserStatsTagsQuery = (
    username: string,
    query?: paths['/users/{username}/stats/tags']['get']['parameters']['query'],
) =>
    queryOptions({
        queryKey: ['/users/{username}/stats/tags', username, query],
        queryFn: () =>
            fetchClient
                .GET('/users/{username}/stats/tags', { params: { path: { username }, query } })
                .then((response) => ensure(response.data)),
    });

export const getUserCheckinsQuery = (
    username: string,
    query?: paths['/users/{username}/checkins']['get']['parameters']['query'],
) =>
    queryOptions({
        queryKey: ['/users/{username}/checkins', username, query],
        queryFn: () =>
            fetchClient.GET('/users/{username}/checkins', { params: { path: { username }, query } }).then(
                (response) =>
                    ensure(response.data) && {
                        totalCount: totalCount(response.response),
                        data: ensure(response.data),
                    },
            ),
    });

export const getTimelinesPublicQuery = (
    query?: paths['/timelines/public']['get']['parameters']['query'],
    keepPrevious: boolean = false,
) =>
    queryOptions({
        queryKey: ['/timelines/public', query],
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
