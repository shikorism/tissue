import React, { useEffect } from 'react';
import { Outlet, useNavigation } from 'react-router';
import { useProgress } from '@bprogress/react';
import { GlobalNavigation } from '../components/GlobalNavigation';
import { GlobalNavigationSm } from '../components/GlobalNavigationSm';

export const BaseLayout: React.FC = () => {
    const { start, stop } = useProgress();
    const navigation = useNavigation();
    const isNavigating = Boolean(navigation.location);
    useEffect(() => {
        if (isNavigating) {
            start();
        } else {
            stop();
        }
    }, [isNavigating]);

    return (
        <>
            <GlobalNavigation />
            <GlobalNavigationSm />
            <div className="max-sm:mb-(--global-nav-height) md:ml-(--global-nav-width)">
                <Outlet />
            </div>
        </>
    );
};
