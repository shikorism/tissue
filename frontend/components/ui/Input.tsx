import React from 'react';
import { cn } from '../../lib/cn';

interface Props extends React.ComponentProps<'input'> {
    error?: boolean;
}

export const Input: React.FC<Props> = ({ className, error, ...props }) => (
    <input
        type="text"
        className={cn(
            'block w-full rounded border px-3 py-2 transition duration-150 ease-in-out focus:outline-none focus:ring-4 disabled:text-neutral-600 disabled:bg-neutral-200',
            error
                ? 'border-danger focus:ring-danger/25'
                : 'border-neutral-300 focus:border-primary-400 focus:ring-primary-400/25',
            className,
        )}
        {...props}
    />
);
