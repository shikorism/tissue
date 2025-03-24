import React from 'react';
import { GlobalHeader } from '../components/GlobalHeader';
import { Outlet } from 'react-router';

export const BaseLayout: React.FC = () => (
    <>
        <GlobalHeader />
        <Outlet />
    </>
);
