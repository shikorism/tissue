import React, { useState, useEffect } from 'react';
import classNames from 'classnames';
import { format } from 'date-fns';
import { CheckBox } from './CheckBox';
import { FieldError, StandaloneFieldError } from './FieldError';
import { TagInput } from './TagInput';
import { MetadataPreview } from './MetadataPreview';
import { FavoriteTags } from './FavoriteTags';

type CheckinFormProps = {
    initialState: any;
};

export const CheckinForm: React.FC<CheckinFormProps> = ({ initialState }) => {
    const mode = initialState.mode;
    const [date, setDate] = useState<string>(initialState.fields.date || '');
    const [time, setTime] = useState<string>(initialState.fields.time || '');
    const [tags, setTags] = useState<string[]>(initialState.fields.tags || []);
    const [link, setLink] = useState<string>(initialState.fields.link || '');
    const [linkForPreview, setLinkForPreview] = useState(link);
    const [note, setNote] = useState<string>(initialState.fields.note || '');
    const [isRealtime, setRealtime] = useState<boolean>(mode === 'create' && initialState.fields.is_realtime);
    const [isPrivate, setPrivate] = useState<boolean>(!!initialState.fields.is_private);
    const [isTooSensitive, setTooSensitive] = useState<boolean>(!!initialState.fields.is_too_sensitive);
    const [discardElapsedTime, setDiscardElapsedTime] = useState<boolean>(!!initialState.fields.discard_elapsed_time);
    useEffect(() => {
        if (mode === 'create' && isRealtime) {
            const id = setInterval(() => {
                const now = new Date();
                setDate(format(now, 'yyyy/MM/dd'));
                setTime(format(now, 'HH:mm'));
            }, 500);
            return () => clearInterval(id);
        }
    }, [mode, isRealtime]);

    return (
        <>
            <div className="form-row">
                {mode === 'create' && (
                    <div className="col-sm-12 mb-2">
                        <CheckBox
                            id="isRealtime"
                            name="is_realtime"
                            checked={isRealtime}
                            onChange={(v) => setRealtime(v)}
                        >
                            現在時刻でチェックイン
                        </CheckBox>
                    </div>
                )}
                <div className="form-group col-sm-6">
                    <label htmlFor="date">
                        <i className="ti ti-calendar-event" /> 日付
                    </label>
                    <input
                        type="text"
                        id="date"
                        name="date"
                        className={classNames({
                            'form-control': true,
                            'is-invalid': initialState.errors?.date || initialState.errors?.datetime,
                        })}
                        pattern="^20[0-9]{2}/(0[1-9]|1[0-2])/(0[1-9]|[12][0-9]|3[01])$"
                        required
                        value={date}
                        onChange={(e) => setDate(e.target.value)}
                        disabled={isRealtime}
                    />
                    <FieldError errors={initialState.errors?.date} />
                </div>
                <div className="form-group col-sm-6">
                    <label htmlFor="time">
                        <i className="ti ti-clock" /> 時刻
                    </label>
                    <input
                        type="text"
                        id="time"
                        name="time"
                        className={classNames({
                            'form-control': true,
                            'is-invalid': initialState.errors?.time || initialState.errors?.datetime,
                        })}
                        pattern="^([01][0-9]|2[0-3]):[0-5][0-9]$"
                        required
                        value={time}
                        onChange={(e) => setTime(e.target.value)}
                        disabled={isRealtime}
                    />
                    <FieldError errors={initialState.errors?.time} />
                </div>
                <StandaloneFieldError errors={initialState.errors?.datetime} />
            </div>
            <div className="form-row">
                <div className="form-group col-sm-12">
                    <label htmlFor="tagInput">
                        <i className="ti ti-tags" /> タグ
                    </label>
                    <TagInput
                        id="tagInput"
                        name="tags"
                        values={tags}
                        isInvalid={!!initialState.errors?.tags}
                        onChange={(v) => setTags(v)}
                    />
                    <small className="form-text text-muted">Tab, Enter, 半角スペースのいずれかで入力確定します。</small>
                    <FieldError errors={initialState.errors?.tags} />
                </div>
                <div className="form-group col-sm-12">
                    <FavoriteTags tags={tags} onClickTag={(v) => setTags(tags.concat(v))} />
                </div>
            </div>
            <div className="form-row">
                <div className="form-group col-sm-12">
                    <label htmlFor="link">
                        <i className="ti ti-link" /> オカズリンク
                    </label>
                    <input
                        type="text"
                        id="link"
                        name="link"
                        autoComplete="off"
                        className={classNames({ 'form-control': true, 'is-invalid': initialState.errors?.link })}
                        placeholder="http://..."
                        value={link}
                        onChange={(e) => setLink(e.target.value)}
                        onBlur={() => setLinkForPreview(link)}
                    />
                    <small className="form-text text-muted">オカズのURLを貼り付けて登録することができます。</small>
                    <FieldError errors={initialState.errors?.link} />
                </div>
            </div>
            <MetadataPreview link={linkForPreview} tags={tags} onClickTag={(v) => setTags(tags.concat(v))} />
            <div className="form-row">
                <div className="form-group col-sm-12">
                    <label htmlFor="note">
                        <i className="ti ti-message-circle" /> ノート
                    </label>
                    <textarea
                        id="note"
                        name="note"
                        className={classNames({ 'form-control': true, 'is-invalid': initialState.errors?.note })}
                        rows={4}
                        value={note}
                        onChange={(e) => setNote(e.target.value)}
                    />
                    <small className="form-text text-muted">最大 500 文字</small>
                    <FieldError errors={initialState.errors?.note} />
                </div>
            </div>
            <div className="form-row mt-4">
                <p>オプション</p>
                <div className="form-group col-sm-12">
                    <CheckBox
                        id="isPrivate"
                        name="is_private"
                        className="mb-3"
                        checked={isPrivate}
                        onChange={(v) => setPrivate(v)}
                    >
                        <i className="ti ti-lock" /> このチェックインを非公開にする
                    </CheckBox>
                    <CheckBox
                        id="isTooSensitive"
                        name="is_too_sensitive"
                        className="mb-3"
                        checked={isTooSensitive}
                        onChange={(v) => setTooSensitive(v)}
                    >
                        <i className="ti ti-alert-triangle" /> チェックイン対象のオカズをより過激なオカズとして設定する
                    </CheckBox>
                    <CheckBox
                        id="discardElapsedTime"
                        name="discard_elapsed_time"
                        className="mb-3"
                        checked={discardElapsedTime}
                        onChange={(v) => setDiscardElapsedTime(v)}
                    >
                        <i className="ti ti-clock-x" /> 前回チェックインからの経過時間を記録しない
                        <br />
                        <small className="form-text text-muted">
                            長期間お使いにならなかった場合など、経過時間に意味が無い時のリセット用オプションです。
                            <ul className="pl-3">
                                <li>最長・最短記録の計算から除外されます。</li>
                                <li>平均記録の起点がこのチェックインになります。</li>
                            </ul>
                        </small>
                    </CheckBox>
                </div>
            </div>
            <div className="text-center">
                <button className="btn btn-primary" type="submit">
                    チェックイン
                </button>
            </div>
        </>
    );
};
