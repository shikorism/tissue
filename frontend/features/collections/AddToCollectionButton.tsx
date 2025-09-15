import React, { Suspense, useState } from 'react';
import { useSuspenseQuery } from '@tanstack/react-query';
import { compareDesc, parseISO } from 'date-fns';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '../../components/DropdownMenu';
import { getMyCollectionsQuery } from '../../api/query';
import { usePostCollectionItem, usePostCollections } from '../../api/mutation';
import { ResponseError } from '../../api/errors';
import type { components } from '../../api/schema';
import { toast } from 'sonner';
import {
    CollectionEditModal,
    CollectionFormErrors,
    CollectionFormValidationError,
    CollectionFormValues,
} from './CollectionEditModal';
import { CollectionSelectModal } from './CollectionSelectModal';

interface AddToCollectionButtonProps {
    link: string;
    tags: string[];
}

export const AddToCollectionButton: React.FC<AddToCollectionButtonProps> = ({ link, tags }) => {
    const [isOpenCreateModal, setIsOpenCreateModal] = useState(false);
    const [isOpenSelectModal, setIsOpenSelectModal] = useState(false);

    const postCollections = usePostCollections();
    const postCollectionItem = usePostCollectionItem();

    const addToCollection = async (collection: components['schemas']['Collection']) => {
        try {
            await postCollectionItem.mutateAsync({ collectionId: collection.id, body: { link, tags } });
            toast.success(`${collection.title} に追加しました`);
            setIsOpenSelectModal(false);
        } catch (e) {
            if (e instanceof ResponseError && e.response.status === 422) {
                if (e.error?.violations) {
                    if (e.error.violations.some((v: any) => v.field === 'link')) {
                        toast.error('すでに登録されています');
                        return;
                    }
                } else if (e.error?.message) {
                    toast.error(e.error.message);
                    return;
                }
            }
            toast.error(`${collection.title} に追加できませんでした`);
        }
    };

    const handleSubmit = async (values: CollectionFormValues) => {
        try {
            await postCollections.mutateAsync({ ...values, items: [{ link, tags }] });
            toast.success('作成して追加しました');
            setIsOpenCreateModal(false);
        } catch (e) {
            if (e instanceof ResponseError && e.response.status === 422) {
                if (e.error?.violations) {
                    const errors: CollectionFormErrors = {};
                    for (const violation of e.error.violations) {
                        const field = violation.field as keyof CollectionFormErrors;
                        (errors[field] || (errors[field] = [])).push(violation.message);
                    }
                    throw new CollectionFormValidationError(errors);
                } else if (e.error?.message) {
                    toast.error(e.error.message);
                    return;
                }
            }
            toast.error('エラーが発生しました');
        }
    };

    return (
        <DropdownMenu>
            <DropdownMenuTrigger
                className="px-4 py-2 text-xl text-secondary rounded outline-2 outline-primary/0 focus:outline-primary/40 active:outline-primary/40 cursor-pointer"
                title="コレクションに追加"
            >
                <i className="ti ti-folder-plus" />
            </DropdownMenuTrigger>
            <DropdownMenuContent align="start">
                <DropdownMenuLabel>コレクションに追加</DropdownMenuLabel>
                <Suspense
                    fallback={
                        <DropdownMenuItem disabled>
                            <i className="ti ti-loader animate-spin" />
                            読み込み中…
                        </DropdownMenuItem>
                    }
                >
                    <CollectionMenuContent
                        onSelect={addToCollection}
                        onOpenCreate={() => setIsOpenCreateModal(true)}
                        onOpenSelect={() => setIsOpenSelectModal(true)}
                    />
                </Suspense>
            </DropdownMenuContent>

            <CollectionEditModal
                mode="create"
                initialValues={{ title: '', is_private: true }}
                isOpen={isOpenCreateModal}
                onSubmit={handleSubmit}
                onClose={() => setIsOpenCreateModal(false)}
            />

            <CollectionSelectModal
                title="追加先のコレクションを選択"
                isOpen={isOpenSelectModal}
                onClose={() => setIsOpenSelectModal(false)}
                onSelectCollection={addToCollection}
            />
        </DropdownMenu>
    );
};

interface CollectionMenuContentProps {
    onSelect: (collection: components['schemas']['Collection']) => void;
    onOpenCreate: () => void;
    onOpenSelect: () => void;
}

const CollectionMenuContent: React.FC<CollectionMenuContentProps> = ({ onSelect, onOpenCreate, onOpenSelect }) => {
    const { data: collections } = useSuspenseQuery({ ...getMyCollectionsQuery(), staleTime: 300000 });

    return (
        <>
            {collections
                .toSorted((a, b) => compareDesc(parseISO(a.updated_at), parseISO(b.updated_at)))
                .filter((_, i) => i < 5)
                .map((collection) => (
                    <DropdownMenuItem key={collection.id} onClick={() => onSelect(collection)}>
                        <i className="ti ti-folder" />
                        {collection.title}
                    </DropdownMenuItem>
                ))}
            {collections.length > 5 && (
                <DropdownMenuItem onClick={onOpenSelect}>
                    <i className="ti ti-dots" />
                    その他のコレクション
                </DropdownMenuItem>
            )}
            <DropdownMenuSeparator />
            <DropdownMenuItem onClick={onOpenCreate}>
                <i className="ti ti-plus" />
                新しいコレクションに追加
            </DropdownMenuItem>
        </>
    );
};
