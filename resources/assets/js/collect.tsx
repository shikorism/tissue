import React, { useState } from 'react';
import ReactDOM from 'react-dom';
import classNames from 'classnames';
import { FieldError } from './components/FieldError';
import { TagInput } from './components/TagInput';
import { MetadataPreview } from './components/MetadataPreview';

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

    const handleSubmit = (event: React.FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        // TODO
    };

    return (
        <form onSubmit={handleSubmit}>
            <div className="form-row">
                <div className="form-group col-sm-12">
                    <label htmlFor="collection">
                        <span className="oi oi-folder" /> 追加先
                    </label>
                    <select name="collection" id="collection" className="custom-select">
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
                    <FieldError errors={errors?.link} />
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
                    <FieldError errors={errors?.tags} />
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
                    <FieldError errors={errors?.note} />
                </div>
            </div>
            <div className="text-center">
                <button className="btn btn-primary" type="submit">
                    登録
                </button>
            </div>
        </form>
    );
};

ReactDOM.render(<CollectForm />, document.getElementById('form'));
