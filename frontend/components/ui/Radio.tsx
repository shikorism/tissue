import React from 'react';
import { cn } from '../../lib/cn';

interface Props extends React.ComponentProps<'input'> {
    inputClassName?: string;
}

export const Radio: React.FC<Props> = ({ className, inputClassName, children, ...props }) => (
    <label className={className}>
        <input className={cn('accent-primary', inputClassName)} type="radio" {...props} />
        <span className="ml-2">{children}</span>
    </label>
);
