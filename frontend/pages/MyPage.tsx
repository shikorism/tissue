import React from 'react';
import { useCurrentUser } from '../components/AuthProvider';
import { Navigate } from 'react-router';

export const MyPage = () => {
    const { user } = useCurrentUser();
    if (!user) {
        throw new Error('invalid context');
    }

    return <Navigate to={`/user/${user.name}`} replace />;
};
