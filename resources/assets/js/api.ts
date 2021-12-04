import { useEffect, useState } from 'react';
import { fetchGet, ResponseError } from './fetch';

function makeFetchHook<Params, Data>(fetch: (params: Params) => Promise<Response>) {
    return (params: Params) => {
        const [loading, setLoading] = useState(false);
        const [data, setData] = useState<Data | undefined>(undefined);
        const [error, setError] = useState<any>(null);

        useEffect(() => {
            setLoading(true);
            fetch(params)
                .then((response) => {
                    if (response.ok) {
                        return response.json();
                    }
                    throw new ResponseError(response);
                })
                .then((data) => {
                    setData(data);
                })
                .catch((e) => {
                    console.error(e);
                    setError(e);
                })
                .finally(() => {
                    setLoading(false);
                });
        }, []);

        return { loading, data, error };
    };
}

export const useFetchMyProfile = makeFetchHook<void, Tissue.Profile>(() => fetchGet(`/api/me`));

export const useFetchCollections = makeFetchHook<{ username: string }, Tissue.Collection[]>(({ username }) =>
    fetchGet(`/api/users/${username}/collections`)
);

export const useFetchCollectionItems = makeFetchHook<{ id: string }, Tissue.CollectionItem[]>(({ id }) =>
    fetchGet(`/api/collections/${id}/items`)
);
