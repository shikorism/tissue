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
        headers,
        ...options,
    });

const fetchWithJson = (path: string, body: any, options: RequestInit = {}) =>
    fetchWrapper(path, {
        body: JSON.stringify(body),
        headers: { 'Content-Type': 'application/json' },
        ...options,
    });

const fetchWithForm = (path: string, body: any, options: RequestInit = {}) =>
    fetchWrapper(path, {
        body: stringify(body),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        ...options,
    });

export const fetchGet = (path: string, params: QueryParams = {}, options: RequestInit = {}) =>
    fetchWrapper(joinParamsToPath(path, params), { method: 'GET', ...options });

export const fetchPostJson = (path: string, body: any, options: RequestInit = {}) =>
    fetchWithJson(path, body, { method: 'POST', ...options });

export const fetchPostForm = (path: string, body: any, options: RequestInit = {}) =>
    fetchWithForm(path, body, { method: 'POST', ...options });

export const fetchPutJson = (path: string, body: any, options: RequestInit = {}) =>
    fetchWithJson(path, body, { method: 'PUT', ...options });

export const fetchPutForm = (path: string, body: any, options: RequestInit = {}) =>
    fetchWithForm(path, body, { method: 'PUT', ...options });

export const fetchDeleteJson = (path: string, body: any, options: RequestInit = {}) =>
    fetchWithJson(path, body, { method: 'DELETE', ...options });

export const fetchDeleteForm = (path: string, body: any, options: RequestInit = {}) =>
    fetchWithForm(path, body, { method: 'DELETE', ...options });
