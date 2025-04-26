import React from 'react';
import { createRoot } from 'react-dom/client';
import { RouterProvider, createBrowserRouter } from 'react-router';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { ReactQueryDevtools } from '@tanstack/react-query-devtools';
import { BaseLayout } from './layouts/BaseLayout';
import { Home, loader as homeLoader } from './pages/Home';
import { PublicTimeline, loader as publicTimelineLoader } from './pages/PublicTimeline';
import { NotFound } from './pages/NotFound';
import './App.css';

const queryClient = new QueryClient({
    defaultOptions: {
        queries: {
            retry: false,
            refetchOnWindowFocus: false,
        },
    },
});

const router = createBrowserRouter(
    [
        {
            path: '/',
            element: <BaseLayout />,
            children: [
                { index: true, element: <Home />, loader: homeLoader(queryClient) },
                { path: 'timeline/public', element: <PublicTimeline />, loader: publicTimelineLoader(queryClient) },
                { path: '*', element: <NotFound /> },
            ],
        },
    ],
    { basename: '/app' },
);

createRoot(document.getElementById('app') as HTMLElement).render(
    <QueryClientProvider client={queryClient}>
        <RouterProvider router={router} />
        <ReactQueryDevtools />
    </QueryClientProvider>,
);
