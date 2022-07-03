import React from 'react';
import { Button, ButtonProps, Spinner } from 'react-bootstrap';

interface ProgressButtonProps extends ButtonProps {
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
            <Spinner className="mr-1" as="span" animation="border" size="sm" role="status" aria-hidden="true" />
            {inProgressLabel}
        </Button>
    ) : (
        <Button {...rest}>{label}</Button>
    );
