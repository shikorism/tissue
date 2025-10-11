import { keepPreviousData, queryOptions, QueryOptions } from '@tanstack/react-query';
import { fetchClient } from './client';
import type { paths } from './schema';
import { ensure, totalCount } from './utils';

type ExtractTData<T> = T extends QueryOptions<any, any, infer TData, any> ? TData : never;
type Return<T> = T extends (...a: any) => infer R ? R : never;
export type TDataOfQuery<F> = ExtractTData<Return<F>>;

export const getMeQuery = ({ refetchOnMount } = { refetchOnMount: false }) =>
    queryOptions({
        queryKey: ['/me'],
        queryFn: () => fetchClient.GET('/me').then((response) => response.data),
        refetchOnMount,
    });

export const getMyCollectionsQuery = () =>
    queryOptions({
        queryKey: ['/collections'],
        queryFn: () => fetchClient.GET('/collections').then((response) => ensure(response.data)),
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

export const getUserStatsCheckinDailyQuery = (
    username: string,
    query?: paths['/users/{username}/stats/checkin/daily']['get']['parameters']['query'],
) =>
    queryOptions({
        queryKey: ['/users/{username}/stats/checkin/daily', username, query],
        queryFn: () =>
            fetchClient
                .GET('/users/{username}/stats/checkin/daily', { params: { path: { username }, query } })
                .then((response) => ensure(response.data)),
    });

export const getUserStatsCheckinHourlyQuery = (
    username: string,
    query?: paths['/users/{username}/stats/checkin/hourly']['get']['parameters']['query'],
) =>
    queryOptions({
        queryKey: ['/users/{username}/stats/checkin/hourly', username, query],
        queryFn: () =>
            fetchClient
                .GET('/users/{username}/stats/checkin/hourly', { params: { path: { username }, query } })
                .then((response) => ensure(response.data)),
    });

export const getUserStatsCheckinOldestQuery = (username: string) =>
    queryOptions({
        queryKey: ['/users/{username}/stats/checkin/oldest', username],
        queryFn: () =>
            fetchClient
                .GET('/users/{username}/stats/checkin/oldest', { params: { path: { username } } })
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

export const getUserLikesQuery = (
    username: string,
    query?: paths['/users/{username}/likes']['get']['parameters']['query'],
) =>
    queryOptions({
        queryKey: ['/users/{username}/likes', username, query],
        queryFn: () =>
            fetchClient.GET('/users/{username}/likes', { params: { path: { username }, query } }).then(
                (response) =>
                    ensure(response.data) && {
                        totalCount: totalCount(response.response),
                        data: ensure(response.data),
                    },
            ),
    });

export const getUserCollectionsQuery = (username: string) =>
    queryOptions({
        queryKey: ['/users/{username}/collections', username],
        queryFn: () =>
            fetchClient
                .GET('/users/{username}/collections', { params: { path: { username } } })
                .then((response) => ensure(response.data)),
    });

export const getCheckinQuery = (id: number) =>
    queryOptions({
        queryKey: ['/checkins/{id}', id],
        queryFn: () =>
            fetchClient.GET('/checkins/{id}', { params: { path: { id } } }).then((response) => ensure(response.data)),
    });

export const getCollectionQuery = (collectionId: number) =>
    queryOptions({
        queryKey: ['/collections/{collection_id}', collectionId],
        queryFn: () =>
            fetchClient
                .GET('/collections/{collection_id}', { params: { path: { collection_id: collectionId } } })
                .then((response) => ensure(response.data)),
    });

export const getCollectionItemsQuery = (
    collectionId: number,
    query?: paths['/collections/{collection_id}/items']['get']['parameters']['query'],
) =>
    queryOptions({
        queryKey: ['/collections/{collection_id}/items', collectionId, query],
        queryFn: () =>
            fetchClient
                .GET('/collections/{collection_id}/items', { params: { path: { collection_id: collectionId }, query } })
                .then(
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

export const getTags = (query?: paths['/tags']['get']['parameters']['query']) =>
    queryOptions({
        queryKey: ['/tags', query],
        queryFn: () =>
            fetchClient.GET('/tags', { params: { query } }).then(
                (response) =>
                    ensure(response.data) && {
                        totalCount: totalCount(response.response),
                        data: ensure(response.data),
                    },
            ),
    });

export const getMetadataQuery = (url: string) =>
    queryOptions({
        queryKey: ['checkin/card', url],
        queryFn: () =>
            fetchClient.GET('/checkin/card', { params: { query: { url } } }).then((response) => response.data),
    });

export const getSearchCheckinsQuery = (query: paths['/search/checkins']['get']['parameters']['query']) =>
    queryOptions({
        queryKey: ['/search/checkins', query],
        queryFn: () =>
            fetchClient.GET('/search/checkins', { params: { query } }).then(
                (response) =>
                    ensure(response.data) && {
                        totalCount: totalCount(response.response),
                        data: ensure(response.data),
                    },
            ),
    });

export const getSearchCollectionsQuery = (query: paths['/search/collections']['get']['parameters']['query']) =>
    queryOptions({
        queryKey: ['/search/collections', query],
        queryFn: () =>
            fetchClient.GET('/search/collections', { params: { query } }).then(
                (response) =>
                    ensure(response.data) && {
                        totalCount: totalCount(response.response),
                        data: ensure(response.data),
                    },
            ),
    });

export const getSearchTagsQuery = (query: paths['/search/tags']['get']['parameters']['query']) =>
    queryOptions({
        queryKey: ['/search/tags', query],
        queryFn: () =>
            fetchClient.GET('/search/tags', { params: { query } }).then(
                (response) =>
                    ensure(response.data) && {
                        totalCount: totalCount(response.response),
                        data: ensure(response.data),
                    },
            ),
    });

export const getInformationLatestQuery = () =>
    queryOptions({
        queryKey: ['/information/latest'],
        queryFn: () => fetchClient.GET('/information/latest').then((response) => ensure(response.data)),
    });
