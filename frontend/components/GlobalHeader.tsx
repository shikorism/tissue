import React from 'react';
import { Link } from 'react-router';
import { BrandLogo } from './BrandLogo';
import { useGetMe } from '../api/hooks';

export const GlobalHeader: React.FC = () => {
    const { data: me, error } = useGetMe();

    return (
        <nav className="px-4 py-2 bg-gray-back flex justify-between items-center">
            <Link to="/" className="flex items-center gap-3">
                <BrandLogo />
                <span className="text-xl opacity-90">Tissue</span>
            </Link>
            {me ? (
                <div className="flex gap-2">
                    <Link to="/checkin" className="p-2 opacity-70 hover:opacity-90 focus:opacity-90">
                        チェックイン
                    </Link>
                </div>
            ) : error?.response?.status === 401 ? (
                <div className="flex gap-2">
                    <Link to="/register" className="p-2 opacity-70 hover:opacity-90 focus:opacity-90">
                        会員登録
                    </Link>
                    <Link to="/login" className="p-2 opacity-70 hover:opacity-90 focus:opacity-90">
                        ログイン
                    </Link>
                </div>
            ) : null}
        </nav>
    );
};
