import React, { ReactNode } from 'react';
import { QueryClient, QueryClientProvider as Provider } from '@tanstack/react-query';
import { ReactQueryDevtools } from '@tanstack/react-query-devtools';

const queryClient = new QueryClient({
    defaultOptions: {
        queries: {
            retry: false,
            refetchOnWindowFocus: false,
        },
    },
});

export const QueryClientProvider: React.FC<{ children?: ReactNode }> = ({ children }) => (
    <Provider client={queryClient}>
        <>
            {children}
            <ReactQueryDevtools initialIsOpen={false} position="bottom-right" />{' '}
        </>
    </Provider>
);
