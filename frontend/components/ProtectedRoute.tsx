import React from 'react';
import { Outlet, useLocation } from 'react-router';
import { useProgress } from '@bprogress/react';
import { useCurrentUser } from './AuthProvider';

export const LOGIN_REDIRECT_KEY = 'login_redirect';

export const ProtectedRoute = () => {
    const { user } = useCurrentUser();
    const location = useLocation();
    const progress = useProgress();

    if (!user) {
        // return <Navigate to="/login" replace />;
        progress.start();
        sessionStorage.setItem(LOGIN_REDIRECT_KEY, location.pathname + location.search);
        window.location.replace('/login');
        return null;
    }

    return <Outlet />;
};
