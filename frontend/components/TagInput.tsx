import React, { useRef, useState } from 'react';
import { cn } from '../lib/cn';

type TagInputProps = {
    id: string;
    name: string;
    values: string[];
    isInvalid: boolean;
    onChange?: (newValues: string[]) => void;
};

export const TagInput: React.FC<TagInputProps> = ({ id, name, values, isInvalid, onChange }) => {
    const [buffer, setBuffer] = useState('');
    const inputRef = useRef<HTMLInputElement>(null);
    const removeTag = (index: number) => {
        onChange?.(values.filter((v, i) => i != index));
    };
    const onKeyDown = (event: React.KeyboardEvent<HTMLInputElement>) => {
        if (buffer.trim() !== '') {
            switch (event.key) {
                case 'Tab':
                case 'Enter':
                case ' ':
                    if (!event.nativeEvent.isComposing) {
                        commitBuffer();
                    }
                    event.preventDefault();
                    break;
                case 'Unidentified': {
                    // 実際にテキストボックスに入力されている文字を見に行く (フォールバック処理)
                    const nativeEvent = event.nativeEvent;
                    if (nativeEvent.srcElement && (nativeEvent.srcElement as HTMLInputElement).value.slice(-1) == ' ') {
                        commitBuffer();
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

    const commitBuffer = () => {
        const newTag = buffer.trim().replace(/\s+/g, '_');
        if (newTag.length === 0) return;
        onChange?.(values.concat(newTag));
        setBuffer('');
    };

    return (
        <div
            className={cn(
                'w-full rounded border px-3 py-2 transition duration-150 ease-in-out',
                isInvalid
                    ? 'border-danger focus:ring-danger/25'
                    : 'border-neutral-300 focus:border-primary-400 focus:ring-primary-400/25',
            )}
            onClick={() => inputRef.current?.focus()}
        >
            <input name={name} type="hidden" value={values.join(' ')} />
            <ul className="flex flex-wrap gap-[0.6ch] items-center">
                {values.map((tag, i) => (
                    <li
                        key={i}
                        className="group text-2xs font-bold inline-block max-w-full rounded text-white bg-neutral-500 hover:bg-neutral-600 break-all whitespace-normal cursor-pointer"
                        onClick={() => removeTag(i)}
                    >
                        <i className="ti ti-tag-filled inline-block ml-1.5 mr-0.5 my-1" /> {tag}
                        <i className="ti ti-x inline-block ml-1 px-1 py-1 rounded-r group-hover:bg-neutral-800" />
                    </li>
                ))}
                <li className="inline-block -my-0.5">
                    <input
                        id={id}
                        ref={inputRef}
                        type="text"
                        className="border-0 outline-0"
                        value={buffer}
                        onChange={(e) => setBuffer(e.target.value)}
                        onBlur={commitBuffer}
                        onKeyDown={onKeyDown}
                    />
                </li>
            </ul>
        </div>
    );
};
