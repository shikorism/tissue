import { QueryClient, useMutation, useQueryClient } from '@tanstack/react-query';
import { fetchClient } from './client';
import type { paths, components } from './schema';
import { ensure } from './utils';
import { getTimelinesPublicQuery, getUserCheckinsQuery, TDataOfQuery } from './query';

export const usePostCheckin = () => {
    const queryClient = useQueryClient();
    return useMutation({
        mutationFn: (params: paths['/checkins']['post']['requestBody']['content']['application/json']) =>
            fetchClient.POST('/checkins', { body: params }).then((response) => ensure(response.data)),
        onSuccess: async (data) => {
            await Promise.all([
                queryClient.invalidateQueries({ queryKey: ['/users/{username}/checkins', data.user.name] }),
                queryClient.invalidateQueries({ queryKey: ['/users/{username}/stats/tags', data.user.name] }),
                queryClient.invalidateQueries({ queryKey: ['/timelines/public'] }),
                queryClient.invalidateQueries({ queryKey: ['/recent-tags'] }),
            ]);
        },
    });
};

export const useDeleteCheckin = () => {
    const queryClient = useQueryClient();
    return useMutation({
        mutationFn: (params: { id: number }) =>
            fetchClient.DELETE('/checkins/{id}', {
                params: { path: { id: params.id } },
            }),
        onSuccess: (_, { id }) => {
            queryClient.removeQueries({ queryKey: ['/checkins/{id}', id] });
        },
    });
};

export const usePostCollections = () => {
    const queryClient = useQueryClient();
    return useMutation({
        mutationFn: (params: paths['/collections']['post']['requestBody']['content']['application/json']) =>
            fetchClient.POST('/collections', { body: params }).then((response) => ensure(response.data)),
        onSuccess: async () => {
            await Promise.all([
                queryClient.invalidateQueries({ queryKey: ['/collections'] }),
                queryClient.invalidateQueries({ queryKey: ['/users/{username}/collections'] }),
            ]);
        },
    });
};

export const usePutCollection = () => {
    const queryClient = useQueryClient();
    return useMutation({
        mutationFn: (params: {
            collectionId: number;
            body: paths['/collections/{collection_id}']['put']['requestBody']['content']['application/json'];
        }) =>
            fetchClient
                .PUT('/collections/{collection_id}', {
                    params: { path: { collection_id: params.collectionId } },
                    body: params.body,
                })
                .then((response) => ensure(response.data)),
        onSuccess: async (data, { collectionId }) => {
            queryClient.setQueryData(['/collections/{collection_id}', collectionId], data);
            await Promise.all([
                queryClient.invalidateQueries({ queryKey: ['/collections'] }),
                queryClient.invalidateQueries({ queryKey: ['/users/{username}/collections'] }),
            ]);
        },
    });
};

export const useDeleteCollection = () => {
    const queryClient = useQueryClient();
    return useMutation({
        mutationFn: (collectionId: number) =>
            fetchClient.DELETE('/collections/{collection_id}', {
                params: { path: { collection_id: collectionId } },
            }),
        onSuccess: async () => {
            await Promise.all([
                queryClient.invalidateQueries({ queryKey: ['/collections'] }),
                queryClient.invalidateQueries({ queryKey: ['/users/{username}/collections'] }),
            ]);
        },
    });
};

export const usePostCollectionItem = () => {
    const queryClient = useQueryClient();
    return useMutation({
        mutationFn: (params: {
            collectionId: number;
            body: paths['/collections/{collection_id}/items']['post']['requestBody']['content']['application/json'];
        }) =>
            fetchClient
                .POST('/collections/{collection_id}/items', {
                    params: {
                        path: { collection_id: params.collectionId },
                    },
                    body: params.body,
                })
                .then((response) => ensure(response.data)),
        onSuccess: async (_, { collectionId }) => {
            await queryClient.invalidateQueries({ queryKey: ['/collections/{collection_id}/items', collectionId] });
        },
    });
};

export const usePatchCollectionItem = () => {
    const queryClient = useQueryClient();
    return useMutation({
        mutationFn: (params: {
            collectionId: number;
            collectionItemId: number;
            body: paths['/collections/{collection_id}/items/{collection_item_id}']['patch']['requestBody']['content']['application/json'];
        }) =>
            fetchClient
                .PATCH('/collections/{collection_id}/items/{collection_item_id}', {
                    params: {
                        path: { collection_id: params.collectionId, collection_item_id: params.collectionItemId },
                    },
                    body: params.body,
                })
                .then((response) => ensure(response.data)),
        onSuccess: async (_, { collectionId }) => {
            await queryClient.invalidateQueries({ queryKey: ['/collections/{collection_id}/items', collectionId] });
        },
    });
};

export const useDeleteCollectionItem = () => {
    const queryClient = useQueryClient();
    return useMutation({
        mutationFn: (params: { collectionId: number; collectionItemId: number }) =>
            fetchClient.DELETE('/collections/{collection_id}/items/{collection_item_id}', {
                params: { path: { collection_id: params.collectionId, collection_item_id: params.collectionItemId } },
            }),
        onSuccess: async (_, { collectionId }) => {
            await queryClient.invalidateQueries({ queryKey: ['/collections/{collection_id}/items', collectionId] });
        },
    });
};

const updateCachesAfterUpdateLike = (
    queryClient: QueryClient,
    data: { id: number; is_liked?: boolean; likes_count?: number },
) => {
    queryClient.setQueryData(['/checkins/{id}', data.id], (old) =>
        old ? { ...old, is_liked: data.is_liked, likes_count: data.likes_count } : old,
    );

    const update = <T extends { data: components['schemas']['Checkin'][] }>(old: T | undefined) =>
        old && {
            ...old,
            data: old.data.map((checkin) =>
                checkin.id === data.id
                    ? {
                          ...checkin,
                          is_liked: data.is_liked,
                          likes_count: data.likes_count,
                      }
                    : checkin,
            ),
        };
    queryClient.setQueriesData<TDataOfQuery<typeof getUserCheckinsQuery>>(
        { queryKey: ['/users/{username}/checkins'] },
        update,
    );
    queryClient.setQueriesData<TDataOfQuery<typeof getTimelinesPublicQuery>>(
        { queryKey: ['/timelines/public'] },
        update,
    );
    queryClient.setQueriesData<TDataOfQuery<typeof getTimelinesPublicQuery>>(
        { queryKey: ['/search/checkins'] },
        update,
    );
};

export const usePostLike = () => {
    const queryClient = useQueryClient();
    return useMutation({
        mutationFn: (checkinId: number) =>
            fetchClient
                .POST('/likes', {
                    body: { id: checkinId },
                })
                .then((response) => ensure(response.data)),
        onSuccess: (data) => {
            updateCachesAfterUpdateLike(queryClient, data.ejaculation);
        },
    });
};

export const useDeleteLike = () => {
    const queryClient = useQueryClient();
    return useMutation({
        mutationFn: (checkinId: number) =>
            fetchClient
                .DELETE('/likes/{id}', {
                    params: { path: { id: checkinId } },
                })
                .then((response) => ensure(response.data)),
        onSuccess: (data) => {
            updateCachesAfterUpdateLike(queryClient, data.ejaculation);
        },
    });
};
