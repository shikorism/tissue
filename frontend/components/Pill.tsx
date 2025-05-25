import React from 'react';
import { cn } from '../lib/cn';

interface Props {
    className?: string;
    children: React.ReactNode;
}

export const Pill: React.FC<Props> = ({ className, children }) => {
    return <span className={cn('px-3 py-1 whitespace-nowrap align-baseline rounded-full', className)}>{children}</span>;
};
