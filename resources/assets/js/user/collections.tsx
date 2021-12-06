import React, { useState } from 'react';
import ReactDOM from 'react-dom';
import { BrowserRouter, Link, Route, Routes, useParams, useSearchParams } from 'react-router-dom';
import { Button, Modal, ModalProps, OverlayTrigger, Tooltip } from 'react-bootstrap';
import { LinkCard } from '../components/LinkCard';
import { MyProfileContext, useMyProfile } from '../context';
import { useFetchMyProfile, useFetchCollections, useFetchCollectionItems } from '../api';
import { Pagination } from '../components/Pagination';
import { showToast } from '../tissue';
import { fetchDeleteJson, ResponseError } from '../fetch';
import classNames from 'classnames';
import { MetadataPreview } from '../components/MetadataPreview';
import { TagInput } from '../components/TagInput';
import { FieldError } from '../components/FieldError';

type SidebarItemProps = {
    collection: Tissue.Collection;
};

const SidebarItem: React.FC<SidebarItemProps> = ({ collection }) => {
    const { id } = useParams();

    if (collection.id == id) {
        return (
            <li className="list-group-item d-flex justify-content-between align-items-center active">
                <div style={{ wordBreak: 'break-all' }}>
                    <span className="oi oi-folder mr-1" />
                    {collection.title}
                </div>
            </li>
        );
    } else {
        return (
            <a className="list-group-item d-flex justify-content-between align-items-center text-dark">
                <Link to={collection.id}>
                    <div style={{ wordBreak: 'break-all' }}>
                        <span className="oi oi-folder text-secondary mr-1" />
                        {collection.title}
                    </div>
                </Link>
            </a>
        );
    }
};

type SidebarProps = {
    collections?: Tissue.Collection[];
};

const Sidebar: React.FC<SidebarProps> = ({ collections }) => {
    if (!collections) {
        return null;
    }

    return (
        <div className="card mb-4">
            <div className="card-header">コレクション</div>
            <div className="list-group list-group-flush">
                {collections.map((collection) => (
                    <SidebarItem key={collection.id} collection={collection} />
                ))}
            </div>
        </div>
    );
};

interface EditModalProps extends ModalProps {
    item: Tissue.CollectionItem;
}

type EditFormValues = {
    tags: string[];
    note: string;
};

type EditFormErrors = {
    [Property in keyof EditFormValues]+?: string[];
};

const EditModal: React.FC<EditModalProps> = ({ item, onHide, ...rest }) => {
    const [values, setValues] = useState<EditFormValues>({
        note: item.note,
        tags: item.tags,
    });
    const [errors, setErrors] = useState<EditFormErrors>({});
    const [submitting, setSubmitting] = useState<boolean>(false);

    const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        // TODO
    };

    const handleHide = () => {
        if (!submitting && onHide) {
            setValues({ note: item.note, tags: item.tags });
            setErrors({});
            onHide();
        }
    };

    return (
        <Modal onHide={handleHide} {...rest}>
            <form onSubmit={handleSubmit}>
                <Modal.Header closeButton>
                    <Modal.Title as="h5">編集</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    <div className="form-row">
                        <div className="form-group col-sm-12">
                            <label htmlFor="link">
                                <span className="oi oi-link-intact" /> オカズリンク
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
                                <span className="oi oi-tags" /> タグ
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
                                <span className="oi oi-comment-square" /> ノート
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
                    <Button variant="secondary" onClick={handleHide}>
                        キャンセル
                    </Button>
                    <Button type="submit" variant="primary">
                        更新
                    </Button>
                </Modal.Footer>
            </form>
        </Modal>
    );
};

type CollectionItemProps = {
    collectionId: string;
    item: Tissue.CollectionItem;
};

