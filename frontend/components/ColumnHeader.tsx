import React from 'react';
import { cn } from '../lib/cn';

interface Props {
    className?: string;
    children: React.ReactNode;
}

export const ColumnHeader: React.FC<Props> = ({ className, children }) => (
    <div className={cn('flex items-center min-h-14 text-secondary border-b-1 border-gray-border', className)}>
        {children}
    </div>
);
