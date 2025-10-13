import React from 'react';
import { RouterProvider, createBrowserRouter, Navigate, RouteObject } from 'react-router';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { ReactQueryDevtools } from '@tanstack/react-query-devtools';
import { ProgressProvider } from '@bprogress/react';
import { ProtectedRoute } from './components/ProtectedRoute';
import { Toaster } from './components/Toaster';
import { BaseLayout } from './layouts/BaseLayout';
import { ErrorBoundary } from './pages/ErrorBoundary';
import { loader as homeLoader } from './pages/Home.loader';
import { loader as collectLoader } from './pages/Collect.loader';
import { loader as checkinCreateLoader } from './pages/CheckinCreate.loader';
import { loader as checkinDetailLoader } from './pages/CheckinDetail.loader';
import { loader as checkinEditLoader } from './pages/CheckinEdit.loader';
import { loader as userLoader } from './pages/User.loader';
import { loader as userProfileLoader } from './pages/UserProfile.loader';
import { loader as userCheckinsLoader } from './pages/UserCheckins.loader';
import { loader as userStatsLoader } from './pages/UserStats.loader';
import { loader as userStatsAllLoader } from './pages/UserStatsAll.loader';
import { loader as userStatsYearlyLoader } from './pages/UserStatsYearly.loader';
import { loader as userStatsMonthlyLoader } from './pages/UserStatsMonthly.loader';
import { loader as userLikesLoader } from './pages/UserLikes.loader';
import { loader as userCollectionsLoader } from './pages/UserCollections.loader';
import { loader as userCollectionLoader } from './pages/UserCollection.loader';
import { loader as publicTimelineLoader } from './pages/PublicTimeline.loader';
import { loader as searchCheckinsLoader } from './pages/SearchCheckins.loader';
import { loader as searchCollectionsLoader } from './pages/SearchCollections.loader';
import { loader as searchTagsLoader } from './pages/SearchTags.loader';
import { loader as tagsLoader } from './pages/Tags.loader';
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

const convert = <T extends { default: React.ComponentType; ErrorBoundary?: React.ComponentType }>(mod: T) => {
    const { default: Component, ErrorBoundary } = mod;
    return { Component, ErrorBoundary };
};

const protectedRoute: RouteObject = {
    element: <ProtectedRoute />,
    children: [
        {
            path: 'home',
            lazy: () => import('./pages/Home').then(convert),
            loader: homeLoader(queryClient),
        },
        {
            path: 'checkin',
            lazy: () => import('./pages/CheckinCreate').then(convert),
            loader: checkinCreateLoader(queryClient),
        },
        {
            path: 'checkin/:id/edit',
            lazy: () => import('./pages/CheckinEdit').then(convert),
            loader: checkinEditLoader(queryClient),
        },
        {
            path: 'collect',
            lazy: () => import('./pages/Collect').then(convert),
            loader: collectLoader(queryClient),
        },
        {
            path: 'user',
            lazy: () => import('./pages/MyPage').then(convert),
        },
        {
            path: 'timeline/public',
            lazy: () => import('./pages/PublicTimeline').then(convert),
            loader: publicTimelineLoader(queryClient),
        },
        {
            path: 'search',
            lazy: () => import('./pages/Search').then(convert),
            children: [
                {
                    path: 'checkin?',
                    lazy: () => import('./pages/SearchCheckins').then(convert),
                    loader: searchCheckinsLoader(queryClient),
                },
                {
                    path: 'collection',
                    lazy: () => import('./pages/SearchCollections').then(convert),
                    loader: searchCollectionsLoader(queryClient),
                },
                {
                    path: 'related-tag',
                    lazy: () => import('./pages/SearchTags').then(convert),
                    loader: searchTagsLoader(queryClient),
                },
            ],
        },
        {
            path: 'tag',
            lazy: () => import('./pages/Tags').then(convert),
            loader: tagsLoader(queryClient),
        },
    ],
};

const router = createBrowserRouter(
    [
        {
            path: '/',
            element: <BaseLayout />,
            errorElement: <ErrorBoundary />,
            children: [
                { index: true, lazy: () => import('./pages/Top').then(convert) },
                {
                    path: 'checkin/:id',
                    lazy: () => import('./pages/CheckinDetail').then(convert),
                    loader: checkinDetailLoader(queryClient),
                },
                {
                    path: 'user/:username',
                    lazy: () => import('./pages/User').then(convert),
                    loader: userLoader(queryClient),
                    children: [
                        {
                            index: true,
                            lazy: () => import('./pages/UserProfile').then(convert),
                            loader: userProfileLoader(queryClient),
                        },
                        {
                            path: 'checkins/:year?/:month?/:date?',
                            lazy: () => import('./pages/UserCheckins').then(convert),
                            loader: userCheckinsLoader(queryClient),
                        },
                        {
                            path: 'stats',
                            lazy: () => import('./pages/UserStats').then(convert),
                            loader: userStatsLoader(queryClient),
                            children: [
                                {
                                    index: true,
                                    lazy: () => import('./pages/UserStatsAll').then(convert),
                                    loader: userStatsAllLoader(queryClient),
                                },
                                {
                                    path: ':year',
                                    lazy: () => import('./pages/UserStatsYearly').then(convert),
                                    loader: userStatsYearlyLoader(queryClient),
                                },
                                {
                                    path: ':year/:month',
                                    lazy: () => import('./pages/UserStatsMonthly').then(convert),
                                    loader: userStatsMonthlyLoader(queryClient),
                                },
                            ],
                        },
                        {
                            path: 'likes',
                            lazy: () => import('./pages/UserLikes').then(convert),
                            loader: userLikesLoader(queryClient),
                        },
                        {
                            path: 'okazu',
                            element: <Navigate to="../checkins?link=1" replace />,
                        },
                        {
                            path: 'collections',
                            lazy: () => import('./pages/UserCollections').then(convert),
                            loader: userCollectionsLoader(queryClient),
                        },
                        {
                            path: 'collections/:collectionId',
                            lazy: () => import('./pages/UserCollection').then(convert),
                            loader: userCollectionLoader(queryClient),
                        },
                    ],
                },
                protectedRoute,
                { path: '*', element: <NotFound /> },
            ],
        },
    ],
    {},
);

export const App = () => (
    <QueryClientProvider client={queryClient}>
        <ProgressProvider color="#e53fb1">
            <RouterProvider router={router} />
            <Toaster />
            <ReactQueryDevtools />
        </ProgressProvider>
    </QueryClientProvider>
);
