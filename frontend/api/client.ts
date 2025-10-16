import createFetchClient from 'openapi-fetch';
import Cookies from 'js-cookie';
import type { paths } from './schema';
import { ResponseError } from './errors';

export const fetchClient = createFetchClient<paths>({
    baseUrl: '/api/',
    mode: 'same-origin',
});

// csrf tokenの自動設定
fetchClient.use({
    async onRequest({ request }) {
        const token =
            Cookies.get('XSRF-TOKEN') ||
            document.head.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content;
        if (token) {
            request.headers.set('X-XSRF-TOKEN', token);
        }
        return request;
    },
});

// エラーレスポンスを例外化
fetchClient.use({
    async onResponse({ response }) {
        if (!response.ok) {
            const body = await response.text();
            throw new ResponseError(response, body);
        }
        return response;
    },
});

declare module '@tanstack/react-query' {
    interface Register {
        defaultError: ResponseError;
    }
}
