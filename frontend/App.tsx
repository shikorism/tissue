import React from 'react';
import { createRoot } from 'react-dom/client';
import { BrowserRouter, Route, Routes } from 'react-router';
import { QueryClientProvider } from './components/QueryClientProvider';
import { BaseLayout } from './layouts/BaseLayout';
import { Home } from './pages/Home';
import { NotFound } from './pages/NotFound';
import './App.css';

createRoot(document.getElementById('app') as HTMLElement).render(
    <QueryClientProvider>
        <BrowserRouter>
            <Routes>
                <Route path="app" element={<BaseLayout />}>
                    <Route index element={<Home />} />
                    <Route path="*" element={<NotFound />} />
                </Route>
            </Routes>
        </BrowserRouter>
    </QueryClientProvider>,
);
