import React from 'react';
import Linkify from 'linkify-react';
import { LinkCard } from '../../components/LinkCard';
import { ExternalLink } from '../../components/ExternalLink';
import { components } from '../../api/schema';
import { cn } from '../../lib/cn';
import { Link } from 'react-router';
import { useCurrentUser } from '../../components/AuthProvider';

interface Props {
    collection: components['schemas']['Collection'];
    item: components['schemas']['CollectionItem'];
    className?: string;
}

export const CollectionItem: React.FC<Props> = ({ collection, item, className }) => {
    const { user: me } = useCurrentUser();

    return (
        <div className={cn('py-4 flex flex-col gap-2 break-words', className)}>
            <LinkCard link={item.link} />
            <p className="flex items-baseline">
                <i className="ti ti-link mr-1" />
                <ExternalLink className="overflow-hidden" href={item.link}>
                    {item.link}
                </ExternalLink>
            </p>

            {item.tags.length > 0 && (
                <ul className="text-2xs font-bold flex flex-wrap gap-[0.6ch]">
                    {item.tags.map((tag) => (
                        <li key={tag}>
                            <Link
                                to={{ pathname: `/search`, search: `?q=${tag}` }}
                                className="inline-block px-2 py-1 max-w-full rounded text-white bg-neutral-500 hover:bg-neutral-600 break-all whitespace-normal"
                            >
                                <i className="ti ti-tag-filled mr-0.5" />
                                {tag}
                            </Link>
                        </li>
                    ))}
                </ul>
            )}

            {item.note && (
                <Linkify
                    as="p"
                    options={{
                        nl2br: true,
                        render: ({ attributes, content }) => <ExternalLink {...attributes}>{content}</ExternalLink>,
                        validate: (value: string, type: string) => type === 'url',
                    }}
                >
                    {item.note}
                </Linkify>
            )}

            <div className="flex gap-4">
                <Link
                    to={{ pathname: `/checkin`, search: makeCheckinParams(item) }}
                    className="px-4 py-2 text-xl text-secondary rounded outline-2 outline-primary/0 focus:outline-primary/40 active:outline-primary/40 cursor-pointer"
                    title="同じオカズでチェックイン"
                >
                    <i className="ti ti-reload" />
                </Link>
                {me && (
                    <button
                        className="px-4 py-2 text-xl text-secondary rounded outline-2 outline-primary/0 focus:outline-primary/40 active:outline-primary/40 cursor-pointer"
                        title="コレクションに追加"
                    >
                        <i className="ti ti-folder-plus" />
                    </button>
                )}
                {me?.name === collection.user_name && (
                    <>
                        <button
                            className="px-4 py-2 text-xl text-secondary rounded outline-2 outline-primary/0 focus:outline-primary/40 active:outline-primary/40 cursor-pointer"
                            title="修正"
                        >
                            <i className="ti ti-edit" />
                        </button>
                        <button
                            className="px-4 py-2 text-xl text-secondary rounded outline-2 outline-primary/0 focus:outline-primary/40 active:outline-primary/40 cursor-pointer"
                            title="削除"
                        >
                            <i className="ti ti-trash" />
                        </button>
                    </>
                )}
            </div>
        </div>
    );
};

const makeCheckinParams = (item: components['schemas']['CollectionItem']): string => {
    const params = new URLSearchParams();
    params.set('link', item.link);
    params.set('note', item.note);
    params.set('tags', item.tags.join(' '));
    return params.toString();
};
