import React from 'react';
import { cn } from '../../lib/cn';

export const ExternalLink: React.FC<React.ComponentProps<'a'>> = ({ className, children, href, ...props }) => (
    <a
        className={cn('text-primary hover:brightness-80 hover:underline', className)}
        target="_blank"
        rel="noopener noreferrer"
        href={href && /^https?:\/\//.test(href) ? href : undefined}
        {...props}
    >
        {children}
    </a>
);
