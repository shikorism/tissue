import React from 'react';
import { Link, useLoaderData } from 'react-router';
import { useSuspenseQuery } from '@tanstack/react-query';
import Linkify from 'linkify-react';
import { getUserCheckinsQuery, getUserQuery, getUserStatsTagsQuery } from '../api/query';
import type { components } from '../api/schema';
import { Checkin } from '../components/Checkin';
import { ExternalLink } from '../components/ExternalLink';
import { LoaderData } from './UserProfile.loader';
import { formatInterval, formatNumber, formatOrDefault } from '../lib/formatter';
import { format, subSeconds } from 'date-fns';
import { useCurrentUser } from '../components/AuthProvider';

export const UserProfile: React.FC = () => {
    const { user: me } = useCurrentUser();
    const { username } = useLoaderData<LoaderData>();
    const { data: user } = useSuspenseQuery(getUserQuery(username));

    return (
        <div className="p-4 flex flex-col *:not-first:mt-6 *:not-first:pt-6 *:not-first:border-t-1 *:not-first:border-gray-border">
            <Biography user={user} />
            {!user.is_protected || user.name === me?.name ? (
                <>
                    <Activity user={user} />
                    <RecentCheckin user={user} />
                </>
            ) : (
                <div>
                    <i className="ti ti-lock" /> このユーザはチェックイン履歴を公開していません。
                </div>
            )}
        </div>
    );
};

interface BiographyProps {
    user: components['schemas']['User'];
}

const Biography: React.FC<BiographyProps> = ({ user }) => {
    if (!user.bio && !user.url) {
        return null;
    }

    return (
        <div className="flex flex-col gap-4">
            {user.bio && (
                <Linkify
                    as="p"
                    options={{
                        nl2br: true,
                        render: ({ attributes, content }) => <ExternalLink {...attributes}>{content}</ExternalLink>,
                        validate: (value: string, type: string) => type === 'url',
                    }}
                >
                    {user.bio}
                </Linkify>
            )}

            {user.url && (
                <p>
                    <i className="ti ti-link mr-1 mt-1" />
                    <ExternalLink href={user.url}>{user.url.replace(/^https?:\/\//, '')}</ExternalLink>
                </p>
            )}
        </div>
    );
};

interface ActivityProps {
    user: components['schemas']['User'];
}

const Activity: React.FC<ActivityProps> = ({ user }) => {
    const { data: tags } = useSuspenseQuery(getUserStatsTagsQuery(user.name));

    return (
        <div>
            <div className="flex items-baseline gap-4">
                <h2 className="text-xl font-bold">アクティビティ</h2>
                <Link to={`/user/${user.name}/stats`} className="text-primary hover:brightness-80 hover:underline">
                    グラフを見る &raquo;
                </Link>
            </div>
            <div className="mt-4 max-w-[1000px] flex flex-col md:flex-row">
                <div className="flex-1 text-start">
                    <h3 className="text-lg font-bold">現在のセッション</h3>
                    <p className="my-2 text-xl">
                        {formatOrDefault(user.checkin_summary?.current_session_elapsed, formatInterval)}
                    </p>
                    <p className="text-sm">
                        {user.checkin_summary
                            ? `${format(
                                  subSeconds(Date.now(), user.checkin_summary.current_session_elapsed),
                                  'yyyy/MM/dd HH:mm',
                              )} にリセット`
                            : '計測がまだ始まっていません'}
                    </p>
                </div>
                <table className="flex-1 text-sm mt-4 md:mt-0 self-start md:self-auto">
                    <tbody>
                        <tr>
                            <th className="pr-2 py-1 text-right md:w-2/5 after:content-[':']">通算回数</th>
                            <td>{formatOrDefault(user.checkin_summary?.total_checkins, formatNumber)}回</td>
                        </tr>
                        <tr>
                            <th className="pr-2 py-1 text-right after:content-[':']">平均記録</th>
                            <td>{formatOrDefault(user.checkin_summary?.average_interval, formatInterval)}</td>
                        </tr>
                        <tr>
                            <th className="pr-2 py-1 text-right after:content-[':']">中央値</th>
                            <td>{formatOrDefault(user.checkin_summary?.median_interval, formatInterval)}</td>
                        </tr>
                    </tbody>
                </table>
                <table className="flex-1 text-sm self-start md:self-auto">
                    <tbody>
                        <tr>
                            <th className="pr-2 py-1 text-right md:w-2/5 after:content-[':']">最長記録</th>
                            <td>{formatOrDefault(user.checkin_summary?.longest_interval, formatInterval)}</td>
                        </tr>
                        <tr>
                            <th className="pr-2 py-1 text-right after:content-[':']">最短記録</th>
                            <td>{formatOrDefault(user.checkin_summary?.shortest_interval, formatInterval)}</td>
                        </tr>
                        <tr>
                            <th className="pr-2 py-1 text-right after:content-[':']">合計時間</th>
                            <td>{formatOrDefault(user.checkin_summary?.total_times, formatInterval)}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <h3 className="mt-4 text-lg font-bold">よく使っているタグ</h3>
            <ul className="mt-2 flex flex-wrap gap-3">
                {tags.map((tag) => (
                    <li key={tag.name}>
                        <Link
                            to={{ pathname: `/search`, search: `?q=${tag.name}` }}
                            className="group inline-block max-w-full break-all whitespace-normal"
                        >
                            <i className="ti ti-tag mr-1" />
                            <span className="text-primary group-hover:brightness-80 group-hover:underline">
                                {tag.name}
                            </span>
                        </Link>
                    </li>
                ))}
            </ul>
        </div>
    );
};

interface RecentCheckinProps {
    user: components['schemas']['User'];
}

const RecentCheckin: React.FC<RecentCheckinProps> = ({ user }) => {
    const { data: checkins } = useSuspenseQuery(getUserCheckinsQuery(user.name));

    return (
        <div>
            <div className="flex items-baseline gap-4">
                <h2 className="text-xl font-bold">最近のチェックイン</h2>
                <Link to={`/user/${user.name}/checkins`} className="text-primary hover:brightness-80 hover:underline">
                    もっと見る &raquo;
                </Link>
            </div>
            {checkins.data.length >= 1 ? <Checkin checkin={checkins.data[0]} /> : null}
        </div>
    );
};
