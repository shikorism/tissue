import React from 'react';
import { cn } from '../../lib/cn';

export const Select: React.FC<React.ComponentProps<'select'>> = ({ className, children, ...props }) => (
    <select
        className={cn(
            'w-full p-2 rounded transition duration-150 ease-in-out focus:outline-none focus:ring-4 border border-neutral-300 focus:border-primary-400 focus:ring-primary-400/25',
            className,
        )}
        {...props}
    >
        {children}
    </select>
);
