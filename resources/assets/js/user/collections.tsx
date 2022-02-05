import React, { useContext, useEffect, useState } from 'react';
import ReactDOM from 'react-dom';
import { BrowserRouter, Link, Outlet, Route, Routes, useNavigate, useParams } from 'react-router-dom';
import { Button, Form, Modal, ModalProps } from 'react-bootstrap';
import classNames from 'classnames';
import { MyProfileContext, useMyProfile } from '../context';
import { useFetchMyProfile, useFetchCollections, useFetchMyCollections } from '../api';
import { showToast } from '../tissue';
import { fetchPostJson, ResponseError } from '../fetch';
import { FieldError } from '../components/FieldError';
import { ProgressButton } from '../components/ProgressButton';
import { Collection } from './collection';

export const CollectionsContext = React.createContext<ReturnType<typeof useFetchCollections> | undefined>(undefined);
export const MyCollectionsContext = React.createContext<ReturnType<typeof useFetchMyCollections> | undefined>(
    undefined
);

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
        if (Error.captureStackTrace) {
            Error.captureStackTrace(this, CollectionFormValidationError);
        }

        this.name = 'CollectionFormValidationError';
        this.errors = errors;
        Object.setPrototypeOf(this, new.target.prototype);
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
                                <span className="oi oi-folder" /> タイトル
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
                                <span className="oi oi-eye" /> 公開設定
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
            <Link
                to={`/user/${collection.user_name}/collections/${collection.id}`}
                className="list-group-item d-flex justify-content-between align-items-center text-dark"
            >
                <div style={{ wordBreak: 'break-all' }}>
                    <span className="oi oi-folder text-secondary mr-1" />
                    {collection.title}
                </div>
            </Link>
        );
    }
};

type SidebarProps = {
    collections?: Tissue.Collection[];
    reloadCollections: () => void;
};

const Sidebar: React.FC<SidebarProps> = ({ collections, reloadCollections }) => {
    const me = useMyProfile();
    const { username } = useParams();
    const navigate = useNavigate();
    const [showCreateModal, setShowCreateModal] = useState(false);

    const handleSubmit = async (values: CollectionFormValues) => {
        try {
            const response = await fetchPostJson('/api/collections', values);
            if (response.status === 201) {
                const createdItem = await response.json();
                showToast('作成しました', { color: 'success', delay: 5000 });
                reloadCollections();
                setShowCreateModal(false);
                navigate(`/user/${createdItem.user_name}/collections/${createdItem.id}`);
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

    if (!collections) {
        return null;
    }

    return (
        <div className="card mb-4">
            <div className="card-header d-flex justify-content-between align-items-center">
                <span>コレクション</span>
                {username === me?.name && (
                    <Button
                        variant="link"
                        className="text-secondary"
                        size="sm"
                        title="追加"
                        onClick={() => setShowCreateModal(true)}
                    >
                        <span className="oi oi-plus" />
                    </Button>
                )}
            </div>
            <div className="list-group list-group-flush">
                {collections.map((collection) => (
                    <SidebarItem key={collection.id} collection={collection} />
                ))}
                {collections.length === 0 && (
                    <li className="list-group-item d-flex justify-content-between align-items-center" />
                )}
            </div>
            <CollectionEditModal
                mode="create"
                initialValues={{ title: '', is_private: true }}
                onSubmit={handleSubmit}
                show={showCreateModal}
                onHide={() => setShowCreateModal(false)}
            />
        </div>
    );
};

const Collections: React.FC = () => {
    const { username } = useParams();
    const fetchMyProfile = useFetchMyProfile();
    const fetchMyCollections = useFetchMyCollections();
    const fetchCollections = useFetchCollections({ username: username as string });

    return (
        <MyProfileContext.Provider value={fetchMyProfile.data}>
            <div className="container">
                <div className="row">
                    <div className="col-lg-4">
                        <Sidebar collections={fetchCollections.data} reloadCollections={fetchCollections.reload} />
                    </div>
                    <div className="col-lg-8">
                        {fetchCollections.error?.response?.status === 403 ? (
                            <p className="mt-4">
                                <span className="oi oi-lock-locked" /> このユーザはチェックイン履歴を公開していません。
                            </p>
                        ) : (
                            <MyCollectionsContext.Provider value={fetchMyCollections}>
                                <CollectionsContext.Provider value={fetchCollections}>
                                    <Outlet />
                                </CollectionsContext.Provider>
                            </MyCollectionsContext.Provider>
                        )}
                    </div>
                </div>
            </div>
        </MyProfileContext.Provider>
    );
};

const Index: React.FC = () => {
    const navigate = useNavigate();
    const collections = useContext(CollectionsContext);

    useEffect(() => {
        // リスト先頭のコレクションに自動遷移
        if (collections && !collections.loading && collections.data && collections.data.length > 0) {
            const first = collections.data[0];
            navigate(`/user/${first.user_name}/collections/${first.id}`);
        }
    }, [collections?.loading]);

    if (collections && !collections.loading && collections.data?.length === 0) {
        return <p className="mt-4">コレクションがありません。</p>;
    }

    return null;
};

ReactDOM.render(
    <BrowserRouter>
        <Routes>
            <Route path="/user/:username/collections" element={<Collections />}>
                <Route index element={<Index />} />
                <Route path=":id" element={<Collection />} />
            </Route>
        </Routes>
    </BrowserRouter>,
    document.getElementById('app')
);
