import React from 'react';
import { Button } from './Button';

interface ProgressButtonProps extends React.ComponentPropsWithoutRef<typeof Button> {
    label: string;
    inProgressLabel?: string;
    inProgress?: boolean;
}

export const ProgressButton: React.FC<ProgressButtonProps> = ({
    label,
    inProgressLabel = `${label}中…`,
    inProgress,
    ...rest
}) =>
    inProgress ? (
        <Button {...rest} disabled>
            <i className="mr-1 ti ti-loader animate-spin" />
            {inProgressLabel}
        </Button>
    ) : (
        <Button {...rest}>{label}</Button>
    );
