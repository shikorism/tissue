import * as React from 'react';
import { useState } from 'react';
import * as classNames from 'classnames';
import { TagInput } from './TagInput';
import { MetadataPreview } from './MetadataPreview';

type CheckboxProps = {
    id: string;
    name: string;
    className?: string;
    checked?: boolean;
    onChange?: (newValue: boolean) => void;
};

const Checkbox: React.FC<CheckboxProps> = ({ id, name, className, checked, onChange, children }) => (
    <div className={`custom-control custom-checkbox ${className}`}>
        <input
            id={id}
            name={name}
            type="checkbox"
            className="custom-control-input"
            checked={checked}
            onChange={(e) => onChange && onChange(e.target.checked)}
        />
        <label className="custom-control-label" htmlFor={id}>
            {children}
        </label>
    </div>
);

type FieldErrorProps = {
    errors?: string[];
};

const FieldError: React.FC<FieldErrorProps> = ({ errors }) =>
    (errors && errors.length > 0 && <div className="invalid-feedback">{errors[0]}</div>) || null;

const StandaloneFieldError: React.FC<FieldErrorProps> = ({ errors }) =>
    (errors && errors.length > 0 && (
        <div className="form-group col-sm-12" style={{ marginTop: '-1rem' }}>
            <small className="text-danger">{errors[0]}</small>
        </div>
    )) ||
    null;

export type CheckinFormProps = {
    initialState: any;
};

export const CheckinForm: React.FC<CheckinFormProps> = ({ initialState }) => {
    const [date, setDate] = useState<string>(initialState.fields.date || '');
    const [time, setTime] = useState<string>(initialState.fields.time || '');
    const [tags, setTags] = useState<string[]>(initialState.fields.tags || []);
    const [link, setLink] = useState<string>(initialState.fields.link || '');
    const [linkForPreview, setLinkForPreview] = useState(link);
    const [note, setNote] = useState<string>(initialState.fields.note || '');
    const [isPrivate, setPrivate] = useState<boolean>(!!initialState.fields.is_private);
    const [isTooSensitive, setTooSensitive] = useState<boolean>(!!initialState.fields.is_too_sensitive);

    return (
        <>
            <div className="form-row">
                <div className="form-group col-sm-6">
                    <label htmlFor="date">
                        <span className="oi oi-calendar" /> 日付
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
                    />
                    <FieldError errors={initialState.errors?.date} />
                </div>
                <div className="form-group col-sm-6">
                    <label htmlFor="time">
                        <span className="oi oi-clock" /> 時刻
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
                    />
                    <FieldError errors={initialState.errors?.time} />
                </div>
                <StandaloneFieldError errors={initialState.errors?.datetime} />
            </div>
            <div className="form-row">
                <div className="form-group col-sm-12">
                    <label htmlFor="tagInput">
                        <span className="oi oi-tags" /> タグ
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
            </div>
            <div className="form-row">
                <div className="form-group col-sm-12">
                    <label htmlFor="link">
                        <span className="oi oi-link-intact" /> オカズリンク
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
                        <span className="oi oi-comment-square" /> ノート
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
                    <Checkbox
                        id="isPrivate"
                        name="is_private"
                        className="mb-3"
                        checked={isPrivate}
                        onChange={(v) => setPrivate(v)}
                    >
                        <span className="oi oi-lock-locked" /> このチェックインを非公開にする
                    </Checkbox>
                    <Checkbox
                        id="isTooSensitive"
                        name="is_too_sensitive"
                        className="mb-3"
                        checked={isTooSensitive}
                        onChange={(v) => setTooSensitive(v)}
                    >
                        <span className="oi oi-warning" /> チェックイン対象のオカズをより過激なオカズとして設定する
                    </Checkbox>
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
