import { keepPreviousData, useQuery } from '@tanstack/react-query';
import { fetchGet, ResponseError } from './fetch';

function asJson(response: Response) {
    if (response.ok) {
        return response.json();
    } else {
        throw new ResponseError(response);
    }
}

export type PaginatedResult<Data> = {
    data: Data;
    totalCount?: number;
};

async function asPaginatedJson<Data>(response: Response): Promise<PaginatedResult<Data>> {
    if (response.ok) {
        const total = response.headers.get('X-Total-Count');
        const data = await response.json();

        return {
            data,
            totalCount: total ? parseInt(total, 10) : undefined,
        };
    } else {
        throw new ResponseError(response);
    }
}

export const useMyProfileQuery = () =>
    useQuery<Tissue.Profile, ResponseError>({
        queryKey: ['MyProfile'],
        queryFn: () => fetchGet('/api/me').then(asJson),
        staleTime: 300000,
    });

export const useMyCollectionsQuery = () =>
    useQuery<Tissue.Collection[], ResponseError>({
        queryKey: ['MyCollections'],
        queryFn: () => fetchGet('/api/collections').then(asJson),
        staleTime: 300000,
    });

export const useCollectionsQuery = (username: string) =>
    useQuery<Tissue.Collection[], ResponseError>({
        queryKey: ['Collections', username],
        queryFn: () => fetchGet(`/api/users/${username}/collections`).then(asJson),
    });

export const useCollectionQuery = (id: string) =>
    useQuery<Tissue.Collection, ResponseError>({
        queryKey: ['Collection', id],
        queryFn: () => fetchGet(`/api/collections/${id}`).then(asJson),
    });

export const useCollectionItemsQuery = (id: string, page?: string | null) =>
    useQuery<PaginatedResult<Tissue.CollectionItem[]>, ResponseError>({
        queryKey: ['CollectionItems', id, { page: page || 1 }],
        queryFn: () =>
            fetchGet(`/api/collections/${id}/items`, page ? { page } : undefined).then((response) =>
                asPaginatedJson(response),
            ),
        placeholderData: keepPreviousData,
    });

export const useUserStatsTagsQuery = (username: string | undefined, includesMetadata?: boolean) =>
    useQuery<Tissue.TagStats[], ResponseError>({
        queryKey: ['UserStatsTags', username, !!includesMetadata],
        queryFn: () =>
            fetchGet(`/api/users/${username}/stats/tags`, {
                includes_metadata: JSON.stringify(!!includesMetadata),
            }).then(asJson),
        enabled: !!username,
    });

export const useRecentTagsQuery = () =>
    useQuery<string[], ResponseError>({
        queryKey: ['RecentTags'],
        queryFn: () => fetchGet(`/api/recent-tags`).then(asJson),
    });
