import React from 'react';
import { Outlet } from 'react-router';
import { GlobalNavigation } from '../components/GlobalNavigation';
import { GlobalNavigationSm } from '../components/GlobalNavigationSm';

export const BaseLayout: React.FC = () => (
    <>
        <GlobalNavigation />
        <GlobalNavigationSm />
        <div className="max-sm:mb-(--global-nav-height) md:ml-(--global-nav-width)">
            <Outlet />
        </div>
    </>
);
