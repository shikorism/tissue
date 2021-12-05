import React from 'react';
import ReactDOM from 'react-dom';
import { BrowserRouter, Link, Route, Routes, useParams, useSearchParams } from 'react-router-dom';
import { LinkCard } from '../components/LinkCard';
import { MyProfileContext, useMyProfile } from '../context';
import { useFetchMyProfile, useFetchCollections, useFetchCollectionItems } from '../api';
import { Pagination } from '../components/Pagination';

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

type CollectionItemProps = {
    collectionId: string;
    item: Tissue.CollectionItem;
};

const CollectionItem: React.FC<CollectionItemProps> = ({ collectionId, item }) => {
    const me = useMyProfile();
    const { username } = useParams();

    // TODO: react-bootstrapで制御すべき
    const actionsRef = (el: HTMLDivElement | null) => {
        if (el) {
            $(el).find('[data-toggle="tooltip"], [data-tooltip="tooltip"]').tooltip();
        }
    };

    return (
        <>
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
            <div ref={actionsRef} className="ejaculation-actions">
                <button
                    type="button"
                    className="btn btn-link text-secondary"
                    data-toggle="tooltip"
                    data-placement="bottom"
                    title="このオカズでチェックイン"
                    data-href={item.checkin_url}
                >
                    <span className="oi oi-check" />
                </button>
                <span className="dropdown">
                    <button
                        type="button"
                        className="btn btn-link text-secondary"
                        data-toggle="dropdown"
                        data-tooltip="tooltip"
                        data-placement="bottom"
                        data-trigger="hover"
                        title="コレクションに追加"
                    >
                        <span className="oi oi-plus" />
                    </button>
                    <div className="dropdown-menu">
                        <h6 className="dropdown-header">コレクションに追加</h6>
                        <button type="button" className="dropdown-item use-later-button" data-link={item.link}>
                            あとで抜く
                        </button>
                    </div>
                </span>
                {username === me?.name && (
                    <>
                        <button
                            type="button"
                            className="btn btn-link text-secondary"
                            data-toggle="tooltip"
                            data-placement="bottom"
                            title="修正"
                            data-href=""
                        >
                            <span className="oi oi-pencil" />
                        </button>
                        <button
                            type="button"
                            className="btn btn-link text-secondary"
                            data-toggle="tooltip"
                            data-placement="bottom"
                            title="削除"
                            data-target="#deleteCollectionItemModal"
                            data-collection-id={collectionId}
                            data-item-id={item.id}
                            data-link={item.link}
                        >
                            <span className="oi oi-trash" />
                        </button>
                    </>
                )}
            </div>
        </>
    );
};

const Collection: React.FC = () => {
    const { id } = useParams();
    const [searchParams] = useSearchParams();
    const { loading, data, totalCount } = useFetchCollectionItems({ id: id as string, page: searchParams.get('page') });

    if (!data) {
        return null;
    }

    // TODO: pagination
    return (
        <>
            <ul className="list-group">
                {loading ? null : data.length === 0 ? (
                    <li className="list-group-item border-bottom-only">
                        <p>このコレクションにはまだオカズが登録されていません。</p>
                    </li>
                ) : (
                    data.map((item) => (
                        <li key={item.id} className="list-group-item border-bottom-only pt-3 pb-3 text-break">
                            <CollectionItem collectionId={id as string} item={item} />
                        </li>
                    ))
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
