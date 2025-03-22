import { Button, Modal, ModalProps, OverlayTrigger, Tooltip } from 'react-bootstrap';
import React, { useEffect, useState } from 'react';
import { useNavigate, useParams, useSearchParams } from 'react-router';
import classNames from 'classnames';
import { useQueryClient } from '@tanstack/react-query';
import { fetchDeleteJson, fetchPutJson, ResponseError } from '../fetch';
import { showToast } from '../tissue';
import {
    PaginatedResult,
    useCollectionQuery,
    useCollectionItemsQuery,
    useMyCollectionsQuery,
    useMyProfileQuery,
} from '../api';
import { MetadataPreview } from '../components/MetadataPreview';
import { TagInput } from '../components/TagInput';
import { FieldError } from '../components/FieldError';
import { ProgressButton } from '../components/ProgressButton';
import { LinkCard } from '../components/LinkCard';
import { Pagination } from '../components/Pagination';
import {
    CollectionEditModal,
    CollectionFormErrors,
    CollectionFormValidationError,
    CollectionFormValues,
} from '../components/collections/CollectionEditModal';
import { AddToCollectionButton } from '../components/collections/AddToCollectionButton';

interface ItemEditModalProps extends ModalProps {
    item: Tissue.CollectionItem;
    onUpdate: (item: Tissue.CollectionItem) => void;
}

type ItemEditFormValues = {
    tags: string[];
    note: string;
};

type ItemEditFormErrors = {
    [Property in keyof ItemEditFormValues]+?: string[];
};

