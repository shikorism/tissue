import React from 'react';
import { useSuspenseQueries } from '@tanstack/react-query';
import { useCurrentUser } from '../../components/AuthProvider';
import { getRecentTagsQuery, getUserStatsTagsQuery } from '../../api/query';

type Candidate = {
    name: string;
    used: boolean;
};

type FavoriteTagsProps = {
    tags: string[];
    onClickTag: (tag: string) => void;
};

export const FavoriteTags: React.FC<FavoriteTagsProps> = ({ tags, onClickTag }) => {
    const { user: me } = useCurrentUser();
    if (!me) return null;

    const [{ data: mostlyUsedTags }, { data: mostlyUsedMetaTags }, { data: recentlyUsedTags }] = useSuspenseQueries({
        queries: [
            getUserStatsTagsQuery(me.name),
            getUserStatsTagsQuery(me.name, { includes_metadata: true }),
            getRecentTagsQuery(),
        ],
    });

    const mostlyCandidates: Candidate[] = Array.from(
        [...mostlyUsedTags, ...mostlyUsedMetaTags].reduce((set, tag) => set.add(tag.name), new Set<string>()),
    ).map((t) => ({ name: t, used: tags.indexOf(t) !== -1 }));

    const recentlyCandidates: Candidate[] = recentlyUsedTags
        .filter((_, i) => i < 20)
        .map((t) => ({ name: t, used: tags.indexOf(t) !== -1 }));

    if (mostlyCandidates.length === 0 && recentlyCandidates.length === 0) return null;

    return (
        <div className="px-4 py-3 rounded border-1 border-gray-border flex flex-col gap-4">
            {mostlyCandidates.length > 0 && (
                <div>
                    <div className="text-sm mb-1">
                        <i className="ti ti-heart mr-1" />
                        よく使うタグ
                    </div>
                    <ul className="text-2xs font-bold flex flex-wrap gap-[0.6ch]">
                        {mostlyCandidates.map((tag) => (
                            <Tag key={tag.name} tag={tag} onClick={() => !tag.used && onClickTag(tag.name)} />
                        ))}
                    </ul>
                </div>
            )}
            {recentlyCandidates.length > 0 && (
                <div>
                    <div className="text-sm mb-1">
                        <i className="ti ti-history mr-1" />
                        最近使ったタグ
                    </div>
                    <ul className="text-2xs font-bold flex flex-wrap gap-[0.6ch]">
                        {recentlyCandidates.map((tag) => (
                            <Tag key={tag.name} tag={tag} onClick={() => !tag.used && onClickTag(tag.name)} />
                        ))}
                    </ul>
                </div>
            )}
            <div className="text-secondary text-sm">クリックするとタグ入力欄にコピーできます</div>
        </div>
    );
};

interface TagProps {
    tag: Candidate;
    onClick: () => void;
}

const Tag: React.FC<TagProps> = ({ tag, onClick }) => (
    <li
        className="inline-block px-2 py-1 max-w-full rounded text-white bg-primary-500 not-aria-disabled:hover:bg-primary-700 break-all whitespace-normal cursor-pointer select-none transition-colors aria-disabled:bg-neutral-500 aria-disabled:cursor-default"
        aria-disabled={tag.used}
        onClick={onClick}
    >
        <i className="ti ti-tag-filled" /> {tag.name}
    </li>
);
