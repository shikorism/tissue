import React from 'react';
import { useRouteError } from 'react-router';
import { ErrorView } from '../components/ErrorView';

export const ErrorBoundary: React.FC = () => {
    const error = useRouteError();

    return <ErrorView error={error} />;
};
