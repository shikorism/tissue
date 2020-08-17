import React, { useState, useRef } from 'react';
import classNames from 'classnames';

type TagInputProps = {
    id: string;
    name: string;
    values: string[];
    isInvalid: boolean;
    onChange?: (newValues: string[]) => void;
};

export const TagInput: React.FC<TagInputProps> = ({ id, name, values, isInvalid, onChange }) => {
    const [buffer, setBuffer] = useState('');
    const containerClass = classNames('form-control', 'h-auto', { 'is-invalid': isInvalid });
    const inputRef = useRef<HTMLInputElement>(null);
    const removeTag = (index: number) => {
        onChange && onChange(values.filter((v, i) => i != index));
    };
    const onKeyDown = (event: React.KeyboardEvent<HTMLInputElement>) => {
        if (buffer.trim() !== '') {
            switch (event.key) {
                case 'Tab':
                case 'Enter':
                case ' ':
                    if ((event as any).isComposing !== true) {
                        onChange && onChange(values.concat(buffer.trim().replace(/\s+/g, '_')));
                        setBuffer('');
                    }
                    event.preventDefault();
                    break;
                case 'Unidentified': {
                    // 実際にテキストボックスに入力されている文字を見に行く (フォールバック処理)
                    const nativeEvent = event.nativeEvent;
                    if (nativeEvent.srcElement && (nativeEvent.srcElement as HTMLInputElement).value.slice(-1) == ' ') {
                        onChange && onChange(values.concat(buffer.trim().replace(/\s+/g, '_')));
                        setBuffer('');
                        event.preventDefault();
                    }
                    break;
                }
            }
        } else if (event.key === 'Enter') {
            // 誤爆防止
            event.preventDefault();
        }
    };

    return (
        <div className={containerClass} onClick={() => inputRef.current?.focus()}>
            <input name={name} type="hidden" value={values.join(' ')} />
            <ul className="list-inline d-inline">
                {values.map((tag, i) => (
                    <li
                        key={i}
                        className={classNames('list-inline-item', 'badge', 'badge-primary', 'tis-tag-input-item')}
                        onClick={() => removeTag(i)}
                    >
                        <span className="oi oi-tag" /> {tag} | x
                    </li>
                ))}
                <li className="list-inline-item">
                    <input
                        id={id}
                        ref={inputRef}
                        type="text"
                        className="tis-tag-input-field"
                        value={buffer}
                        onChange={(e) => setBuffer(e.target.value)}
                        onKeyDown={onKeyDown}
                    />
                </li>
            </ul>
        </div>
    );
};
