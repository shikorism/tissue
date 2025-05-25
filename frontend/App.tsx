import React from 'react';
import { createRoot } from 'react-dom/client';
import { RouterProvider, createBrowserRouter, Navigate } from 'react-router';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { ReactQueryDevtools } from '@tanstack/react-query-devtools';
import { ProgressProvider } from '@bprogress/react';
import { BaseLayout } from './layouts/BaseLayout';
import { Home, loader as homeLoader } from './pages/Home';
import { User, ErrorBoundary as UserErrorBoundary } from './pages/User';
import { loader as userLoader } from './pages/User.loader';
import { UserProfile } from './pages/UserProfile';
import { loader as userProfileLoader } from './pages/UserProfile.loader';
import { UserCheckins } from './pages/UserCheckins';
import { loader as userCheckinsLoader } from './pages/UserCheckins.loader';
import { UserLikes, ErrorBoundary as UserLikesErrorBoundary } from './pages/UserLikes';
import { loader as userLikesLoader } from './pages/UserLikes.loader';
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
                    children: [
                        { index: true, element: <UserProfile />, loader: userProfileLoader(queryClient) },
                        {
                            path: 'checkins/:year?/:month?/:date?',
                            element: <UserCheckins />,
                            loader: userCheckinsLoader(queryClient),
                        },
                        {
                            path: 'likes',
                            element: <UserLikes />,
                            errorElement: <UserLikesErrorBoundary />,
                            loader: userLikesLoader(queryClient),
                        },
                        {
                            path: 'okazu',
                            element: <Navigate to="../checkins?link=1" replace />,
                        },
                    ],
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
