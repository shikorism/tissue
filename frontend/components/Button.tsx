import React from 'react';
import { cn } from '../lib/cn';

interface Props {
    as?: React.ElementType;
    className?: string;
    variant?: 'primary' | 'danger';
}

export const Button = <Element extends React.ElementType>({
    as: Component = 'button',
    className,
    variant,
    ...props
}: Props & React.ComponentPropsWithoutRef<Element>) => (
    <Component
        type={Component === 'button' ? 'button' : undefined}
        {...props}
        className={cn(
            'px-3 py-1.5 rounded text-neutral-700 bg-neutral-200 hover:bg-neutral-300 disabled:text-neutral-400 disabled:bg-neutral-100 cursor-pointer select-none transition-colors',
            variant === 'primary' &&
                'text-white bg-primary-500 hover:bg-primary-700 disabled:bg-primary-300 disabled:text-white',
            variant === 'danger' && 'text-white bg-rose-500 hover:bg-rose-700 disabled:bg-rose-300 disabled:text-white',
            className,
        )}
    />
);
