import * as React from 'react';
import { Metadata, MetadataLoadState } from '../checkin';
import * as classNames from 'classnames';

type Suggestion = {
    name: string;
    used: boolean;
};

type MetadataPreviewProps = {
    state: MetadataLoadState;
    metadata: Metadata | null;
    tags: string[];
    handleAddTag: (tag: string) => void;
};

const MetadataLoading = () => (
    <div className="row no-gutters">
        <div className="col-12">
            <div className="card-body">
                <h6 className="card-title text-center font-weight-bold text-info">
                    <span className="oi oi-loop-circular" /> オカズの情報を読み込んでいます…
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
                    <span className="oi oi-circle-x" /> オカズの情報を読み込めませんでした
                </h6>
            </div>
        </div>
    </div>
);

export const MetadataPreview: React.FC<MetadataPreviewProps> = ({ state, metadata, tags, handleAddTag }) => {
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
            'metadata-tag-item': true,
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
                            <div v-if="hasImage" className="col-4 justify-content-center align-items-center">
                                <img src={metadata?.image} alt="Thumbnail" className="w-100 bg-secondary" />
                            </div>
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
                                                        onClick={() => handleAddTag(tag.name)}
                                                    >
                                                        <span className="oi oi-tag" /> {tag.name}
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
