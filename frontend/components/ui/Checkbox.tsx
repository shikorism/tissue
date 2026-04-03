import React from 'react';
import { cn } from '../../lib/cn';

export const Checkbox: React.FC<React.ComponentProps<'input'>> = ({ className, children, ...props }) =>
    children ? (
        <label>
            <Checkbox {...props} />
            <span className="ml-2">{children}</span>
        </label>
    ) : (
        <input type="checkbox" className={cn('accent-primary', className)} {...props} />
    );
