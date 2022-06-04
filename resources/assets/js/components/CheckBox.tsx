import React from 'react';

type CheckboxProps = {
    id: string;
    name: string;
    className?: string;
    checked?: boolean;
    onChange?: (newValue: boolean) => void;
    children?: React.ReactNode;
};

export const CheckBox: React.FC<CheckboxProps> = ({ id, name, className, checked, onChange, children }) => (
    <div className={`custom-control custom-checkbox ${className}`}>
        <input
            id={id}
            name={name}
            type="checkbox"
            className="custom-control-input"
            checked={checked}
            onChange={(e) => onChange && onChange(e.target.checked)}
        />
        <label className="custom-control-label" htmlFor={id}>
            {children}
        </label>
    </div>
);
