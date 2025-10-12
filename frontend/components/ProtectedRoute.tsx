import React from 'react';
import { Outlet, useLocation } from 'react-router';
import { useProgress } from '@bprogress/react';
import { useCurrentUser } from './AuthProvider';

export const ProtectedRoute = () => {
    const { user } = useCurrentUser();
    const location = useLocation();
    const progress = useProgress();

    if (!user) {
        // return <Navigate to="/login" replace />;
        progress.start();
        const params = new URLSearchParams({
            next: location.pathname + location.search,
        });
        window.location.replace(`/login?${params}`);
        return null;
    }

    return <Outlet />;
};
