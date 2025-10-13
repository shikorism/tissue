import React, { useState } from 'react';
import { Link } from 'react-router';
import { formatDate } from 'date-fns';
import Linkify from 'linkify-react';
import type { components } from '../../api/schema';
import { cn } from '../../lib/cn';
import { ExternalLink } from '../../components/ExternalLink';
import { LinkCard } from '../../components/LinkCard';
import { useCurrentUser } from '../../components/AuthProvider';
import { formatInterval } from '../../lib/formatter';
import { AddToCollectionButton } from '../collections/AddToCollectionButton';
import { Modal, ModalBody, ModalFooter, ModalHeader } from '../../components/Modal';
import { Button } from '../../components/Button';
import { ProgressButton } from '../../components/ProgressButton';
import { useDeleteCheckin, useDeleteLike, usePostLike } from '../../api/mutation';
import { toast } from 'sonner';

interface Props {
    checkin: components['schemas']['Checkin'];
    className?: string;
    intervalStyle?: 'none' | 'relative' | 'full';
    showSource?: boolean;
    showActions?: boolean;
    onDelete?: () => void;
}

export const Checkin: React.FC<Props> = ({
    checkin,
    className,
    intervalStyle = 'none',
    showSource,
    showActions,
    onDelete,
}) => {
    const { user: me } = useCurrentUser();
    const [isOpenDeleteModal, setIsOpenDeleteModal] = useState(false);
    const [isDeleted, setIsDeleted] = useState(false);
    const [isHiddenMuted, setIsHiddenMuted] = useState(true);

    const deleteCheckin = useDeleteCheckin();
    const postLike = usePostLike();
    const deleteLike = useDeleteLike();

    const handleClickLike = () => {
        if (!me) {
            toast.error('いいねするためにはログインしてください');
            return;
        }

        if (checkin.is_liked) {
            deleteLike.mutate(checkin.id, {
                onError: () => {
                    toast.error('いいねを解除できませんでした');
                },
            });
        } else {
            postLike.mutate(checkin.id, {
                onError: () => {
                    toast.error('いいねできませんでした');
                },
            });
        }
    };

    const handleClickDelete = () => {
        deleteCheckin.mutate(
            { id: checkin.id },
            {
                onSuccess: () => {
                    setIsOpenDeleteModal(false);
                    toast.success('削除しました');
                    onDelete?.();
                    setIsDeleted(true);
                },
                onError: () => {
                    toast.error('削除中にエラーが発生しました');
                },
            },
        );
    };

    if (isDeleted) {
        return null;
    }

    return (
        <article className={cn('py-4 flex flex-col gap-2 break-words', className)}>
            {intervalStyle !== 'none' ? (
                <h5>
                    <span className="text-xl font-medium mr-2">{formatCheckinInterval(checkin)}</span>
                    <Link to={`/checkin/${checkin.id}`} className="text-secondary hover:underline">
                        {intervalStyle === 'full' && !checkin.discard_elapsed_time && checkin.previous_checked_in_at
                            ? `${formatDate(checkin.previous_checked_in_at, 'yyyy/MM/dd HH:mm')} 〜 `
                            : ''}
                        {formatDate(checkin.checked_in_at, 'yyyy/MM/dd HH:mm')}
                    </Link>
                </h5>
            ) : (
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
            )}

            {(checkin.is_private || checkin.source === 'csv' || checkin.tags.length > 0) && (
                <ul className="text-2xs font-bold flex flex-wrap gap-[0.6ch]">
                    {checkin.is_private && (
                        <li className="inline-block px-2 py-1 max-w-full rounded text-black bg-warning break-all whitespace-normal">
                            <i className="ti ti-lock mr-0.5" /> 非公開
                        </li>
                    )}
                    {checkin.source === 'csv' && (
                        <li className="inline-block px-2 py-1 max-w-full rounded text-white bg-info break-all whitespace-normal">
                            <i className="ti ti-cloud-upload mr-0.5" /> インポート
                        </li>
                    )}
                    {checkin.tags.map((tag) => (
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

            {checkin.is_muted && isHiddenMuted ? (
                <div
                    className="p-2 rounded bg-neutral-100 text-center text-sm/6 select-none cursor-pointer"
                    onClick={() => setIsHiddenMuted(false)}
                >
                    このチェックインはミュートされています
                    <br />
                    クリックまたはタップで表示
                </div>
            ) : (
                <>
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
                                render: ({ attributes, content }) => (
                                    <ExternalLink {...attributes}>{content}</ExternalLink>
                                ),
                                validate: (value: string, type: string) => type === 'url',
                            }}
                        >
                            {checkin.note}
                        </Linkify>
                    )}
                </>
            )}

            {(() => {
                if (!showSource) return null;
                switch (checkin.source) {
                    case 'webhook':
                        return <p className="text-secondary text-sm">Webhookからチェックイン</p>;
                    case 'api':
                        return <p className="text-secondary text-sm">APIからチェックイン</p>;
                    default:
                        return null;
                }
            })()}

            {checkin.likes?.length ? (
                <div className="flex py-1 border-y border-gray-border items-center">
                    <div className="ml-2 mr-3 text-sm text-secondary shrink-0">
                        <strong>{checkin.likes_count}</strong> 件のいいね
                    </div>
                    <div className="h-[30px] grow overflow-hidden">
                        {checkin.likes?.map((user) => (
                            <Link key={user.name} to={`/user/${user.name}`}>
                                <img
                                    className="rounded inline-block align-bottom mr-1"
                                    src={user.profile_mini_image_url}
                                    alt={`${user.display_name}'s Avatar`}
                                    title={user.display_name}
                                    width={30}
                                    height={30}
                                />
                            </Link>
                        ))}
                    </div>
                </div>
            ) : null}

            {showActions && (
                <div className="flex gap-4">
                    <Link
                        to={{ pathname: `/checkin`, search: makeCheckinParams(checkin) }}
                        className="px-4 py-2 text-xl text-secondary rounded outline-2 outline-primary/0 focus:outline-primary/40 active:outline-primary/40 cursor-pointer"
                        title="同じオカズでチェックイン"
                    >
                        <i className="ti ti-reload" />
                    </Link>
                    <button
                        className="px-4 py-2 text-xl text-secondary rounded outline-2 outline-primary/0 focus:outline-primary/40 active:outline-primary/40 cursor-pointer"
                        title="いいね"
                        onClick={handleClickLike}
                    >
                        <i className={cn('ti ti-heart-filled', checkin.is_liked && 'text-danger')} />
                        {checkin.likes_count ? (
                            <span className="ml-2 text-base align-text-top">{checkin.likes_count}</span>
                        ) : null}
                    </button>
                    {me && checkin.link && <AddToCollectionButton link={checkin.link} tags={checkin.tags} />}
                    {me?.name === checkin.user.name ? (
                        <>
                            <Link
                                to={`/checkin/${checkin.id}/edit`}
                                className="px-4 py-2 text-xl text-secondary rounded outline-2 outline-primary/0 focus:outline-primary/40 active:outline-primary/40 cursor-pointer"
                                title="修正"
                            >
                                <i className="ti ti-edit" />
                            </Link>
                            <button
                                className="px-4 py-2 text-xl text-secondary rounded outline-2 outline-primary/0 focus:outline-primary/40 active:outline-primary/40 cursor-pointer"
                                title="削除"
                                onClick={() => setIsOpenDeleteModal(true)}
                            >
                                <i className="ti ti-trash" />
                            </button>
                        </>
                    ) : (
                        <a
                            href={`/checkin/${checkin.id}/report`}
                            className="px-4 py-2 text-xl text-secondary rounded outline-2 outline-primary/0 focus:outline-primary/40 active:outline-primary/40 cursor-pointer"
                            title="問題を報告"
                        >
                            <i className="ti ti-flag" />
                        </a>
                    )}
                </div>
            )}

            <Modal isOpen={isOpenDeleteModal} onClose={() => setIsOpenDeleteModal(false)}>
                <ModalHeader closeButton>削除確認</ModalHeader>
                <ModalBody>
                    {formatDate(checkin.checked_in_at, 'yyyy/MM/dd HH:mm ')}
                    のチェックインを削除してもよろしいですか？
                </ModalBody>
                <ModalFooter>
                    <Button onClick={() => setIsOpenDeleteModal(false)}>キャンセル</Button>
                    <ProgressButton
                        label="削除"
                        variant="danger"
                        inProgress={deleteCheckin.isPending}
                        disabled={deleteCheckin.isPending}
                        onClick={handleClickDelete}
                    />
                </ModalFooter>
            </Modal>
        </article>
    );
};

const formatCheckinInterval = (checkin: components['schemas']['Checkin']): string => {
    if (checkin.discard_elapsed_time) {
        return formatInterval(0);
    }
    if (checkin.checkin_interval) {
        return formatInterval(checkin.checkin_interval);
    }
    return '精通';
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
