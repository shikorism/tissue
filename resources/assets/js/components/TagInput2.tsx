import * as React from 'react';
import { useState, useRef } from 'react';
import * as classNames from 'classnames';

type TagInputProps = {
    id: string;
    name: string;
    value: string;
    isInvalid: boolean;
};

const TagInput: React.FC<TagInputProps> = ({ id, name, value, isInvalid }) => {
    const [tags, setTags] = useState(value.trim() !== '' ? value.trim().split(' ') : []);
    const [buffer, setBuffer] = useState('');
    const containerClass = classNames('form-control', 'h-auto', { 'is-invalid': isInvalid });
    const inputRef = useRef<HTMLInputElement>(null);
    const removeTag = (index: number) => {
        setTags(tags.filter((v, i) => i != index));
    };
    const onKeyDown = (event: React.KeyboardEvent<HTMLInputElement>) => {
        if (buffer.trim() !== '') {
            switch (event.key) {
                case 'Tab':
                case 'Enter':
                case ' ':
                    if ((event as any).isComposing !== true) {
                        setTags(tags.concat(buffer.trim()));
                        setBuffer('');
                    }
                    event.preventDefault();
                    break;
                case 'Unidentified':
                    // 実際にテキストボックスに入力されている文字を見に行く (フォールバック処理)
                    const nativeEvent = event.nativeEvent;
                    if (nativeEvent.srcElement && (nativeEvent.srcElement as HTMLInputElement).value.slice(-1) == ' ') {
                        setTags(tags.concat(buffer.trim()));
                        setBuffer('');
                        event.preventDefault();
                    }
                    break;
            }
        } else if (event.key === 'Enter') {
            // 誤爆防止
            event.preventDefault();
        }
    };

    return (
        <div className={containerClass} onClick={() => inputRef.current?.focus()}>
            <input name={name} type="hidden" value={tags.join(' ')} />
            <ul className="list-inline d-inline">
                {tags.map((tag, i) => (
                    <li
                        key={i}
                        className={classNames('list-inline-item', 'badge', 'badge-primary', 'tis-tag-input-item')}
                        onClick={() => removeTag(i)}
                    >
                        <span className="oi oi-tag" /> {tag} | x
                    </li>
                ))}
            </ul>
            <input
                id={id}
                ref={inputRef}
                type="text"
                className="tis-tag-input-field"
                value={buffer}
                onChange={(e) => setBuffer(e.target.value)}
                onKeyDown={onKeyDown}
            />
        </div>
    );
};

export default TagInput;