const ItemEditModal: React.FC<ItemEditModalProps> = ({ item, onUpdate, show, onHide, ...rest }) => {
    const [values, setValues] = useState<ItemEditFormValues>({
        note: item.note,
        tags: item.tags,
    });
    const [errors, setErrors] = useState<ItemEditFormErrors>({});
    const [submitting, setSubmitting] = useState<boolean>(false);

    useEffect(() => {
        if (show) {
            setValues({
                note: item.note,
                tags: item.tags,
            });
            setErrors({});
        }
    }, [show]);

    const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        setSubmitting(true);
        try {
            const response = await fetchPutJson(`/api/collections/${item.collection_id}/items/${item.id}`, {
                ...values,
                flash: true,
            });
            if (response.status === 200) {
                const updatedItem = await response.json();
                showToast('更新しました', { color: 'success', delay: 5000 });
                onUpdate(updatedItem);
                onHide?.();
                return;
            }
            throw new ResponseError(response);
        } catch (e) {
            console.error(e);
            if (e instanceof ResponseError && e.response.status == 422) {
                const data = await e.response.json();
                if (data.error?.violations) {
                    const errors: ItemEditFormErrors = {};
                    for (const violation of data.error.violations) {
                        const field = violation.field as keyof ItemEditFormErrors;
                        (errors[field] || (errors[field] = [])).push(violation.message);
                    }
                    setErrors(errors);
                    return;
                }
            }
            showToast('エラーが発生しました', { color: 'danger', delay: 5000 });
        } finally {
            setSubmitting(false);
        }
    };

    const handleHide = () => {
        if (!submitting && onHide) {
            setValues({ note: item.note, tags: item.tags });
            setErrors({});
            onHide();
        }
    };

    return (
        <Modal show={show} onHide={handleHide} {...rest}>
            <form onSubmit={handleSubmit}>
                <Modal.Header closeButton>
                    <Modal.Title as="h5">編集</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    <div className="form-row">
                        <div className="form-group col-sm-12">
                            <label htmlFor="link">
                                <i className="ti ti-link" /> オカズリンク
                            </label>
                            <input
                                type="text"
                                id="link"
                                name="link"
                                className="form-control"
                                disabled
                                placeholder="http://..."
                                value={item.link}
                            />
                        </div>
                    </div>
                    <MetadataPreview
                        link={item.link}
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
                            <small className="form-text text-muted">
                                Tab, Enter, 半角スペースのいずれかで入力確定します。
                            </small>
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
                </Modal.Body>
                <Modal.Footer>
                    <Button variant="secondary" disabled={submitting} onClick={handleHide}>
                        キャンセル
                    </Button>
                    <ProgressButton
                        label="更新"
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

type CollectionItemProps = {
    item: Tissue.CollectionItem;
    onUpdate: (item: Tissue.CollectionItem) => void;
};

const CollectionItem: React.FC<CollectionItemProps> = ({ item, onUpdate }) => {
    const { data: me } = useMyProfileQuery();
    const queryClient = useQueryClient();
    const myCollectionsQuery = useMyCollectionsQuery();
    const { username } = useParams();
    const [showEditModal, setShowEditModal] = useState(false);
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const [deleted, setDeleted] = useState(false);

    const handleClickDelete = async () => {
        setDeleting(true);
        try {
            const response = await fetchDeleteJson(`/api/collections/${item.collection_id}/items/${item.id}`);
            if (response.ok) {
                setShowDeleteModal(false);
                setDeleted(true);
                showToast('削除しました', { color: 'success', delay: 5000 });
                return;
            }
            throw new ResponseError(response);
        } catch (e) {
            console.error(e);
            showToast('削除中にエラーが発生しました', { color: 'danger', delay: 5000 });
        } finally {
            setDeleting(false);
        }
    };

    if (deleted) {
        return null;
    }

    return (
        <li className="list-group-item border-bottom-only pt-3 pb-3 text-break">
            <div className="row mx-0">
                <LinkCard link={item.link} />
                <p className="d-flex align-items-baseline mb-2 col-12 px-0">
                    <i className="ti ti-link mr-1" />
                    <a className="overflow-hidden" href={item.link} target="_blank" rel="noopener noreferrer">
                        {item.link}
                    </a>
                </p>
            </div>
            {item.tags.length !== 0 && (
                <p className="tis-checkin-tags mb-2">
                    {item.tags.map((tag: string) => (
                        <a
                            key={tag}
                            className="badge badge-secondary"
                            href={`/search/collection?q=${encodeURIComponent(tag)}`}
                        >
                            <i className="ti ti-tag-filled" /> {tag}
                        </a>
                    ))}
                </p>
            )}
            {item.note_html != '' && (
                <p className="mb-2 text-break" dangerouslySetInnerHTML={{ __html: item.note_html }} />
            )}
            <div className="ejaculation-actions">
                <OverlayTrigger
                    placement="bottom"
                    overlay={<Tooltip id={`checkin_${item.id}`}>このオカズでチェックイン</Tooltip>}
                >
                    <button
                        type="button"
                        className="btn text-secondary"
                        onClick={() => (location.href = item.checkin_url)}
                    >
                        <i className="ti ti-send" />
                    </button>
                </OverlayTrigger>
                {me && (
                    <AddToCollectionButton
                        link={item.link}
                        tags={item.tags}
                        collections={myCollectionsQuery?.data}
                        onCreateCollection={() => {
                            queryClient.invalidateQueries(['MyCollections']);
                            queryClient.invalidateQueries(['Collections', username]);
                        }}
                    />
                )}
                {username === me?.name && (
                    <>
                        <OverlayTrigger placement="bottom" overlay={<Tooltip id={`edit_${item.id}`}>編集</Tooltip>}>
                            <button type="button" className="btn text-secondary" onClick={() => setShowEditModal(true)}>
                                <i className="ti ti-edit" />
                            </button>
                        </OverlayTrigger>
                        <OverlayTrigger placement="bottom" overlay={<Tooltip id={`delete_${item.id}`}>削除</Tooltip>}>
                            <button
                                type="button"
                                className="btn text-secondary"
                                onClick={() => setShowDeleteModal(true)}
                            >
                                <i className="ti ti-trash" />
                            </button>
                        </OverlayTrigger>
                    </>
                )}
            </div>
            <ItemEditModal
                item={item}
                show={showEditModal}
                onHide={() => setShowEditModal(false)}
                onUpdate={onUpdate}
            />
            <Modal show={showDeleteModal} onHide={() => !deleting && setShowDeleteModal(false)}>
                <Modal.Header closeButton>
                    <Modal.Title as="h5">削除確認</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    <a className="link-label" href={item.link} target="_blank" rel="noopener noreferrer">
                        {item.link}
                    </a>{' '}
                    をコレクションから削除してもよろしいですか？
                </Modal.Body>
                <Modal.Footer>
                    <Button variant="secondary" disabled={deleting} onClick={() => setShowDeleteModal(false)}>
                        キャンセル
                    </Button>
                    <Button variant="danger" disabled={deleting} onClick={handleClickDelete}>
                        削除
                    </Button>
                </Modal.Footer>
            </Modal>
        </li>
    );
};

type CollectionHeaderProps = {
    collection: Tissue.Collection;
    onUpdate: (collection: Tissue.Collection) => void;
    onDelete: () => void;
};

const CollectionHeader: React.FC<CollectionHeaderProps> = ({ collection, onUpdate, onDelete }) => {
    const { data: me } = useMyProfileQuery();
    const [showEditModal, setShowEditModal] = useState(false);
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [deleting, setDeleting] = useState(false);

    const handleSubmit = async (values: CollectionFormValues) => {
        try {
            const response = await fetchPutJson(`/api/collections/${collection.id}`, values);
            if (response.status === 200) {
                const updatedItem = await response.json();
                showToast('更新しました', { color: 'success', delay: 5000 });
                onUpdate(updatedItem);
                setShowEditModal(false);
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
        }
    };

    const handleClickDelete = async () => {
        setDeleting(true);
        try {
            const response = await fetchDeleteJson(`/api/collections/${collection.id}`);
            if (response.ok) {
                setShowDeleteModal(false);
                showToast('削除しました', { color: 'success', delay: 5000 });
                onDelete();
                return;
            }
            throw new ResponseError(response);
        } catch (e) {
            console.error(e);
            showToast('削除中にエラーが発生しました', { color: 'danger', delay: 5000 });
        } finally {
            setDeleting(false);
        }
    };

    return (
        <div className="border-bottom">
            <div className="d-flex justify-content-between align-items-start flex-column flex-md-row">
                <div>
                    <h4 className="mb-1">{collection.title}</h4>
                    <p className="mb-3">
                        {collection.is_private ? (
                            <small className="text-secondary">
                                <i className="ti ti-lock mr-1" />
                                非公開コレクション
                            </small>
                        ) : (
                            <small className="text-secondary">
                                <i className="ti ti-lock-open mr-1" />
                                公開コレクション
                            </small>
                        )}
                    </p>
                </div>
                {me?.id === collection.user_id && (
                    <div className="flex-shrink-0 mb-3 mb-md-0">
                        <Button
                            className="mr-2"
                            variant="primary"
                            size="sm"
                            href={`/collect?collection=${collection.id}`}
                        >
                            <i className="ti ti-plus mr-2" />
                            オカズを追加
                        </Button>
                        <Button
                            className="mr-2"
                            variant="outline-secondary"
                            size="sm"
                            onClick={() => setShowEditModal(true)}
                        >
                            <i className="ti ti-edit mr-2" />
                            設定
                        </Button>
                        <Button variant="outline-secondary" size="sm" onClick={() => setShowDeleteModal(true)}>
                            <i className="ti ti-trash mr-2" />
                            削除
                        </Button>
                    </div>
                )}
            </div>
            <CollectionEditModal
                mode="edit"
                initialValues={{ title: collection.title, is_private: collection.is_private }}
                onSubmit={handleSubmit}
                show={showEditModal}
                onHide={() => setShowEditModal(false)}
            />
            <Modal show={showDeleteModal} onHide={() => !deleting && setShowDeleteModal(false)}>
                <Modal.Header closeButton>
                    <Modal.Title as="h5">削除確認</Modal.Title>
                </Modal.Header>
                <Modal.Body>コレクションを削除してもよろしいですか？</Modal.Body>
                <Modal.Footer>
                    <Button variant="secondary" disabled={deleting} onClick={() => setShowDeleteModal(false)}>
                        キャンセル
                    </Button>
                    <ProgressButton
                        label="削除"
                        inProgress={deleting}
                        type="submit"
                        variant="danger"
                        disabled={deleting}
                        onClick={handleClickDelete}
                    />
                </Modal.Footer>
            </Modal>
        </div>
    );
};

const CollectionItemList: React.FC = () => {
    const { id } = useParams();
    const [searchParams] = useSearchParams();
    const page = searchParams.get('page');

    const queryClient = useQueryClient();
    const collectionItemsQuery = useCollectionItemsQuery(id as string, page);

    const handleItemUpdate = (item: Tissue.CollectionItem) => {
        queryClient.setQueryData<PaginatedResult<Tissue.CollectionItem[]> | undefined>(
            ['CollectionItems', id, { page: page || 1 }],
            (result) =>
                result ? { ...result, data: result.data?.map((i) => (i.id === item.id ? item : i)) } : undefined,
        );
    };

    return (
        <>
            {collectionItemsQuery.data && (
                <ul className="list-group">
                    {collectionItemsQuery.isLoading ? null : collectionItemsQuery.data.data.length === 0 ? (
                        <li className="list-group-item border-bottom-only">
                            <p className="my-3">このコレクションにはまだオカズが登録されていません。</p>
                        </li>
                    ) : (
                        collectionItemsQuery.data.data.map((item) => (
                            <CollectionItem key={item.id} item={item} onUpdate={handleItemUpdate} />
                        ))
                    )}
                </ul>
            )}
            {!!collectionItemsQuery.data?.totalCount && (
                <Pagination
                    className="mt-4 justify-content-center"
                    perPage={20}
                    totalCount={collectionItemsQuery.data.totalCount}
                />
            )}
        </>
    );
};

export const Collection: React.FC = () => {
    const { username, id } = useParams();
    const [searchParams] = useSearchParams();
    const page = searchParams.get('page');
    const navigate = useNavigate();

    const queryClient = useQueryClient();
    const collectionQuery = useCollectionQuery(id as string);

    useEffect(() => {
        window.scrollTo(0, 0);
    }, [page]);

    useEffect(() => {
        if (!collectionQuery.isLoading && collectionQuery.data && collectionQuery.data.user_name !== username) {
            // リロードをかけるため、location.hrefを変更
            location.href = `/user/${collectionQuery.data.user_name}/collections/${collectionQuery.data.id}`;
        }
    }, [username, collectionQuery.isLoading]);

    const handleCollectionUpdate = (collection: Tissue.Collection) => {
        queryClient.setQueryData(['Collection', id], collection);
        queryClient.setQueryData<Tissue.Collection[] | undefined>(['Collections', username], (col) =>
            col?.map((c) => (c.id === collection.id ? collection : c)),
        );
        queryClient.setQueryData<Tissue.Collection[] | undefined>(['MyCollections'], (col) =>
            col?.map((c) => (c.id === collection.id ? collection : c)),
        );
    };

    const handleCollectionDelete = () => {
        queryClient.setQueryData<Tissue.Collection[] | undefined>(['Collections', username], (col) =>
            col?.filter((c) => c.id !== collectionQuery.data.id),
        );
        queryClient.invalidateQueries(['Collections', username]);
        queryClient.setQueryData<Tissue.Collection[] | undefined>(['MyCollections'], (col) =>
            col?.filter((c) => c.id !== collectionQuery.data.id),
        );
        queryClient.invalidateQueries(['MyCollections']);
        navigate('../');
    };

    return (
        <>
            {collectionQuery.error?.response?.status === 404 && (
                <p className="mt-4">お探しのコレクションは見つかりませんでした。</p>
            )}
            {collectionQuery.data && (
                <CollectionHeader
                    collection={collectionQuery.data}
                    onUpdate={handleCollectionUpdate}
                    onDelete={handleCollectionDelete}
                />
            )}
            <CollectionItemList key={id} />
        </>
    );
};
