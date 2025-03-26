import React from 'react';
import { createRoot } from 'react-dom/client';
import { RouterProvider, createBrowserRouter } from 'react-router';
import { QueryClientProvider } from './components/QueryClientProvider';
import { BaseLayout } from './layouts/BaseLayout';
import { Home } from './pages/Home';
import { NotFound } from './pages/NotFound';
import './App.css';

const router = createBrowserRouter([
    {
        path: 'app',
        element: <BaseLayout />,
        children: [
            { index: true, element: <Home /> },
            { path: '*', element: <NotFound /> },
        ],
    },
]);

createRoot(document.getElementById('app') as HTMLElement).render(
    <QueryClientProvider>
        <RouterProvider router={router} />
    </QueryClientProvider>,
);
