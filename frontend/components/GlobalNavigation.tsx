import React from 'react';
import { Link } from 'react-router';
import { useGetMe } from '../api/hooks';
import { BrandLogo } from './BrandLogo';

export const GlobalNavigation: React.FC = () => {
    const { data: me } = useGetMe();

    return (
        <nav className="hidden md:block fixed left-0 top-0 bottom-0 w-(--global-nav-width) p-4 bg-gray-back">
            <Link to="/" className="flex items-center gap-3">
                <BrandLogo />
                <span className="text-xl opacity-90">Tissue</span>
            </Link>
            {me && (
                <Link
                    to="/checkin"
                    className="block rounded my-4 py-1.5 text-center border-1 border-neutral-600 hover:bg-neutral-600 hover:text-white transition-colors"
                >
                    チェックイン
                </Link>
            )}
            <ul>
                <NavItem to="/">
                    <i className="ti ti-home text-2xl text-neutral-600"></i>ホーム
                </NavItem>
                <NavItem to="/timeline/public">
                    <i className="ti ti-search text-2xl text-neutral-600"></i>お惣菜
                </NavItem>
                <NavItem to="/tag">
                    <i className="ti ti-tags text-2xl text-neutral-600"></i>タグ一覧
                </NavItem>
            </ul>
            {me && (
                <>
                    <h2 className="mt-4 mb-2 px-2 text-sm font-bold">マイデータ</h2>
                    <ul>
                        <NavItem to={`/user/${me.name}`}>
                            <i className="ti ti-list text-2xl text-neutral-600"></i>タイムライン
                        </NavItem>
                        <NavItem to={`/user/${me.name}/stats`}>
                            <i className="ti ti-timeline text-2xl text-neutral-600"></i>グラフ
                        </NavItem>
                        <NavItem to={`/user/${me.name}/okazu`}>
                            <i className="ti ti-books text-2xl text-neutral-600"></i>オカズ
                        </NavItem>
                        <NavItem to={`/user/${me.name}/likes`}>
                            <i className="ti ti-heart text-2xl text-neutral-600"></i>いいね履歴
                        </NavItem>
                        <NavItem to={`/user/${me.name}/collections`}>
                            <i className="ti ti-folder text-2xl text-neutral-600"></i>コレクション
                        </NavItem>
                    </ul>
                </>
            )}
        </nav>
    );
};

interface NavItemProps {
    to: string;
    children: React.ReactNode;
}

const NavItem: React.FC<NavItemProps> = ({ to, children }) => (
    <li>
        <Link to={to} className="p-2 rounded-sm hover:bg-neutral-200 flex items-center gap-2">
            {children}
        </Link>
    </li>
);
