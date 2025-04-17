import React from 'react';
import { Link } from 'react-router';
import { formatDate } from 'date-fns';
import Linkify from 'linkify-react';
import type { components } from '../api/schema';
import { cn } from '../lib/cn';
import { ExternalLink } from './ExternalLink';
import { LinkCard } from './LinkCard';
import { useGetMe } from '../api/hooks';

interface Props {
    checkin: components['schemas']['Checkin'];
    className?: string;
    showActions?: boolean;
}

export const Checkin: React.FC<Props> = ({ checkin, className, showActions }) => {
    const { data: me } = useGetMe();

    return (
        <article className={cn('py-4 flex flex-col gap-2 break-words', className)}>
            <h5>
                <Link to={`/user/${checkin.user.name}`} className="mr-1 hover:underline">
                    <img
                        className="rounded inline-block align-bottom mr-1"
                        src={checkin.user.profile_mini_image_url}
                        alt={`${checkin.user.display_name}'s Avatar`}
                        width={30}
                        height={30}
                    />
                    <bdi className="text-xl font-medium">{checkin.user.display_name}</bdi>
                </Link>
                <Link to={`/checkin/${checkin.id}`} className="text-secondary hover:underline">
                    {formatDate(checkin.checked_in_at, 'yyyy/MM/dd HH:mm')}
                </Link>
            </h5>

            {(checkin.is_private || checkin.source === 'csv' || checkin.tags.length > 0) && (
                <ul className="text-xs font-bold text-white flex flex-wrap gap-[0.6ch]">
                    {checkin.is_private && <li>非公開</li>}
                    {checkin.source === 'csv' && <li>インポート</li>}
                    {checkin.tags.map((tag) => (
                        <li key={tag}>
                            <Link
                                to={{ pathname: `/search`, search: `?q=${tag}` }}
                                className="inline-block px-2 py-1 max-w-full rounded bg-neutral-500 hover:bg-neutral-600 break-all whitespace-normal"
                            >
                                <i className="ti ti-tag-filled mr-0.5" />
                                {tag}
                            </Link>
                        </li>
                    ))}
                </ul>
            )}

            {checkin.link && (
                <>
                    <LinkCard link={checkin.link} isTooSensitive={checkin.is_too_sensitive} />
                    <p className="flex items-baseline">
                        <i className="ti ti-link mr-1" />
                        <ExternalLink className="overflow-hidden" href={checkin.link}>
                            {checkin.link}
                        </ExternalLink>
                    </p>
                </>
            )}

            {checkin.note && (
                <Linkify
                    as="p"
                    options={{
                        nl2br: true,
                        render: ({ attributes, content }) => <ExternalLink {...attributes}>{content}</ExternalLink>,
                        validate: (value: string, type: string) => type === 'url',
                    }}
                >
                    {checkin.note}
                </Linkify>
            )}

            {/* TODO: source, muted overlay, likes */}

            {showActions && (
                <div className="flex gap-4">
                    <Link
                        to={{ pathname: `/checkin`, search: makeCheckinParams(checkin) }}
                        className="px-4 py-2 text-xl rounded outline-2 outline-primary/0 focus:outline-primary/40 active:outline-primary/40 cursor-pointer"
                        title="同じオカズでチェックイン"
                    >
                        <i className="ti ti-reload" />
                    </Link>
                    <button
                        className="px-4 py-2 text-xl rounded outline-2 outline-primary/0 focus:outline-primary/40 active:outline-primary/40 cursor-pointer"
                        title="いいね"
                    >
                        <i className={cn('ti ti-heart-filled', checkin.is_liked && 'text-danger')} />
                    </button>
                    {me && checkin.link && (
                        <button
                            className="px-4 py-2 text-xl rounded outline-2 outline-primary/0 focus:outline-primary/40 active:outline-primary/40 cursor-pointer"
                            title="コレクションに追加"
                        >
                            <i className="ti ti-folder-plus" />
                        </button>
                    )}
                    {me?.name === checkin.user.name ? (
                        <>
                            <Link
                                to={`/checkin/${checkin.id}/edit`}
                                className="px-4 py-2 text-xl rounded outline-2 outline-primary/0 focus:outline-primary/40 active:outline-primary/40 cursor-pointer"
                                title="修正"
                            >
                                <i className="ti ti-edit" />
                            </Link>
                            <button
                                className="px-4 py-2 text-xl rounded outline-2 outline-primary/0 focus:outline-primary/40 active:outline-primary/40 cursor-pointer"
                                title="削除"
                            >
                                <i className="ti ti-trash" />
                            </button>
                        </>
                    ) : (
                        <button
                            className="px-4 py-2 text-xl rounded outline-2 outline-primary/0 focus:outline-primary/40 active:outline-primary/40 cursor-pointer"
                            title="問題を報告"
                        >
                            <i className="ti ti-flag" />
                        </button>
                    )}
                </div>
            )}
        </article>
    );
};

const makeCheckinParams = (checkin: components['schemas']['Checkin']): string => {
    const params = new URLSearchParams();
    params.set('link', checkin.link);
    params.set('note', checkin.note);
    params.set('tags', checkin.tags.join(' '));
    params.set('is_private', checkin.is_private.toString());
    params.set('is_too_sensitive', checkin.is_too_sensitive.toString());
    return params.toString();
};
