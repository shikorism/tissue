import * as React from 'react';

type FieldErrorProps = {
    errors?: string[];
};

export const FieldError: React.FC<FieldErrorProps> = ({ errors }) =>
    (errors && errors.length > 0 && <div className="invalid-feedback">{errors[0]}</div>) || null;

export const StandaloneFieldError: React.FC<FieldErrorProps> = ({ errors }) =>
    (errors && errors.length > 0 && (
        <div className="form-group col-sm-12" style={{ marginTop: '-1rem' }}>
            <small className="text-danger">{errors[0]}</small>
        </div>
    )) ||
    null;
