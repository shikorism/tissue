import { useEffect, useState, useCallback } from 'react';
import { fetchGet, ResponseError } from './fetch';

/**
 * @example
 *   manaita([1, 2, [30, [40, 50]]]) // => [1, 2, 30, 40, 50]
 *   manaita({ foo: 'hoge', bar: 'fuga', baz: [10, 20, 30] }) // => ['hoge', 'fuga', 10, 20, 30]
 */
function manaita(input: unknown): unknown[] {
    if (Array.isArray(input)) {
        let flattened: any[] = [];
        for (const v of input) {
            flattened = [...flattened, ...manaita(v)];
        }
        return flattened;
    } else if (input !== null && typeof input === 'object') {
        return manaita(Object.values(input));
    } else {
        return [input];
    }
}

function makeFetchHook<Params, Data>(fetch: (params: Params) => Promise<Response>) {
    return (params: Params) => {
        const [loading, setLoading] = useState(false);
        const [data, setData] = useState<Data | undefined>(undefined);
        const [totalCount, setTotalCount] = useState<number | undefined>(undefined);
        const [error, setError] = useState<any>(null);
        const [reloadCounter, setReloadCounter] = useState(0);

        const reload = useCallback(() => setReloadCounter((v) => v + 1), []);

        useEffect(() => {
            setLoading(true);

            let cancelled = false;
            (async () => {
                try {
                    const response = await fetch(params);
                    if (cancelled) {
                        return;
                    }
                    if (response.ok) {
                        const total = response.headers.get('X-Total-Count');
                        if (total) {
                            setTotalCount(parseInt(total, 10));
                        } else {
                            setTotalCount(undefined);
                        }

                        const data = await response.json();
                        if (cancelled) {
                            return;
                        }

                        setData(data);
                        setLoading(false);
                    }
                    throw new ResponseError(response);
                } catch (e) {
                    console.error(e);
                    setError(e);
                    setLoading(false);
                }
            })();

            return () => {
                cancelled = true;
            };
        }, [...manaita(params), reloadCounter]);

        return { loading, data, setData, totalCount, error, reload };
    };
}

export const useFetchMyProfile = makeFetchHook<void, Tissue.Profile>(() => fetchGet(`/api/me`));

export const useFetchCollections = makeFetchHook<{ username: string }, Tissue.Collection[]>(({ username }) =>
    fetchGet(`/api/users/${username}/collections`)
);

export const useFetchCollectionItems = makeFetchHook<{ id: string; page?: string | null }, Tissue.CollectionItem[]>(
    ({ id, page }) => fetchGet(`/api/collections/${id}/items`, page ? { page } : undefined)
);
