import React from 'react';
import { cn } from '../../lib/cn';

export const Checkbox: React.FC<React.ComponentProps<'input'>> = ({ className, children, ...props }) => {
    const checkbox = <input type="checkbox" className={cn('accent-primary', className)} {...props} />;
    return children ? (
        <label>
            {checkbox}
            <span className="ml-2">{children}</span>
        </label>
    ) : (
        checkbox
    );
};
