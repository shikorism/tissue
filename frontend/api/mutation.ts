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
