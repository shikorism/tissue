import React, { ComponentPropsWithoutRef, useEffect, useState } from 'react';
import { cn } from '../../lib/cn';
import { Button } from '../../components/Button';
import { FieldError } from '../../components/FieldError';
import { Modal, ModalHeader, ModalBody, ModalFooter } from '../../components/Modal';
import { ProgressButton } from '../../components/ProgressButton';

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

interface CollectionEditModalProps extends Omit<ComponentPropsWithoutRef<typeof Modal>, 'children'> {
    mode: 'create' | 'edit';
    initialValues: CollectionFormValues;
    onSubmit: (values: CollectionFormValues) => Promise<void>;
}

export const CollectionEditModal: React.FC<CollectionEditModalProps> = ({
    mode,
    initialValues,
    onSubmit,
    isOpen,
    onClose,
    ...rest
}) => {
    const [values, setValues] = useState(initialValues);
    const [errors, setErrors] = useState<CollectionFormErrors>({});
    const [submitting, setSubmitting] = useState(false);

    useEffect(() => {
        if (isOpen) {
            setValues(initialValues);
            setErrors({});
        }
    }, [isOpen]);

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

    const handleClose = () => {
        if (!submitting && onClose) {
            setValues(initialValues);
            setErrors({});
            onClose();
        }
    };

    return (
        <Modal isOpen={isOpen} onClose={handleClose} {...rest}>
            <form onSubmit={handleSubmit}>
                <ModalHeader closeButton>コレクションの{mode === 'create' ? '作成' : '設定'}</ModalHeader>
                <ModalBody>
                    <div className="flex flex-col gap-4">
                        <div>
                            <label htmlFor="title" className="block mb-2">
                                <i className="ti ti-folder" /> タイトル
                            </label>
                            <input
                                type="text"
                                id="title"
                                name="title"
                                className={cn(
                                    'block w-full rounded border px-3 py-2 transition duration-150 ease-in-out focus:outline-none focus:ring-4',
                                    errors?.title
                                        ? 'border-danger focus:ring-danger/25'
                                        : 'border-neutral-300 focus:border-primary-400 focus:ring-primary-400/25',
                                )}
                                required
                                value={values.title}
                                onChange={(e) => setValues((values) => ({ ...values, title: e.target.value }))}
                            />
                            <FieldError name="title" label="タイトル" errors={errors?.title} />
                        </div>
                        <div>
                            <p className="mb-2">
                                <i className="ti ti-eye" /> 公開設定
                            </p>
                            <div className="inline mr-4">
                                <input
                                    className="accent-primary"
                                    type="radio"
                                    id="collectionItemVisibilityPublic"
                                    name="is_private"
                                    checked={!values.is_private}
                                    onChange={() => setValues((values) => ({ ...values, is_private: false }))}
                                />
                                <label htmlFor="collectionItemVisibilityPublic" className="ml-2">
                                    公開
                                </label>
                            </div>
                            <div className="inline">
                                <input
                                    className="accent-primary"
                                    type="radio"
                                    id="collectionItemVisibilityPrivate"
                                    name="is_private"
                                    checked={values.is_private}
                                    onChange={() => setValues((values) => ({ ...values, is_private: true }))}
                                />
                                <label htmlFor="collectionItemVisibilityPrivate" className="ml-2">
                                    非公開
                                </label>
                            </div>
                        </div>
                    </div>
                </ModalBody>
                <ModalFooter>
                    <Button disabled={submitting} onClick={handleClose}>
                        キャンセル
                    </Button>
                    <ProgressButton
                        label={mode === 'create' ? '作成' : '更新'}
                        inProgress={submitting}
                        type="submit"
                        variant="primary"
                        disabled={submitting}
                    />
                </ModalFooter>
            </form>
        </Modal>
    );
};
