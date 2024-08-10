import React from 'react';
import { createRoot } from 'react-dom/client';
import { CheckinForm } from './components/CheckinForm';
import { QueryClientProvider } from './query';

const initialState = JSON.parse(document.getElementById('initialState')?.textContent as string);
createRoot(document.getElementById('checkinForm') as HTMLElement).render(
    <QueryClientProvider>
        <CheckinForm initialState={initialState} />
    </QueryClientProvider>,
);