const CollectionItem: React.FC<CollectionItemProps> = ({ collectionId, item }) => {
    const me = useMyProfile();
    const { username } = useParams();
    const [showEditModal, setShowEditModal] = useState(false);
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const [deleted, setDeleted] = useState(false);

    const handleClickDelete = async () => {
        setDeleting(true);
        try {
            const response = await fetchDeleteJson(`/api/collections/${collectionId}/items/${item.id}`);
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
                    <span className="oi oi-link-intact mr-1" />
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
                            href={`/search/checkin?q=${encodeURIComponent(tag)}`}
                        >
                            <span className="oi oi-tag" /> {tag}
                        </a>
                    ))}
                </p>
            )}
            {item.note != '' && <p className="mb-2 text-break" dangerouslySetInnerHTML={{ __html: item.note }} />}
            <div className="ejaculation-actions">
                <OverlayTrigger
                    placement="bottom"
                    overlay={<Tooltip id={`checkin_${item.id}`}>このオカズでチェックイン</Tooltip>}
                >
                    <button
                        type="button"
                        className="btn btn-link text-secondary"
                        onClick={() => (location.href = item.checkin_url)}
                    >
                        <span className="oi oi-check" />
                    </button>
                </OverlayTrigger>
                <span className="dropdown">
                    <OverlayTrigger
                        placement="bottom"
                        overlay={<Tooltip id={`add_collection_${item.id}`}>コレクションに追加</Tooltip>}
                    >
                        <button type="button" className="btn btn-link text-secondary" data-toggle="dropdown">
                            <span className="oi oi-plus" />
                        </button>
                    </OverlayTrigger>
                    <div className="dropdown-menu">
                        <h6 className="dropdown-header">コレクションに追加</h6>
                        <button type="button" className="dropdown-item use-later-button" data-link={item.link}>
                            あとで抜く
                        </button>
                    </div>
                </span>
                {username === me?.name && (
                    <>
                        <OverlayTrigger placement="bottom" overlay={<Tooltip id={`edit_${item.id}`}>編集</Tooltip>}>
                            <button
                                type="button"
                                className="btn btn-link text-secondary"
                                onClick={() => setShowEditModal(true)}
                            >
                                <span className="oi oi-pencil" />
                            </button>
                        </OverlayTrigger>
                        <OverlayTrigger placement="bottom" overlay={<Tooltip id={`delete_${item.id}`}>削除</Tooltip>}>
                            <button
                                type="button"
                                className="btn btn-link text-secondary"
                                onClick={() => setShowDeleteModal(true)}
                            >
                                <span className="oi oi-trash" />
                            </button>
                        </OverlayTrigger>
                    </>
                )}
            </div>
            <EditModal item={item} show={showEditModal} onHide={() => setShowEditModal(false)} />
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

const Collection: React.FC = () => {
    const { id } = useParams();
    const [searchParams] = useSearchParams();
    const { loading, data, totalCount } = useFetchCollectionItems({ id: id as string, page: searchParams.get('page') });

    if (!data) {
        return null;
    }

    return (
        <>
            <ul className="list-group">
                {loading ? null : data.length === 0 ? (
                    <li className="list-group-item border-bottom-only">
                        <p>このコレクションにはまだオカズが登録されていません。</p>
                    </li>
                ) : (
                    data.map((item) => <CollectionItem key={item.id} collectionId={id as string} item={item} />)
                )}
            </ul>
            {totalCount && <Pagination className="mt-4 justify-content-center" perPage={20} totalCount={totalCount} />}
        </>
    );
};

const Page: React.FC = () => {
    const { username } = useParams();
    const fetchMyProfile = useFetchMyProfile();
    const fetchCollections = useFetchCollections({ username: username as string });

    return (
        <MyProfileContext.Provider value={fetchMyProfile.data}>
            <div className="container">
                <div className="row">
                    <div className="col-lg-4">
                        <Sidebar collections={fetchCollections.data} />
                    </div>
                    <div className="col-lg-8">
                        {fetchCollections.error?.response?.status === 403 && (
                            <p className="mt-4">
                                <span className="oi oi-lock-locked" /> このユーザはチェックイン履歴を公開していません。
                            </p>
                        )}
                        {fetchCollections.data?.length === 0 && <p className="mt-4">コレクションがありません。</p>}
                        <Collection />
                    </div>
                </div>
            </div>
        </MyProfileContext.Provider>
    );
};

ReactDOM.render(
    <BrowserRouter>
        <Routes>
            <Route path="/user/:username/collections/:id" element={<Page />} />
        </Routes>
    </BrowserRouter>,
    document.getElementById('app')
);
