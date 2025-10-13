import React, { FormEventHandler, useEffect, useState } from 'react';
import { useNavigate, useSearchParams } from 'react-router';
import { useSuspenseQuery } from '@tanstack/react-query';
import { getMyCollectionsQuery } from '../api/query';
import { cn } from '../lib/cn';
import { FieldError } from '../components/FieldError';
import { MetadataPreview } from '../components/MetadataPreview';
import { TagInput } from '../components/TagInput';
import { ProgressButton } from '../components/ProgressButton';
import { usePostCollectionItem, usePostCollections } from '../api/mutation';
import {
    CollectionEditModal,
    CollectionFormErrors,
    CollectionFormValidationError,
    CollectionFormValues,
} from '../features/collections/CollectionEditModal';
import { toast } from 'sonner';
import { ResponseError } from '../api/errors';

type FormValues = {
    link: string;
    tags: string[];
    note: string;
};

type FormErrors = {
    [Property in keyof FormValues]+?: string[];
};

export const Collect: React.FC = () => {
    const [searchParams] = useSearchParams();
    const navigate = useNavigate();
    const [collectionId, setCollectionId] = useState(searchParams.get('collection') || '');
    const [values, setValues] = useState<FormValues>({
        link: searchParams.get('link') || '',
        note: searchParams.get('note') || '',
        tags: [],
    });
    const [errors, setErrors] = useState<FormErrors>({});
    const [linkForPreview, setLinkForPreview] = useState(values.link);
    const [submitting, setSubmitting] = useState<boolean>(false);
    const myCollectionsQuery = useSuspenseQuery(getMyCollectionsQuery());
    const postCollectionItem = usePostCollectionItem();
    const postCollections = usePostCollections();
    const [isOpenCreateModal, setIsOpenCreateModal] = useState(false);

    useEffect(() => {
        if (!myCollectionsQuery.isLoading && myCollectionsQuery.data && myCollectionsQuery.data.length !== 0) {
            if (myCollectionsQuery.data.find((col) => `${col.id}` === collectionId)) {
                return;
            }
            setCollectionId(`${myCollectionsQuery.data[0].id}`);
        }
    }, [myCollectionsQuery.isLoading]);

    const handleSubmit: FormEventHandler<HTMLFormElement> = async (event) => {
        event.preventDefault();
        setSubmitting(true);
        if (collectionId === '') {
            toast.error('コレクションが選択されていません');
            return;
        }
        try {
            const createdItem = await postCollectionItem.mutateAsync({
                collectionId: Number(collectionId),
                body: values,
            });
            toast.success('登録しました');
            navigate(`/user/${createdItem.user_name}/collections/${createdItem.collection_id}`);
        } catch (e) {
            if (e instanceof ResponseError && e.response.status === 422) {
                if (e.error?.violations) {
                    const errors: FormErrors = {};
                    for (const violation of e.error.violations) {
                        const field = violation.field as keyof FormValues;
                        (errors[field] || (errors[field] = [])).push(violation.message);
                    }
                    setErrors(errors);
                    return;
                } else if (e.error?.message) {
                    toast.error(e.error.message);
                    return;
                }
            }
            toast.error('エラーが発生しました');
        } finally {
            setSubmitting(false);
        }
    };

    const handleSubmitCreate = async (values: CollectionFormValues) => {
        try {
            const createdCollection = await postCollections.mutateAsync(values);
            toast.success('コレクションを作成しました');
            setIsOpenCreateModal(false);
            setCollectionId(`${createdCollection.id}`);
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
        <div className="p-4">
            <h1 className="pb-4 text-3xl border-b-1 border-gray-border">コレクションに追加</h1>
            <form className="flex flex-col gap-4 mx-auto py-4 lg:max-w-[600px]" onSubmit={handleSubmit}>
                <div>
                    <div className="flex justify-between mb-2">
                        <label htmlFor="collection" className="block">
                            <i className="ti ti-folder" /> 追加先
                        </label>
                        <button
                            type="button"
                            className="text-primary cursor-pointer hover:brightness-80 hover:underline"
                            onClick={() => setIsOpenCreateModal(true)}
                        >
                            新規作成
                        </button>
                    </div>
                    <select
                        name="collection"
                        id="collection"
                        className="w-full p-2 rounded transition duration-150 ease-in-out focus:outline-none focus:ring-4 border border-neutral-300 focus:border-primary-400 focus:ring-primary-400/25"
                        disabled={!myCollectionsQuery.data}
                        value={collectionId}
                        onChange={(e) => setCollectionId(e.target.value)}
                    >
                        {myCollectionsQuery.data?.map((collection) => (
                            <option key={collection.id} value={collection.id}>
                                {collection.title}
                            </option>
                        ))}
                    </select>
                </div>

                <div>
                    <label htmlFor="link" className="block mb-2">
                        <i className="ti ti-link" /> オカズリンク
                    </label>
                    <input
                        type="text"
                        id="link"
                        name="link"
                        autoComplete="off"
                        className={cn(
                            'block w-full rounded border px-3 py-2 transition duration-150 ease-in-out focus:outline-none focus:ring-4',
                            errors?.link
                                ? 'border-danger focus:ring-danger/25'
                                : 'border-neutral-300 focus:border-primary-400 focus:ring-primary-400/25',
                        )}
                        placeholder="http://..."
                        required
                        value={values.link}
                        onChange={(e) => setValues((values) => ({ ...values, link: e.target.value }))}
                        onBlur={() => setLinkForPreview(values.link)}
                    />
                    <p className="mt-1 text-xs text-secondary">オカズのURLを貼り付けてください。</p>
                    <FieldError name="link" label="リンク" errors={errors?.link} />
                </div>

                <MetadataPreview
                    link={linkForPreview}
                    tags={values.tags}
                    onClickTag={(v) => setValues(({ tags, ...rest }) => ({ ...rest, tags: tags.concat(v) }))}
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

                <div className="text-center py-2">
                    <ProgressButton
                        label="登録"
                        type="submit"
                        variant="primary"
                        inProgress={submitting}
                        disabled={submitting}
                    />
                </div>
            </form>

            <CollectionEditModal
                mode="create"
                initialValues={{ title: '', is_private: true }}
                isOpen={isOpenCreateModal}
                onSubmit={handleSubmitCreate}
                onClose={() => setIsOpenCreateModal(false)}
            />
        </div>
    );
};

export default Collect;
