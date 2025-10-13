import React, { useState } from 'react';
import { Navigate } from 'react-router';
import { useProgress } from '@bprogress/react';
import { useCurrentUser } from '../components/AuthProvider';
import { LOGIN_REDIRECT_KEY } from '../components/ProtectedRoute';

const validateRedirectTo = (next: string | null): next is string => {
    next = next?.trim() || '';
    return !!next && next.startsWith('/') && !next.startsWith('//');
};

export const Top: React.FC = () => {
    const { user } = useCurrentUser();
    const progress = useProgress();
    const [redirectTo] = useState(sessionStorage.getItem(LOGIN_REDIRECT_KEY));

    if (user) {
        if (validateRedirectTo(redirectTo)) {
            sessionStorage.removeItem(LOGIN_REDIRECT_KEY);
            return <Navigate to={redirectTo} replace />;
        }
        return <Navigate to="/home" replace />;
    } else {
        progress.start();
        window.location.replace('/?nl'); // MPAから再読み込み。ブラウザバック時にSPAの履歴を使わないよう適当なパラメータを付与。
        return null;
    }
};
