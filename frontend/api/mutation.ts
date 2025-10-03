import { useMutation, useQueryClient } from '@tanstack/react-query';
import { fetchClient } from './client';
import type { paths } from './schema';
import { ensure } from './utils';

export const useDeleteCheckin = () => {
    const queryClient = useQueryClient();
    return useMutation({
        mutationFn: (params: { id: number }) =>
            fetchClient.DELETE('/checkins/{id}', {
                params: { path: { id: params.id } },
            }),
        onSuccess: async (_, { id }) => {
            await queryClient.invalidateQueries({ queryKey: ['/checkins/{id}', id] });
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
