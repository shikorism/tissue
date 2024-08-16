import React, { useState } from 'react';
import { Button, Dropdown, OverlayTrigger, Spinner, Tooltip } from 'react-bootstrap';
import { fetchPostJson, ResponseError } from '../../fetch';
import { showToast } from '../../tissue';
import {
    CollectionEditModal,
    CollectionFormErrors,
    CollectionFormValidationError,
    CollectionFormValues,
} from './CollectionEditModal';
import { CollectionSelectModal } from './CollectionSelectModal';
import { parseISO, compareDesc } from 'date-fns';

const ToggleButton = React.forwardRef<HTMLButtonElement>((props, ref) => (
    <Button {...props} ref={ref} variant="" className="text-secondary">
        <i className="ti ti-folder-plus" />
    </Button>
));
ToggleButton.displayName = 'ToggleButton';

type AddToCollectionButtonProps = {
    link: string;
    tags: string[];
    collections?: Tissue.Collection[];
    onCreateCollection?: () => void;
};

export const AddToCollectionButton: React.FC<AddToCollectionButtonProps> = ({
    link,
    tags,
    collections,
    onCreateCollection,
}) => {
    const [showCreateModal, setShowCreateModal] = useState(false);
    const [showSelectModal, setShowSelectModal] = useState(false);

    const handleSelectCollection = async (collection: Tissue.Collection) => {
        try {
            const response = await fetchPostJson(`/api/collections/${collection.id}/items`, { link, tags });
            if (response.ok) {
                showToast(`${collection.title} に追加しました`, { color: 'success', delay: 5000 });
                setShowSelectModal(false);
            } else {
                throw new ResponseError(response);
            }
        } catch (e) {
            console.error(e);
            if (e instanceof ResponseError && e.response.status == 422) {
                const data = await e.response.json();
                if (data.error?.violations) {
                    if (data.error.violations.some((v: any) => v.field === 'link')) {
                        showToast('すでに登録されています', { color: 'danger', delay: 5000 });
                        return;
                    }
                } else if (data.error?.message) {
                    showToast(data.error.message, { color: 'danger', delay: 5000 });
                    return;
                }
            }
            showToast(`${collection.title} に追加できませんでした`, { color: 'danger', delay: 5000 });
        }
    };

    const handleSelect = async (eventKey: string | null) => {
        if (eventKey === 'new') {
            setShowCreateModal(true);
            return;
        }
        if (eventKey === 'select') {
            setShowSelectModal(true);
            return;
        }

        const collection = collections?.find((collection) => collection.id == eventKey);
        if (!collection) {
            return;
        }
        await handleSelectCollection(collection);
    };

    const handleSubmit = async (values: CollectionFormValues) => {
        try {
            const response = await fetchPostJson('/api/collections', { ...values, items: [{ link, tags }] });
            if (response.status === 201) {
                await response.json();
                showToast('作成して追加しました', { color: 'success', delay: 5000 });
                setShowCreateModal(false);
                onCreateCollection?.();
                return;
            }
            throw new ResponseError(response);
        } catch (e) {
            console.error(e);
            if (e instanceof ResponseError && e.response.status == 422) {
                const data = await e.response.json();
                if (data.error?.violations) {
                    const errors: CollectionFormErrors = {};
                    for (const violation of data.error.violations) {
                        const field = violation.field as keyof CollectionFormErrors;
                        (errors[field] || (errors[field] = [])).push(violation.message);
                    }
                    throw new CollectionFormValidationError(errors);
                }
            }
            showToast('エラーが発生しました', { color: 'danger', delay: 5000 });
        }
    };

    return (
        <Dropdown as="span" onSelect={handleSelect}>
            <OverlayTrigger placement="bottom" overlay={<Tooltip id="add_collection">コレクションに追加</Tooltip>}>
                <Dropdown.Toggle as={ToggleButton} />
            </OverlayTrigger>
            <Dropdown.Menu>
                <Dropdown.Header>コレクションに追加</Dropdown.Header>
                {collections ? (
                    <>
                        {collections
                            .sort((a, b) => compareDesc(parseISO(a.updated_at), parseISO(b.updated_at)))
                            .filter((_, i) => i < 5)
                            .map((collection) => (
                                <Dropdown.Item key={collection.id} eventKey={collection.id}>
                                    <i className="ti ti-folder mr-2 text-secondary" />
                                    {collection.title}
                                </Dropdown.Item>
                            ))}
                        {collections.length > 5 && (
                            <Dropdown.Item eventKey="select">
                                <i className="ti ti-dots mr-2 text-secondary" />
                                その他のコレクション
                            </Dropdown.Item>
                        )}
                        <Dropdown.Divider />
                        <Dropdown.Item eventKey="new">
                            <i className="ti ti-plus mr-2 text-secondary" />
                            新しいコレクションに追加
                        </Dropdown.Item>
                    </>
                ) : (
                    <Dropdown.ItemText className="text-secondary">
                        <Spinner
                            className="mr-1"
                            as="span"
                            animation="border"
                            size="sm"
                            role="status"
                            aria-hidden="true"
                        />
                        読み込み中…
                    </Dropdown.ItemText>
                )}
            </Dropdown.Menu>
            <CollectionEditModal
                mode="create"
                initialValues={{ title: '', is_private: true }}
                onSubmit={handleSubmit}
                show={showCreateModal}
                onHide={() => setShowCreateModal(false)}
            />
            {collections && (
                <CollectionSelectModal
                    title="追加先のコレクションを選択"
                    collections={collections}
                    show={showSelectModal}
                    onHide={() => setShowSelectModal(false)}
                    onSelectCollection={handleSelectCollection}
                />
            )}
        </Dropdown>
    );
};
