import createFetchClient from 'openapi-fetch';
import createClient from 'openapi-react-query';
import type { paths } from './schema';
import { UnauthorizedError, ResponseError } from './errors';

export * from './errors';

export const fetchClient = createFetchClient<paths>({
    baseUrl: '/api/',
});

fetchClient.use({
    async onResponse({ response }) {
        if (response.status === 401) {
            throw new UnauthorizedError(response);
        } else if (response.status >= 500) {
            const body = await response.json();
            const message = body?.error?.message;
            throw new ResponseError(response, message || `${response.status} ${response.statusText}`);
        }
        return response;
    },
});

export const $api = createClient(fetchClient);
