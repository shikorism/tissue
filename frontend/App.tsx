import React from 'react';
import { createRoot } from 'react-dom/client';
import { RouterProvider, createBrowserRouter } from 'react-router';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { ReactQueryDevtools } from '@tanstack/react-query-devtools';
import { ProgressProvider } from '@bprogress/react';
import { BaseLayout } from './layouts/BaseLayout';
import { Home, loader as homeLoader } from './pages/Home';
import { User, ErrorBoundary as UserErrorBoundary } from './pages/User';
import { loader as userLoader } from './pages/User.loader';
import { UserProfile } from './pages/UserProfile';
import { loader as userProfileLoader } from './pages/UserProfile.loader';
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
                {
                    path: 'user/:username',
                    element: <User />,
                    errorElement: <UserErrorBoundary />,
                    loader: userLoader(queryClient),
                    children: [{ index: true, element: <UserProfile />, loader: userProfileLoader(queryClient) }],
                },
                {
                    path: 'timeline/public',
                    element: <PublicTimeline />,
                    loader: publicTimelineLoader(queryClient),
                },
                { path: '*', element: <NotFound /> },
            ],
        },
    ],
    { basename: '/app' },
);

createRoot(document.getElementById('app') as HTMLElement).render(
    <QueryClientProvider client={queryClient}>
        <ProgressProvider color="#e53fb1">
            <RouterProvider router={router} />
            <ReactQueryDevtools />
        </ProgressProvider>
    </QueryClientProvider>,
);
