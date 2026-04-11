import React from 'react';
import { cn } from '../../lib/cn';

interface Props extends React.ComponentProps<'input'> {
    small?: boolean;
}

export const SearchInput: React.FC<Props> = ({ small, className, ...props }) => (
    <div className="relative">
        <input
            className={cn(
                'block w-full rounded-full border pl-10 pr-4 py-2 transition duration-150 ease-in-out focus:outline-none focus:ring-4',
                'border-neutral-300 focus:border-primary-400 focus:ring-primary-400/25',
                small ? 'pl-8 text-sm' : 'pl-10',
                className,
            )}
            required
            {...props}
        />
        <i
            className={cn(
                'ti ti-search text-neutral-500 absolute left-3 top-1/2 -translate-y-1/2',
                small ? 'text-base' : 'text-xl',
            )}
        />
    </div>
);
