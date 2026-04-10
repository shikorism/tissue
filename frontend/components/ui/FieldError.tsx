import React from 'react';

type FieldErrorProps = {
    name?: string;
    label?: string;
    errors?: string[];
};

function replaceNameToLabel(error: string, name?: string, label?: string): string {
    if (name && label) {
        return error.replace(name, label);
    }
    return error;
}

export const FieldError: React.FC<FieldErrorProps> = ({ name, label, errors }) =>
    (errors && errors.length > 0 && (
        <div className="mt-1">
            <small className="text-danger">{replaceNameToLabel(errors[0], name, label)}</small>
        </div>
    )) ||
    null;
