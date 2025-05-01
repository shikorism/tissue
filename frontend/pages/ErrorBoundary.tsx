import React from 'react';
import { useRouteError } from 'react-router';
import { ResponseError } from '../api/errors';
import { NotFound } from './NotFound';

export const ErrorBoundary: React.FC = () => {
    const error = useRouteError();

    if (error instanceof ResponseError) {
        if (error.response.status === 404) {
            return <NotFound />;
        }
    }

    // TODO: あとで考える
    throw error;
};
