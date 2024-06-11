import React from 'react';
import { createRoot } from 'react-dom/client';
import { CheckinForm } from './components/CheckinForm';

const initialState = JSON.parse(document.getElementById('initialState')?.textContent as string);
createRoot(document.getElementById('checkinForm') as HTMLElement).render(<CheckinForm initialState={initialState} />);
