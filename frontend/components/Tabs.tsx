import React from 'react';
import { cn } from '../lib/cn';

interface TabsProps {
    className?: string;
    children?: React.ReactNode;
}

export const Tabs: React.FC<TabsProps> = ({ className, children }) => {
    return <ul className={cn('-mb-px flex', className)}>{children}</ul>;
};

interface TabProps {
    active?: boolean;
    children?: React.ReactNode;
}

export const Tab: React.FC<TabProps> = ({ active, children }) => {
    return (
        <li
            className={cn(
                'text-secondary border-b-2 border-transparent transition-colors shrink-0',
                active ? 'text-primary border-primary' : 'hover:border-secondary',
            )}
        >
            {children}
        </li>
    );
};
