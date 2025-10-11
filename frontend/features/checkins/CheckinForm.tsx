import React, { FormEventHandler, useEffect, useState } from 'react';
import { format } from 'date-fns';
import { TagInput } from '../../components/TagInput';
import { FieldError } from '../../components/FieldError';
import { cn } from '../../lib/cn';
import { MetadataPreview } from '../../components/MetadataPreview';
import { ProgressButton } from '../../components/ProgressButton';
import { FavoriteTags } from './FavoriteTags';

export interface CheckinFormValues {
    date: string;
    time: string;
    tags: string[];
    link: string;
    note: string;
    is_realtime: boolean;
    is_private: boolean;
    is_too_sensitive: boolean;
    discard_elapsed_time: boolean;
}

export interface CheckinFormErrors {
    checked_in_at?: string[];
    tags?: string[];
    link?: string[];
    note?: string[];
    is_realtime?: string[];
    is_private?: string[];
    is_too_sensitive?: string[];
    discard_elapsed_time?: string[];
}

export class CheckinFormValidationError extends Error {
    errors: CheckinFormErrors;

    constructor(errors: CheckinFormErrors, ...rest: any) {
        super(...rest);
        this.name = 'CheckinFormValidationError';
        this.errors = errors;
    }
}

export type SubmitHandler = (values: CheckinFormValues) => Promise<void>;

interface CheckinFormProps {
    mode: 'create' | 'edit';
    initialValues: Partial<CheckinFormValues>;
    onSubmit: SubmitHandler;
}

