import { useEffect, useState, useCallback, useRef } from 'react';
import { fetchGet, ResponseError } from './fetch';

/**
 * @example
 *   flat([1, 2, [30, [40, 50]]]) // => [1, 2, 30, 40, 50]
 *   flat({ foo: 'hoge', bar: 'fuga', baz: [10, 20, 30] }) // => ['hoge', 'fuga', 10, 20, 30]
 */
function flat(input: unknown): unknown[] {
    if (Array.isArray(input)) {
        let flattened: any[] = [];
        for (const v of input) {
            flattened = [...flattened, ...flat(v)];
        }
        return flattened;
    } else if (input !== null && typeof input === 'object') {
        return flat(Object.values(input));
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

        const resolvedCallbacksRef = useRef<((data: Data) => void)[]>([]);
        const rejectedCallbacksRef = useRef<((reason?: any) => void)[]>([]);
        const reload = useCallback(() => {
            setReloadCounter((v) => v + 1);
            return new Promise<Data>((resolved, rejected) => {
                resolvedCallbacksRef.current.push(resolved);
                rejectedCallbacksRef.current.push(rejected);
            });
        }, []);

        const clear = useCallback(() => {
            setData(undefined);
            setTotalCount(undefined);
            setError(undefined);
            setReloadCounter(0);
        }, []);

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

                        resolvedCallbacksRef.current.forEach((resolved) => resolved(data));
                        resolvedCallbacksRef.current.splice(0);
                        rejectedCallbacksRef.current.splice(0);
                    }
                    throw new ResponseError(response);
                } catch (e) {
                    console.error(e);
                    setError(e);
                    setLoading(false);

                    rejectedCallbacksRef.current.forEach((rejected) => rejected(e));
                    resolvedCallbacksRef.current.splice(0);
                    rejectedCallbacksRef.current.splice(0);
                }
            })();

            return () => {
                cancelled = true;
            };
        }, [...flat(params), reloadCounter]);

        return { loading, data, setData, totalCount, error, reload, clear };
    };
}

export const useFetchMyProfile = makeFetchHook<void, Tissue.Profile>(() => fetchGet(`/api/me`));

export const useFetchCollections = makeFetchHook<{ username: string }, Tissue.Collection[]>(({ username }) =>
    fetchGet(`/api/users/${username}/collections`)
);

export const useFetchCollection = makeFetchHook<{ id: string }, Tissue.Collection>(({ id }) =>
    fetchGet(`/api/collections/${id}`)
);

export const useFetchCollectionItems = makeFetchHook<{ id: string; page?: string | null }, Tissue.CollectionItem[]>(
    ({ id, page }) => fetchGet(`/api/collections/${id}/items`, page ? { page } : undefined)
);
