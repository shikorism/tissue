import React, { ComponentPropsWithoutRef, useEffect, useState } from 'react';
import { Button } from '../../components/ui/Button';
import { Input } from '../../components/ui/Input';
import { Radio } from '../../components/ui/Radio';
import { FieldError } from '../../components/ui/FieldError';
import { Modal, ModalHeader, ModalBody, ModalFooter } from '../../components/ui/Modal';
import { ProgressButton } from '../../components/ui/ProgressButton';

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
                            <Input
                                id="title"
                                name="title"
                                error={!!errors?.title}
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
                            <Radio
                                className="mr-4"
                                name="is_private"
                                checked={!values.is_private}
                                onChange={() => setValues((values) => ({ ...values, is_private: false }))}
                            >
                                公開
                            </Radio>
                            <Radio
                                name="is_private"
                                checked={values.is_private}
                                onChange={() => setValues((values) => ({ ...values, is_private: true }))}
                            >
                                非公開
                            </Radio>
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
