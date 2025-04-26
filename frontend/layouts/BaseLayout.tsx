import React, { useEffect } from 'react';
import { Outlet, useNavigation } from 'react-router';
import { useProgress } from '@bprogress/react';
import { AuthProvider, useCurrentUser } from '../components/AuthProvider';
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
        <AuthProvider>
            <AwaitAuth>
                <GlobalNavigation />
                <GlobalNavigationSm />
                <div className="max-sm:mb-(--global-nav-height) md:ml-(--global-nav-width)">
                    <Outlet />
                </div>
            </AwaitAuth>
        </AuthProvider>
    );
};

const AwaitAuth: React.FC<{ children: React.ReactNode }> = ({ children }) => {
    const { isLoading } = useCurrentUser();
    if (isLoading) {
        return null;
    }

    return children;
};
