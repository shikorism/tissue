import React from 'react';
import { useMyProfileQuery, useRecentTagsQuery, useUserStatsTagsQuery } from '../api';
import classNames from 'classnames';

type Candidate = {
    name: string;
    used: boolean;
};

type FavoriteTagsProps = {
    tags: string[];
    onClickTag: (tag: string) => void;
};

export const FavoriteTags: React.FC<FavoriteTagsProps> = ({ tags, onClickTag }) => {
    const { data: me } = useMyProfileQuery();
    const { data: mostlyUsedTags } = useUserStatsTagsQuery(me?.name);
    const { data: mostlyUsedMetaTags } = useUserStatsTagsQuery(me?.name, true);
    const { data: recentlyUsedTags } = useRecentTagsQuery();

    if (!mostlyUsedTags || !mostlyUsedMetaTags || !recentlyUsedTags) return null;

    const mostlyCandidates: Candidate[] = Array.from(
        [...mostlyUsedTags, ...mostlyUsedMetaTags].reduce((set, tag) => set.add(tag.name), new Set<string>()),
    ).map((t) => ({ name: t, used: tags.indexOf(t) !== -1 }));

    const recentlyCandidates: Candidate[] = recentlyUsedTags
        .filter((_, i) => i < 20)
        .map((t) => ({ name: t, used: tags.indexOf(t) !== -1 }));

    if (mostlyCandidates.length === 0 && recentlyCandidates.length === 0) return null;

    const tagClasses = (t: Candidate) =>
        classNames({
            'list-inline-item': true,
            badge: true,
            'badge-primary': !t.used,
            'badge-secondary': t.used,
            'cursor-pointer': true,
        });

    return (
        <div className="card">
            <div className="card-body px-3 py-2 d-flex flex-column" style={{ gap: '1rem' }}>
                {mostlyCandidates.length > 0 && (
                    <div>
                        <div className="small">
                            <i className="ti ti-heart mr-1" />
                            よく使うタグ
                        </div>
                        <ul className="list-inline d-inline">
                            {mostlyCandidates.map((tag) => (
                                <li
                                    key={tag.name}
                                    className={tagClasses(tag)}
                                    onClick={() => !tag.used && onClickTag(tag.name)}
                                >
                                    <i className="ti ti-tag-filled" /> {tag.name}
                                </li>
                            ))}
                        </ul>
                    </div>
                )}
                {recentlyCandidates.length > 0 && (
                    <div>
                        <div className="small">
                            <i className="ti ti-history mr-1" />
                            最近使ったタグ
                        </div>
                        <ul className="list-inline d-inline">
                            {recentlyCandidates.map((tag) => (
                                <li
                                    key={tag.name}
                                    className={tagClasses(tag)}
                                    onClick={() => !tag.used && onClickTag(tag.name)}
                                >
                                    <i className="ti ti-tag-filled" /> {tag.name}
                                </li>
                            ))}
                        </ul>
                    </div>
                )}
                <div className="text-secondary small">クリックするとタグ入力欄にコピーできます</div>
            </div>
        </div>
    );
};
