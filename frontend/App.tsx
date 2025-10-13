import React from 'react';
import { createRoot } from 'react-dom/client';
import { RouterProvider, createBrowserRouter, Navigate, RouteObject } from 'react-router';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { ReactQueryDevtools } from '@tanstack/react-query-devtools';
import { ProgressProvider } from '@bprogress/react';
import { ProtectedRoute } from './components/ProtectedRoute';
import { Toaster } from './components/Toaster';
import { BaseLayout } from './layouts/BaseLayout';
import { Top } from './pages/Top';
import { Home } from './pages/Home';
import { loader as homeLoader } from './pages/Home.loader';
import { Collect } from './pages/Collect';
import { loader as collectLoader } from './pages/Collect.loader';
import { CheckinCreate } from './pages/CheckinCreate';
import { loader as checkinCreateLoader } from './pages/CheckinCreate.loader';
import { CheckinDetail, ErrorBoundary as CheckinDetailErrorBoundary } from './pages/CheckinDetail';
import { loader as checkinDetailLoader } from './pages/CheckinDetail.loader';
import { CheckinEdit } from './pages/CheckinEdit';
import { loader as checkinEditLoader } from './pages/CheckinEdit.loader';
import { MyPage } from './pages/MyPage';
import { User, ErrorBoundary as UserErrorBoundary } from './pages/User';
import { loader as userLoader } from './pages/User.loader';
import { UserProfile } from './pages/UserProfile';
import { loader as userProfileLoader } from './pages/UserProfile.loader';
import { UserCheckins } from './pages/UserCheckins';
import { loader as userCheckinsLoader } from './pages/UserCheckins.loader';
import { UserStats } from './pages/UserStats';
import { loader as userStatsLoader } from './pages/UserStats.loader';
import { UserStatsAll } from './pages/UserStatsAll';
import { loader as userStatsAllLoader } from './pages/UserStatsAll.loader';
import { UserStatsYearly } from './pages/UserStatsYearly';
import { loader as userStatsYearlyLoader } from './pages/UserStatsYearly.loader';
import { UserStatsMonthly } from './pages/UserStatsMonthly';
import { loader as userStatsMonthlyLoader } from './pages/UserStatsMonthly.loader';
import { UserLikes, ErrorBoundary as UserLikesErrorBoundary } from './pages/UserLikes';
import { loader as userLikesLoader } from './pages/UserLikes.loader';
import { UserCollections, ErrorBoundary as UserCollectionsErrorBoundary } from './pages/UserCollections';
import { loader as userCollectionsLoader } from './pages/UserCollections.loader';
import { UserCollection, ErrorBoundary as UserCollectionErrorBoundary } from './pages/UserCollection';
import { loader as userCollectionLoader } from './pages/UserCollection.loader';
import { PublicTimeline } from './pages/PublicTimeline';
import { loader as publicTimelineLoader } from './pages/PublicTimeline.loader';
import { Search } from './pages/Search';
import { SearchCheckins, ErrorBoundary as SearchCheckinsErrorBoundary } from './pages/SearchCheckins';
import { loader as searchCheckinsLoader } from './pages/SearchCheckins.loader';
import { SearchCollections, ErrorBoundary as SearchCollectionsErrorBoundary } from './pages/SearchCollections';
import { loader as searchCollectionsLoader } from './pages/SearchCollections.loader';
import { SearchTags, ErrorBoundary as SearchTagsErrorBoundary } from './pages/SearchTags';
import { loader as searchTagsLoader } from './pages/SearchTags.loader';
import { Tags } from './pages/Tags';
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

const protectedRoute: RouteObject = {
    element: <ProtectedRoute />,
    children: [
        {
            path: 'checkin',
            element: <CheckinCreate />,
            loader: checkinCreateLoader(queryClient),
        },
        {
            path: 'checkin/:id/edit',
            element: <CheckinEdit />,
            loader: checkinEditLoader(queryClient),
        },
        {
            path: 'collect',
            element: <Collect />,
            loader: collectLoader(queryClient),
        },
        {
            path: 'user',
            element: <MyPage />,
        },
        {
            path: 'timeline/public',
            element: <PublicTimeline />,
            loader: publicTimelineLoader(queryClient),
        },
        {
            path: 'search',
            element: <Search />,
            children: [
                {
                    path: 'checkin?',
                    element: <SearchCheckins />,
                    errorElement: <SearchCheckinsErrorBoundary />,
                    loader: searchCheckinsLoader(queryClient),
                },
                {
                    path: 'collection',
                    element: <SearchCollections />,
                    errorElement: <SearchCollectionsErrorBoundary />,
                    loader: searchCollectionsLoader(queryClient),
                },
                {
                    path: 'related-tag',
                    element: <SearchTags />,
                    errorElement: <SearchTagsErrorBoundary />,
                    loader: searchTagsLoader(queryClient),
                },
            ],
        },
        {
            path: 'tag',
            element: <Tags />,
            loader: tagsLoader(queryClient),
        },
    ],
};

const router = createBrowserRouter(
    [
        {
            path: '/',
            element: <BaseLayout />,
            children: [
                { index: true, element: <Top /> },
                { path: 'home', element: <Home />, loader: homeLoader(queryClient) },
                {
                    path: 'checkin/:id',
                    element: <CheckinDetail />,
                    errorElement: <CheckinDetailErrorBoundary />,
                    loader: checkinDetailLoader(queryClient),
                },
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
                            path: 'stats',
                            element: <UserStats />,
                            loader: userStatsLoader(queryClient),
                            children: [
                                { index: true, element: <UserStatsAll />, loader: userStatsAllLoader(queryClient) },
                                {
                                    path: ':year',
                                    element: <UserStatsYearly />,
                                    loader: userStatsYearlyLoader(queryClient),
                                },
                                {
                                    path: ':year/:month',
                                    element: <UserStatsMonthly />,
                                    loader: userStatsMonthlyLoader(queryClient),
                                },
                            ],
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
                        {
                            path: 'collections',
                            element: <UserCollections />,
                            errorElement: <UserCollectionsErrorBoundary />,
                            loader: userCollectionsLoader(queryClient),
                        },
                        {
                            path: 'collections/:collectionId',
                            element: <UserCollection />,
                            errorElement: <UserCollectionErrorBoundary />,
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
