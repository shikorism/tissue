import React from 'react';
import { cn } from '../lib/cn';

export const ExternalLink = React.forwardRef<HTMLAnchorElement, React.ComponentPropsWithoutRef<'a'>>(
    ({ className, children, ...props }, ref) => (
        <a
            ref={ref}
            className={cn('text-primary hover:brightness-80 hover:underline', className)}
            target="_blank"
            rel="noopener"
            {...props}
        >
            {children}
        </a>
    ),
);
ExternalLink.displayName = 'ExternalLink';
