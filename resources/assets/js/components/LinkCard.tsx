// 修正の際は resources/views/components/link-card.blade.php も修正してください!
import React, { useCallback } from 'react';
import classNames from 'classnames';
import { linkCard } from '../tissue';

type LinkCardProps = {
    link: string;
    isTooSensitive?: boolean;
};

export const LinkCard: React.FC<LinkCardProps> = ({ link, isTooSensitive = false }) => {
    const ref = useCallback((el: HTMLDivElement | null) => {
        if (el) {
            linkCard(el);
        }
    }, []);

    return (
        <div ref={ref} className="card link-card mb-2 px-0 col-12 d-none" style={{ fontSize: 'small' }}>
            <a
                className={classNames('text-dark', 'card-link', { 'card-spoiler': isTooSensitive })}
                href={link}
                target="_blank"
                rel="noopener noreferrer"
            >
                <div className="row no-gutters">
                    <div className="col-12 col-md-6 justify-content-center align-items-center">
                        {isTooSensitive && (
                            <div className="card-spoiler-img-overlay">
                                <span className="warning-text">クリックまたはタップで表示</span>
                            </div>
                        )}
                        <img src="" alt="Thumbnail" className="w-100 bg-secondary" />
                    </div>
                    <div className="col-12 col-md-6">
                        <div className="card-body">
                            <h6 className="card-title font-weight-bold">タイトル</h6>
                            <p className="card-text">コンテンツの説明文</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    );
};
