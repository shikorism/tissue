import React from 'react';
import { cn } from '../lib/cn';

const sizes = {
    xs: 'lg:mx-auto lg:max-w-[600px]',
    sm: 'lg:mx-auto lg:max-w-[800px]',
    md: 'lg:mx-auto lg:max-w-[1040px]',
    full: '',
};

interface Props {
    className?: string;
    size?: keyof typeof sizes;
    children: React.ReactNode;
}

export const Container: React.FC<Props> = ({ className, children, size = 'md' }) => (
    <div className={cn('p-4 w-full', sizes[size], className)}>{children}</div>
);
