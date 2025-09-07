import createFetchClient from 'openapi-fetch';
import type { paths } from './schema';
import { ResponseError } from './errors';

const token = document.head.querySelector<HTMLMetaElement>('meta[name="csrf-token"]');
if (!token) {
    console.error('CSRF token not found');
}

export const fetchClient = createFetchClient<paths>({
    baseUrl: '/api/',
    headers: {
        'X-CSRF-TOKEN': token?.content,
    },
});

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
