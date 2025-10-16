import React, { ComponentPropsWithoutRef, useEffect, useState } from 'react';
import { Modal, ModalBody, ModalHeader, ModalFooter } from '../../components/Modal';
import { components } from '../../api/schema';
import { Button } from '../../components/Button';
import { ProgressButton } from '../../components/ProgressButton';
import { cn } from '../../lib/cn';
import { FieldError } from '../../components/FieldError';
import { TagInput } from '../../components/TagInput';
import { MetadataPreview } from '../../components/MetadataPreview';
import { usePatchCollectionItem } from '../../api/mutation';
import { ResponseError } from '../../api/errors';
import { toast } from 'sonner';

type ItemEditFormValues = {
    tags: string[];
    note: string;
};

type ItemEditFormErrors = {
    [Property in keyof ItemEditFormValues]+?: string[];
};

interface CollectionItemEditModalProps extends Omit<ComponentPropsWithoutRef<typeof Modal>, 'children'> {
    item: components['schemas']['CollectionItem'];
}

export const CollectionItemEditModal: React.FC<CollectionItemEditModalProps> = ({ item, isOpen, onClose, ...rest }) => {
    const [values, setValues] = useState<ItemEditFormValues>({
        note: item.note,
        tags: item.tags,
    });
    const [errors, setErrors] = useState<ItemEditFormErrors>({});
    const patchCollectionItem = usePatchCollectionItem();

    useEffect(() => {
        if (isOpen) {
            setValues({
                note: item.note,
                tags: item.tags,
            });
            setErrors({});
        }
    }, [isOpen]);

    const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        try {
            await patchCollectionItem.mutateAsync({
                collectionId: item.collection.id,
                collectionItemId: item.id,
                body: values,
            });
            toast.success('更新しました');
            onClose();
        } catch (e) {
            if (e instanceof ResponseError && e.response.status === 422) {
                if (e.error?.violations) {
                    const errors: ItemEditFormErrors = {};
                    for (const violation of e.error.violations) {
                        const field = violation.field as keyof ItemEditFormErrors;
                        (errors[field] || (errors[field] = [])).push(violation.message);
                    }
                    setErrors(errors);
                    return;
                } else if (e.error?.message) {
                    toast.error(e.error.message);
                    return;
                }
            }
        }
    };

    const handleClose = () => {
        if (!patchCollectionItem.isPending) {
            onClose();
        }
    };

    return (
        <Modal isOpen={isOpen} onClose={handleClose} {...rest}>
            <form onSubmit={handleSubmit}>
                <ModalHeader closeButton>編集</ModalHeader>
                <ModalBody>
                    <div className="flex flex-col gap-4">
                        <div>
                            <label htmlFor="link" className="block mb-2">
                                <i className="ti ti-link" /> オカズリンク
                            </label>
                            <input
                                type="text"
                                id="link"
                                name="link"
                                className="block w-full rounded border px-3 py-2 text-neutral-600 border-neutral-300 bg-neutral-200"
                                disabled
                                placeholder="http://..."
                                value={item.link}
                            />
                        </div>
                        <MetadataPreview
                            link={item.link}
                            tags={values.tags}
                            onClickTag={(v) => setValues({ ...values, tags: values.tags.concat(v) })}
                        />
                        <div>
                            <label htmlFor="tagInput" className="block mb-2">
                                <i className="ti ti-tags" /> タグ
                            </label>
                            <TagInput
                                id="tagInput"
                                name="tags"
                                values={values.tags}
                                isInvalid={!!errors?.tags}
                                onChange={(v) => setValues((values) => ({ ...values, tags: v }))}
                            />
                            <div className="mt-1 text-xs text-secondary">
                                Tab, Enter, 半角スペースのいずれかで入力確定します。
                            </div>
                            <FieldError name="tags" label="タグ" errors={errors?.tags} />
                        </div>
                        <div>
                            <label htmlFor="note" className="block mb-2">
                                <i className="ti ti-message-circle" /> ノート
                            </label>
                            <textarea
                                id="note"
                                name="note"
                                className={cn(
                                    'block w-full rounded border px-3 py-2 transition duration-150 ease-in-out focus:outline-none focus:ring-4',
                                    errors?.note
                                        ? 'border-danger focus:ring-danger/25'
                                        : 'border-neutral-300 focus:border-primary-400 focus:ring-primary-400/25',
                                )}
                                rows={4}
                                value={values.note}
                                onChange={(e) => setValues((values) => ({ ...values, note: e.target.value }))}
                            />
                            <div className="mt-1 text-xs text-secondary">最大 500 文字</div>
                            <FieldError name="note" label="ノート" errors={errors?.note} />
                        </div>
                    </div>
                </ModalBody>
                <ModalFooter>
                    <Button onClick={handleClose}>キャンセル</Button>
                    <ProgressButton
                        label="更新"
                        type="submit"
                        variant="primary"
                        inProgress={patchCollectionItem.isPending}
                        disabled={patchCollectionItem.isPending}
                    />
                </ModalFooter>
            </form>
        </Modal>
    );
};
