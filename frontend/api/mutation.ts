import { useMutation, useQueryClient } from '@tanstack/react-query';
import { fetchClient } from './client';
import type { paths } from './schema';
import { ensure } from './utils';

export const usePostCollections = () => {
    const queryClient = useQueryClient();
    return useMutation({
        mutationFn: (params: paths['/collections']['post']['requestBody']['content']['application/json']) =>
            fetchClient.POST('/collections', { body: params }).then((response) => ensure(response.data)),
        onSuccess: async () => {
            // TODO: invalidate my collections (AddToCollectionButton)
            await queryClient.invalidateQueries({ queryKey: ['/users/{username}/collections'] });
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
            // TODO: invalidate my collections (AddToCollectionButton)
            queryClient.setQueryData(['/collections/{collection_id}', collectionId], data);
            await queryClient.invalidateQueries({ queryKey: ['/users/{username}/collections'] });
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
            // TODO: invalidate my collections (AddToCollectionButton)
            await queryClient.invalidateQueries({ queryKey: ['/users/{username}/collections'] });
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
