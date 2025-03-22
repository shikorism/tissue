import React, { useEffect, useState } from 'react';
import classNames from 'classnames';
import { fetchGet, ResponseError } from '../fetch';

enum MetadataLoadState {
    Inactive,
    Loading,
    Success,
    Failed,
}

type Metadata = {
    url: string;
    title: string;
    description: string;
    image: string;
    expires_at: string | null;
    tags: {
        name: string;
    }[];
};

type Suggestion = {
    name: string;
    used: boolean;
};

type MetadataPreviewProps = {
    link: string;
    tags: string[];
    onClickTag: (tag: string) => void;
};

const MetadataLoading = () => (
    <div className="row no-gutters">
        <div className="col-12">
            <div className="card-body">
                <h6 className="card-title text-center font-weight-bold text-info">
                    <i className="ti ti-loader" /> オカズの情報を読み込んでいます…
                </h6>
            </div>
        </div>
    </div>
);

const MetadataLoadFailed = () => (
    <div className="row no-gutters">
        <div className="col-12">
            <div className="card-body">
                <h6 className="card-title text-center font-weight-bold text-danger">
                    <i className="ti ti-circle-x" /> オカズの情報を読み込めませんでした
                </h6>
            </div>
        </div>
    </div>
);

export const MetadataPreview: React.FC<MetadataPreviewProps> = ({ link, tags, onClickTag }) => {
    const [state, setState] = useState(MetadataLoadState.Inactive);
    const [metadata, setMetadata] = useState<Metadata | null>(null);

    useEffect(() => {
        if (link.trim() === '' || !/^https?:\/\//.test(link)) {
            setState(MetadataLoadState.Inactive);
            setMetadata(null);
            return;
        }

        setState(MetadataLoadState.Loading);
        fetchGet('/api/checkin/card', { url: link })
            .then((response) => {
                if (!response.ok) {
                    throw new ResponseError(response);
                }
                return response.json();
            })
            .then((data) => {
                setState(MetadataLoadState.Success);
                setMetadata(data);
            })
            .catch(() => {
                setState(MetadataLoadState.Failed);
                setMetadata(null);
            });
    }, [link]);

    if (state === MetadataLoadState.Inactive) {
        return null;
    }
    const hasImage = metadata !== null && metadata.image !== '';
    const descClasses = classNames({
        'col-8': hasImage,
        'col-12': !hasImage,
    });
    const tagClasses = (s: Suggestion) =>
        classNames({
            'list-inline-item': true,
            badge: true,
            'badge-primary': !s.used,
            'badge-secondary': s.used,
            'tis-metadata-preview-tag-item': true,
        });
    const suggestions =
        metadata?.tags.map((t) => ({
            name: t.name,
            used: tags.indexOf(t.name) !== -1,
        })) ?? [];

    return (
        <div className="form-row">
            <div className="form-group col-sm-12">
                <div className="card tis-metadata-preview-link-card mb-2 px-0">
                    {state === MetadataLoadState.Loading ? (
                        <MetadataLoading />
                    ) : state === MetadataLoadState.Success ? (
                        <div className="row no-gutters">
                            {hasImage && (
                                <div className="col-4 justify-content-center align-items-center">
                                    <img src={metadata?.image} alt="Thumbnail" className="w-100 bg-secondary" />
                                </div>
                            )}
                            <div className={descClasses}>
                                <div className="card-body">
                                    <h6 className="card-title font-weight-bold">{metadata?.title}</h6>
                                    {suggestions.length > 0 && (
                                        <>
                                            <p className="card-text mb-2">
                                                タグ候補
                                                <br />
                                                <span className="text-secondary">
                                                    (クリックするとタグ入力欄にコピーできます)
                                                </span>
                                            </p>
                                            <ul className="list-inline d-inline">
                                                {suggestions.map((tag) => (
                                                    <li
                                                        key={tag.name}
                                                        className={tagClasses(tag)}
                                                        onClick={() => !tag.used && onClickTag(tag.name)}
                                                    >
                                                        <i className="ti ti-tag-filled" /> {tag.name}
                                                    </li>
                                                ))}
                                            </ul>
                                        </>
                                    )}
                                </div>
                            </div>
                        </div>
                    ) : (
                        <MetadataLoadFailed />
                    )}
                </div>
            </div>
        </div>
    );
};
