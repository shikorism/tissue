import createFetchClient from 'openapi-fetch';
import type { paths } from './schema';
import { ResponseError } from './errors';

export const fetchClient = createFetchClient<paths>({
    baseUrl: '/api/',
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
