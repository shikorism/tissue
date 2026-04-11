import React from 'react';
import { ErrorView } from '../components/ErrorView';

export const NotFound: React.FC = () => (
    <ErrorView title="404" subtitle="Not Found" message="お探しのページが見つかりませんでした。" />
);
