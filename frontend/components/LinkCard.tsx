import React, { useState } from 'react';
import { cn } from '../lib/cn';
import { useGetMetadata } from '../api/hooks';

interface Props {
    className?: string;
    link: string;
    isTooSensitive?: boolean;
}

export const LinkCard: React.FC<Props> = ({ className, link, isTooSensitive = false }) => {
    const [isSpoiled, setIsSpoiled] = useState(isTooSensitive);
    const { data } = useGetMetadata(link);
    if (!data) {
        return null;
    }

    return (
        <div className={cn('rounded border border-gray-border overflow-hidden', className)}>
            <a href={link} target="_blank" rel="noopener noreferrer">
                <div className="flex flex-col md:flex-row">
                    {data.image && (
                        <div className="flex justify-center items-center flex-1 max-h-[400px] overflow-hidden relative">
                            {isSpoiled && (
                                <div
                                    className="absolute w-full h-full z-10 flex justify-center items-center cursor-pointer"
                                    onClick={(e) => {
                                        e.preventDefault();
                                        setIsSpoiled(false);
                                    }}
                                >
                                    <span className="text-sm rounded p-3 bg-[#f0f0f0cc] select-none">
                                        クリックまたはタップで表示
                                    </span>
                                </div>
                            )}
                            <img
                                src={data.image}
                                alt="Thumbnail"
                                className={cn('w-full', isSpoiled && 'blur-lg grayscale')}
                            />
                        </div>
                    )}
                    <div className="p-5 flex-1 max-h-[400px] overflow-hidden">
                        {data.title && <h6 className="font-bold mb-3">{data.title}</h6>}
                        {data.description && <p className="text-xs whitespace-pre-line">{data.description}</p>}
                    </div>
                </div>
            </a>
        </div>
    );
};
