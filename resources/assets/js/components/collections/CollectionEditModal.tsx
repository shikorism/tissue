import React, { useEffect, useState } from 'react';
import { Button, Form, Modal, ModalProps } from 'react-bootstrap';
import classNames from 'classnames';
import { FieldError } from '../FieldError';
import { ProgressButton } from '../ProgressButton';

export type CollectionFormValues = {
    title: string;
    is_private: boolean;
};

export type CollectionFormErrors = {
    [Property in keyof CollectionFormValues]+?: string[];
};

export class CollectionFormValidationError extends Error {
    errors: CollectionFormErrors;

    constructor(errors: CollectionFormErrors, ...rest: any) {
        super(...rest);
        this.name = 'CollectionFormValidationError';
        this.errors = errors;
    }
}

interface CollectionEditModalProps extends ModalProps {
    mode: 'create' | 'edit';
    initialValues: CollectionFormValues;
    onSubmit: (values: CollectionFormValues) => Promise<void>;
}

export const CollectionEditModal: React.FC<CollectionEditModalProps> = ({
    mode,
    initialValues,
    onSubmit,
    show,
    onHide,
    ...rest
}) => {
    const [values, setValues] = useState(initialValues);
    const [errors, setErrors] = useState<CollectionFormErrors>({});
    const [submitting, setSubmitting] = useState(false);

    useEffect(() => {
        if (show) {
            setValues(initialValues);
            setErrors({});
        }
    }, [show]);

    const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        event.stopPropagation();
        setSubmitting(true);
        try {
            await onSubmit(values);
        } catch (e) {
            if (e instanceof CollectionFormValidationError) {
                setErrors(e.errors);
                return;
            }
            throw e;
        } finally {
            setSubmitting(false);
        }
    };

    const handleHide = () => {
        if (!submitting && onHide) {
            setValues(initialValues);
            setErrors({});
            onHide();
        }
    };

    return (
        <Modal show={show} onHide={handleHide} {...rest}>
            <form onSubmit={handleSubmit}>
                <Modal.Header closeButton>
                    <Modal.Title as="h5">コレクションの{mode === 'create' ? '作成' : '設定'}</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    <div className="form-row">
                        <div className="form-group col-sm-12">
                            <label htmlFor="title">
                                <i className="ti ti-folder" /> タイトル
                            </label>
                            <input
                                type="text"
                                id="title"
                                name="title"
                                className={classNames({ 'form-control': true, 'is-invalid': errors?.title })}
                                required
                                value={values.title}
                                onChange={(e) => setValues((values) => ({ ...values, title: e.target.value }))}
                            />
                            <FieldError name="title" label="タイトル" errors={errors?.title} />
                        </div>
                    </div>
                    <div className="form-row">
                        <div className="form-group col-sm-12">
                            <p className="mb-1">
                                <i className="ti ti-eye" /> 公開設定
                            </p>
                            <Form.Check
                                custom
                                inline
                                type="radio"
                                id="collectionItemVisibilityPublic"
                                label="公開"
                                checked={!values.is_private}
                                onChange={() => setValues((values) => ({ ...values, is_private: false }))}
                            />
                            <Form.Check
                                custom
                                inline
                                type="radio"
                                id="collectionItemVisibilityPrivate"
                                label="非公開"
                                className="mt-2"
                                checked={values.is_private}
                                onChange={() => setValues((values) => ({ ...values, is_private: true }))}
                            />
                        </div>
                    </div>
                </Modal.Body>
                <Modal.Footer>
                    <Button variant="secondary" disabled={submitting} onClick={handleHide}>
                        キャンセル
                    </Button>
                    <ProgressButton
                        label={mode === 'create' ? '作成' : '更新'}
                        inProgress={submitting}
                        type="submit"
                        variant="primary"
                        disabled={submitting}
                    />
                </Modal.Footer>
            </form>
        </Modal>
    );
};
