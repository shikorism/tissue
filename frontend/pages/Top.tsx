import React from 'react';
import { Navigate } from 'react-router';
import { useProgress } from '@bprogress/react';
import { useCurrentUser } from '../components/AuthProvider';

export const Top: React.FC = () => {
    const { user } = useCurrentUser();
    const progress = useProgress();

    if (user) {
        return <Navigate to="/home" replace />;
    } else {
        progress.start();
        window.location.replace('/?nl'); // MPAから再読み込み。ブラウザバック時にSPAの履歴を使わないよう適当なパラメータを付与。
        return null;
    }
};
