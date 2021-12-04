import React, { useState } from 'react';
import ReactDOM from 'react-dom';
import classNames from 'classnames';
import { FieldError } from './components/FieldError';
import { TagInput } from './components/TagInput';
import { MetadataPreview } from './components/MetadataPreview';
import { fetchPostJson, ResponseError } from './fetch';
import { showToast } from './tissue';

type FormValues = {
    collection: string;
    link: string;
    tags: string[];
    note: string;
};

type FormErrors = {
    [Property in keyof FormValues]+?: string[];
};

const CollectForm = () => {
    const [values, setValues] = useState<FormValues>({
        collection: '',
        link: '',
        note: '',
        tags: [],
    });
    const [errors, setErrors] = useState<FormErrors>({});
    const [linkForPreview, setLinkForPreview] = useState(values.link);
    const [submitting, setSubmitting] = useState<boolean>(false);

    const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        setSubmitting(true);
        try {
            const response = await fetchPostJson('/api/collections/inbox', { ...values, flash: true });
            if (response.status === 201) {
                const data = await response.json();
                if (data.collection_id && data.user_name) {
                    location.href = `/user/${data.user_name}/collections/${data.collection_id}`;
                    return;
                }
            }
            throw new ResponseError(response);
        } catch (e) {
            console.error(e);
            if (e instanceof ResponseError && e.response.status == 422) {
                const data = await e.response.json();
                if (data.error?.violations) {
                    const errors: FormErrors = {};
                    for (const violation of data.error.violations) {
                        const field = violation.field as keyof FormValues;
                        (errors[field] || (errors[field] = [])).push(violation.message);
                    }
                    setErrors(errors);
                    return;
                }
            }
            showToast('エラーが発生しました', { color: 'danger', delay: 5000 });
            setSubmitting(false);
        }
    };

    return (
        <form onSubmit={handleSubmit}>
            <div className="form-row">
                <div className="form-group col-sm-12">
                    <label htmlFor="collection">
                        <span className="oi oi-folder" /> 追加先
                    </label>
                    <select name="collection" id="collection" className="custom-select" disabled>
                        <option value="">あとで抜く</option>
                    </select>
                    <FieldError errors={errors?.collection} />
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
                        className={classNames({ 'form-control': true, 'is-invalid': errors?.link })}
                        placeholder="http://..."
                        required
                        value={values.link}
                        onChange={(e) => setValues((values) => ({ ...values, link: e.target.value }))}
                        onBlur={() => setLinkForPreview(values.link)}
                    />
                    <small className="form-text text-muted">オカズのURLを貼り付けてください。</small>
                    <FieldError name="link" label="リンク" errors={errors?.link} />
                </div>
            </div>
            <MetadataPreview
                link={linkForPreview}
                tags={values.tags}
                onClickTag={(v) => setValues(({ tags, ...rest }) => ({ ...rest, tags: tags.concat(v) }))}
            />
            <div className="form-row">
                <div className="form-group col-sm-12">
                    <label htmlFor="tagInput">
                        <span className="oi oi-tags" /> タグ
                    </label>
                    <TagInput
                        id="tagInput"
                        name="tags"
                        values={values.tags}
                        isInvalid={!!errors?.tags}
                        onChange={(v) => setValues((values) => ({ ...values, tags: v }))}
                    />
                    <small className="form-text text-muted">Tab, Enter, 半角スペースのいずれかで入力確定します。</small>
                    <FieldError name="tags" label="タグ" errors={errors?.tags} />
                </div>
            </div>
            <div className="form-row">
                <div className="form-group col-sm-12">
                    <label htmlFor="note">
                        <span className="oi oi-comment-square" /> ノート
                    </label>
                    <textarea
                        id="note"
                        name="note"
                        className={classNames({ 'form-control': true, 'is-invalid': errors?.note })}
                        rows={4}
                        value={values.note}
                        onChange={(e) => setValues((values) => ({ ...values, note: e.target.value }))}
                    />
                    <small className="form-text text-muted">最大 500 文字</small>
                    <FieldError name="note" label="ノート" errors={errors?.note} />
                </div>
            </div>
            <div className="text-center">
                <button className="btn btn-primary" type="submit" disabled={submitting}>
                    登録
                </button>
            </div>
        </form>
    );
};

ReactDOM.render(<CollectForm />, document.getElementById('form'));