export const CheckinForm: React.FC<CheckinFormProps> = ({ mode, initialValues, onSubmit }) => {
    const MAX_NOTE = 500;

    const [date, setDate] = useState<string>(initialValues.date || '');
    const [time, setTime] = useState<string>(initialValues.time || '');
    const [tags, setTags] = useState<string[]>(initialValues.tags || []);
    const [link, setLink] = useState<string>(initialValues.link || '');
    const [linkForPreview, setLinkForPreview] = useState(link);
    const [note, setNote] = useState<string>(initialValues.note || '');
    const [remainingChars, setRemainingChars] = useState<number>(MAX_NOTE);
    const [isRealtime, setRealtime] = useState<boolean>(!!(mode === 'create' && initialValues.is_realtime));
    const [isPrivate, setPrivate] = useState<boolean>(!!initialValues.is_private);
    const [isTooSensitive, setTooSensitive] = useState<boolean>(!!initialValues.is_too_sensitive);
    const [discardElapsedTime, setDiscardElapsedTime] = useState<boolean>(!!initialValues.discard_elapsed_time);

    const [submitting, setSubmitting] = useState(false);
    const [errors, setErrors] = useState<CheckinFormErrors>({});

    useEffect(() => {
        if (mode === 'create' && isRealtime) {
            const id = setInterval(() => {
                const now = new Date();
                setDate(format(now, 'yyyy-MM-dd'));
                setTime(format(now, 'HH:mm'));
            }, 500);
            return () => clearInterval(id);
        }
    }, [mode, isRealtime]);
    useEffect(() => {
        setRemainingChars(MAX_NOTE - [...note].length);
    }, [note]);

    const handleSubmit: FormEventHandler<HTMLFormElement> = async (event) => {
        event.preventDefault();
        event.stopPropagation();
        setSubmitting(true);
        try {
            await onSubmit({
                date,
                time,
                tags,
                link,
                note,
                is_realtime: isRealtime,
                is_private: isPrivate,
                is_too_sensitive: isTooSensitive,
                discard_elapsed_time: discardElapsedTime,
            });
        } catch (e) {
            if (e instanceof CheckinFormValidationError) {
                setErrors(e.errors);
                return;
            }
            throw e;
        } finally {
            setSubmitting(false);
        }
    };

    return (
        <form className="flex flex-col gap-4 py-4" onSubmit={handleSubmit}>
            <div className="flex flex-col gap-2">
                {mode === 'create' && (
                    <label>
                        <input
                            id="isRealtime"
                            type="checkbox"
                            className="accent-primary"
                            checked={isRealtime}
                            onChange={(e) => setRealtime(e.target.checked)}
                        />
                        <span className="ml-2">現在時刻でチェックイン</span>
                    </label>
                )}

                <div className="flex gap-4">
                    <div className="flex-1">
                        <label htmlFor="date" className="block mb-2">
                            <i className="ti ti-calendar-event" /> 日付
                        </label>
                        <input
                            type="date"
                            id="date"
                            name="date"
                            className={cn(
                                'block w-full rounded border px-3 py-2 transition duration-150 ease-in-out focus:outline-none focus:ring-4 disabled:text-neutral-600 disabled:bg-neutral-200',
                                errors?.checked_in_at
                                    ? 'border-danger focus:ring-danger/25'
                                    : 'border-neutral-300 focus:border-primary-400 focus:ring-primary-400/25',
                            )}
                            pattern="^20[0-9]{2}[-/](0[1-9]|1[0-2])[-/](0[1-9]|[12][0-9]|3[01])$"
                            required
                            value={date}
                            onChange={(e) => setDate(e.target.value)}
                            disabled={isRealtime}
                        />
                    </div>
                    <div className="flex-1">
                        <label htmlFor="date" className="block mb-2">
                            <i className="ti ti-clock" /> 時刻
                        </label>
                        <input
                            type="time"
                            id="time"
                            name="time"
                            className={cn(
                                'block w-full rounded border px-3 py-2 transition duration-150 ease-in-out focus:outline-none focus:ring-4 disabled:text-neutral-600 disabled:bg-neutral-200',
                                errors?.checked_in_at
                                    ? 'border-danger focus:ring-danger/25'
                                    : 'border-neutral-300 focus:border-primary-400 focus:ring-primary-400/25',
                            )}
                            pattern="^([01][0-9]|2[0-3]):[0-5][0-9]$"
                            required
                            value={time}
                            onChange={(e) => setTime(e.target.value)}
                            disabled={isRealtime}
                        />
                    </div>
                </div>

                <FieldError name="checked_in_at" label="日時" errors={errors?.checked_in_at} />
            </div>

            <div>
                <label htmlFor="tagInput" className="block mb-2">
                    <i className="ti ti-tags" /> タグ
                </label>
                <TagInput
                    id="tagInput"
                    name="tags"
                    values={tags}
                    isInvalid={!!errors?.tags}
                    onChange={(v) => setTags(v)}
                />
                <div className="mt-1 text-xs text-secondary">Tab, Enter, 半角スペースのいずれかで入力確定します。</div>
                <FieldError name="tags" label="タグ" errors={errors?.tags} />
            </div>
            <FavoriteTags tags={tags} onClickTag={(v) => setTags(tags.concat(v))} />

            <div>
                <label htmlFor="link" className="block mb-2">
                    <i className="ti ti-link" /> オカズリンク
                </label>
                <input
                    type="text"
                    id="link"
                    name="link"
                    autoComplete="off"
                    className={cn(
                        'block w-full rounded border px-3 py-2 transition duration-150 ease-in-out focus:outline-none focus:ring-4',
                        errors?.link
                            ? 'border-danger focus:ring-danger/25'
                            : 'border-neutral-300 focus:border-primary-400 focus:ring-primary-400/25',
                    )}
                    placeholder="http://..."
                    value={link}
                    onChange={(e) => setLink(e.target.value)}
                    onBlur={() => setLinkForPreview(link)}
                />
                <p className="mt-1 text-xs text-secondary">オカズのURLを貼り付けて登録することができます。</p>
                <FieldError name="link" label="リンク" errors={errors?.link} />
            </div>

            <MetadataPreview link={linkForPreview} tags={tags} onClickTag={(v) => setTags(tags.concat(v))} />

            <div>
                <label htmlFor="note" className="block mb-2">
                    <i className="ti ti-message-circle" /> ノート
                </label>
                <textarea
                    id="note"
                    name="note"
                    className={cn(
                        'block w-full rounded border px-3 py-2 transition duration-150 ease-in-out focus:outline-none focus:ring-4',
                        errors?.note
                            ? 'border-danger focus:ring-danger/25'
                            : 'border-neutral-300 focus:border-primary-400 focus:ring-primary-400/25',
                    )}
                    rows={4}
                    value={note}
                    onChange={(e) => setNote(e.target.value)}
                />
                <div className={cn('mt-1 text-xs text-secondary', remainingChars < 0 && 'text-danger')}>
                    残り {remainingChars} 文字
                </div>
                <FieldError name="note" label="ノート" errors={errors?.note} />
            </div>

            <div>
                <label>
                    <input
                        id="isPrivate"
                        type="checkbox"
                        className="accent-primary"
                        checked={isPrivate}
                        onChange={(e) => setPrivate(e.target.checked)}
                    />
                    <span className="ml-2">
                        <i className="ti ti-lock" /> このチェックインを非公開にする
                    </span>
                </label>
            </div>

            <div>
                <label>
                    <input
                        id="isTooSensitive"
                        type="checkbox"
                        className="accent-primary"
                        checked={isTooSensitive}
                        onChange={(e) => setTooSensitive(e.target.checked)}
                    />
                    <span className="ml-2">
                        <i className="ti ti-alert-triangle" /> チェックイン対象のオカズをより過激なオカズとして設定する
                    </span>
                </label>
            </div>

            <div>
                <label>
                    <input
                        id="discardElapsedTime"
                        type="checkbox"
                        className="accent-primary"
                        checked={discardElapsedTime}
                        onChange={(e) => setDiscardElapsedTime(e.target.checked)}
                    />
                    <span className="ml-2">
                        <i className="ti ti-clock-x" /> 前回チェックインからの経過時間を記録しない
                    </span>
                </label>
                <br />
                <div className="ml-4 mt-1 text-sm text-secondary">
                    長期間お使いにならなかった場合など、経過時間に意味が無い時のリセット用オプションです。
                    <ul className="pl-4 list-disc">
                        <li>最長・最短記録の計算から除外されます。</li>
                        <li>平均記録の起点がこのチェックインになります。</li>
                    </ul>
                </div>
            </div>

            <div className="text-center py-2">
                <ProgressButton
                    label="チェックイン"
                    type="submit"
                    variant="primary"
                    inProgress={submitting}
                    disabled={submitting}
                />
            </div>
        </form>
    );
};
