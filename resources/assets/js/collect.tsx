import React, { useEffect, useState } from 'react';
import { createRoot } from 'react-dom/client';
import { useQueryClient } from '@tanstack/react-query';
import classNames from 'classnames';
import { FieldError } from './components/FieldError';
import { TagInput } from './components/TagInput';
import { MetadataPreview } from './components/MetadataPreview';
import { fetchPostJson, ResponseError } from './fetch';
import { showToast } from './tissue';
import { useMyCollectionsQuery } from './api';
import { QueryClientProvider } from './query';
import {
    CollectionEditModal,
    CollectionFormErrors,
    CollectionFormValidationError,
    CollectionFormValues,
} from './components/collections/CollectionEditModal';

type FormValues = {
    link: string;
    tags: string[];
    note: string;
};

type FormErrors = {
    [Property in keyof FormValues]+?: string[];
};

const CollectForm = () => {
    const searchParams = new URLSearchParams(location.search);
    const [collectionId, setCollectionId] = useState(searchParams.get('collection') || '');
    const [values, setValues] = useState<FormValues>({
        link: searchParams.get('link') || '',
        note: searchParams.get('note') || '',
        tags: [],
    });
    const [errors, setErrors] = useState<FormErrors>({});
    const [linkForPreview, setLinkForPreview] = useState(values.link);
    const [submitting, setSubmitting] = useState<boolean>(false);
    const queryClient = useQueryClient();
    const myCollectionsQuery = useMyCollectionsQuery();
    const [showCreateModal, setShowCreateModal] = useState(false);

    useEffect(() => {
        if (!myCollectionsQuery.isLoading && myCollectionsQuery.data && myCollectionsQuery.data.length !== 0) {
            if (myCollectionsQuery.data.find((col) => col.id == collectionId)) {
                return;
            }
            setCollectionId(myCollectionsQuery.data[0].id);
        }
    }, [myCollectionsQuery.isLoading]);

    const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        if (collectionId === '') {
            showToast('コレクションが選択されていません', { color: 'danger', delay: 5000 });
            return;
        }
        setSubmitting(true);
        try {
            const response = await fetchPostJson(`/api/collections/${collectionId}/items`, { ...values, flash: true });
            if (response.status === 201) {
                const data = await response.json();
                if (data.collection_id && data.user_name) {
                    location.href = `/user/${data.user_name}/collections/${data.collection_id}`;
                    return;
                }
            }
            throw new ResponseError(response);
        } catch (e) {
            console.error(e);
            if (e instanceof ResponseError && e.response.status == 422) {
                const data = await e.response.json();
                if (data.error?.violations) {
                    const errors: FormErrors = {};
                    for (const violation of data.error.violations) {
                        const field = violation.field as keyof FormValues;
                        (errors[field] || (errors[field] = [])).push(violation.message);
                    }
                    setErrors(errors);
                    setSubmitting(false);
                    return;
                } else if (data.error?.message) {
                    showToast(data.error.message, { color: 'danger', delay: 5000 });
                    return;
                }
            }
            showToast('エラーが発生しました', { color: 'danger', delay: 5000 });
            setSubmitting(false);
        }
    };

    const handleSubmitCreate = async (values: CollectionFormValues) => {
        try {
            const response = await fetchPostJson('/api/collections', values);
            if (response.status === 201) {
                const createdItem = await response.json();
                showToast('コレクションを作成しました', { color: 'success', delay: 5000 });
                setShowCreateModal(false);
                queryClient.setQueryData<Tissue.Collection[]>(['MyCollections'], (col) => [
                    ...(col || []),
                    createdItem,
                ]);
                setCollectionId(createdItem.id);
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
        <form onSubmit={handleSubmit}>
            <div className="form-row">
                <div className="form-group col-sm-12">
                    <div className="d-flex justify-content-between">
                        <label htmlFor="collection">
                            <i className="ti ti-folder" /> 追加先
                        </label>
                        <button
                            className="btn btn-link p-0 mb-2"
                            type="button"
                            onClick={() => setShowCreateModal(true)}
                        >
                            新規作成
                        </button>
                    </div>
                    <select
                        name="collection"
                        id="collection"
                        className="custom-select"
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
            </div>
            <div className="form-row">
                <div className="form-group col-sm-12">
                    <label htmlFor="link">
                        <i className="ti ti-link" /> オカズリンク
                    </label>
                    <input
                        type="text"
                        id="link"
                        name="link"
                        autoComplete="off"
                        className={classNames({ 'form-control': true, 'is-invalid': errors?.link })}
                        placeholder="http://..."
                        required
                        value={values.link}
                        onChange={(e) => setValues((values) => ({ ...values, link: e.target.value }))}
                        onBlur={() => setLinkForPreview(values.link)}
                    />
                    <small className="form-text text-muted">オカズのURLを貼り付けてください。</small>
                    <FieldError name="link" label="リンク" errors={errors?.link} />
                </div>
            </div>
            <MetadataPreview
                link={linkForPreview}
                tags={values.tags}
                onClickTag={(v) => setValues(({ tags, ...rest }) => ({ ...rest, tags: tags.concat(v) }))}
            />
            <div className="form-row">
                <div className="form-group col-sm-12">
                    <label htmlFor="tagInput">
                        <i className="ti ti-tags" /> タグ
                    </label>
                    <TagInput
                        id="tagInput"
                        name="tags"
                        values={values.tags}
                        isInvalid={!!errors?.tags}
                        onChange={(v) => setValues((values) => ({ ...values, tags: v }))}
                    />
                    <small className="form-text text-muted">Tab, Enter, 半角スペースのいずれかで入力確定します。</small>
                    <FieldError name="tags" label="タグ" errors={errors?.tags} />
                </div>
            </div>
            <div className="form-row">
                <div className="form-group col-sm-12">
                    <label htmlFor="note">
                        <i className="ti ti-message-circle" /> ノート
                    </label>
                    <textarea
                        id="note"
                        name="note"
                        className={classNames({ 'form-control': true, 'is-invalid': errors?.note })}
                        rows={4}
                        value={values.note}
                        onChange={(e) => setValues((values) => ({ ...values, note: e.target.value }))}
                    />
                    <small className="form-text text-muted">最大 500 文字</small>
                    <FieldError name="note" label="ノート" errors={errors?.note} />
                </div>
            </div>
            <div className="text-center">
                <button className="btn btn-primary" type="submit" disabled={submitting || collectionId === ''}>
                    登録
                </button>
            </div>
            <CollectionEditModal
                mode="create"
                initialValues={{ title: '', is_private: true }}
                onSubmit={handleSubmitCreate}
                show={showCreateModal}
                onHide={() => setShowCreateModal(false)}
            />
        </form>
    );
};

createRoot(document.getElementById('form') as HTMLElement).render(
    <QueryClientProvider>
        <CollectForm />
    </QueryClientProvider>,
);
