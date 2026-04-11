import React from 'react';
import { useQuery } from '@tanstack/react-query';
import { getMetadataQuery } from '../api/query';

type MetadataPreviewProps = {
    link: string;
    tags: string[];
    onClickTag: (tag: string) => void;
};

const MetadataLoading = () => (
    <div className="w-full h-[150px] p-4">
        <h6 className="text-xs text-center font-bold text-info">
            <i className="ti ti-loader" /> オカズの情報を読み込んでいます…
        </h6>
    </div>
);

const MetadataLoadFailed = () => (
    <div className="w-full h-[150px] p-4">
        <h6 className="text-xs text-center font-bold text-danger">
            <i className="ti ti-circle-x" /> オカズの情報を読み込めませんでした
        </h6>
    </div>
);

export const MetadataPreview: React.FC<MetadataPreviewProps> = ({ link, tags, onClickTag }) => {
    const isValidLink = link.trim() !== '' && /^https?:\/\//.test(link);
    const { data: metadata, isLoading } = useQuery({ ...getMetadataQuery(link), enabled: isValidLink });

    if (!isValidLink) {
        return null;
    }
    const hasImage = metadata?.image !== '';
    const suggestions =
        metadata?.tags.map((t) => ({
            name: t.name,
            used: tags.indexOf(t.name) !== -1,
        })) ?? [];

    return (
        <div className="rounded border border-gray-border flex overflow-hidden">
            {isLoading ? (
                <MetadataLoading />
            ) : metadata ? (
                <>
                    {hasImage && (
                        <div className="flex w-1/3 min-h-[150px] items-center justify-center relative">
                            <img src={metadata.image} alt="Thumbnail" className="w-full absolute" />
                        </div>
                    )}
                    <div className="p-4 w-2/3">
                        <h6 className="text-sm font-bold">{metadata.title}</h6>
                        {suggestions.length > 0 && (
                            <>
                                <p className="text-xs mt-2 mb-2">
                                    タグ候補
                                    <br />
                                    <span className="text-secondary">(クリックするとタグ入力欄にコピーできます)</span>
                                </p>
                                <ul className="text-2xs font-bold flex flex-wrap gap-[0.6ch]">
                                    {suggestions.map((tag) => (
                                        <li
                                            key={tag.name}
                                            className="inline-block px-2 py-1 max-w-full rounded text-white bg-primary-500 not-aria-disabled:hover:bg-primary-700 break-all whitespace-normal cursor-pointer select-none transition-colors aria-disabled:bg-neutral-500 aria-disabled:cursor-default"
                                            aria-disabled={tag.used}
                                            onClick={() => !tag.used && onClickTag(tag.name)}
                                        >
                                            <i className="ti ti-tag-filled" /> {tag.name}
                                        </li>
                                    ))}
                                </ul>
                            </>
                        )}
                    </div>
                </>
            ) : (
                <MetadataLoadFailed />
            )}
        </div>
    );
};
