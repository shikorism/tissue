import { stringify } from 'qs';

const token = document.head.querySelector<HTMLMetaElement>('meta[name="csrf-token"]');
if (!token) {
    console.error('CSRF token not found');
}

const headers = {
    'X-CSRF-TOKEN': token?.content ?? '',
};

type QueryParams = { [key: string]: string };

const joinParamsToPath = (path: string, params: QueryParams) =>
    Object.keys(params).length === 0 ? path : `${path}?${stringify(params)}`;

const fetchWrapper = (path: string, options: RequestInit = {}) =>
    fetch(path, {
        credentials: 'same-origin',
        ...options,
        headers: { ...headers, ...options.headers },
    });

const fetchWithJson = (path: string, body?: any, options: RequestInit = {}) =>
    fetchWrapper(path, {
        ...options,
        body: body && JSON.stringify(body),
        headers: { 'Content-Type': 'application/json', ...options.headers },
    });

const fetchWithForm = (path: string, body?: any, options: RequestInit = {}) =>
    fetchWrapper(path, {
        ...options,
        body: body && stringify(body),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', ...options.headers },
    });

export const fetchGet = (path: string, params: QueryParams = {}, options: RequestInit = {}) =>
    fetchWrapper(joinParamsToPath(path, params), { method: 'GET', ...options });

export const fetchPostJson = (path: string, body?: any, options: RequestInit = {}) =>
    fetchWithJson(path, body, { method: 'POST', ...options });

export const fetchPostForm = (path: string, body?: any, options: RequestInit = {}) =>
    fetchWithForm(path, body, { method: 'POST', ...options });

export const fetchPutJson = (path: string, body?: any, options: RequestInit = {}) =>
    fetchWithJson(path, body, { method: 'PUT', ...options });

export const fetchPutForm = (path: string, body?: any, options: RequestInit = {}) =>
    fetchWithForm(path, body, { method: 'PUT', ...options });

export const fetchDeleteJson = (path: string, body?: any, options: RequestInit = {}) =>
    fetchWithJson(path, body, { method: 'DELETE', ...options });

export const fetchDeleteForm = (path: string, body?: any, options: RequestInit = {}) =>
    fetchWithForm(path, body, { method: 'DELETE', ...options });

export class ResponseError extends Error {
    response: Response;

    constructor(response: Response, ...rest: any) {
        super(...rest);
        this.name = 'ResponseError';
        this.response = response;
    }
}
