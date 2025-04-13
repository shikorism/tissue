import React from 'react';
import { Link } from 'react-router';
import { formatDate } from 'date-fns';
import Linkify from 'linkify-react';
import type { components } from '../api/schema';
import { cn } from '../lib/cn';
import { ExternalLink } from './ExternalLink';
import { LinkCard } from './LinkCard';

interface Props {
    checkin: components['schemas']['Checkin'];
    className?: string;
}

export const Checkin: React.FC<Props> = ({ checkin, className }) => (
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
    </article>
);
