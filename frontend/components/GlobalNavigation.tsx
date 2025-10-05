import React from 'react';
import { Link } from 'react-router';
import { DropdownMenu } from 'radix-ui';
import { useProgress } from '@bprogress/react';
import { BrandLogo } from './BrandLogo';
import { logout, useCurrentUser } from './AuthProvider';

export const GlobalNavigation: React.FC = () => {
    const { user: me } = useCurrentUser();
    const { start, stop } = useProgress();

    const handleLogout = async () => {
        start();
        try {
            await logout();
        } finally {
            stop();
        }
    };

    return (
        <nav className="hidden md:flex flex-col fixed left-0 top-0 bottom-0 w-(--global-nav-width) bg-gray-back">
            <Link to="/" className="flex items-center gap-3 m-4 mb-2">
                <BrandLogo />
                <span className="text-xl opacity-90">Tissue</span>
            </Link>
            <div className="px-4 flex-1 shrink-1 overflow-y-auto">
                <ul>
                    <NavItem to="/">
                        <i className="ti ti-home text-2xl text-neutral-600"></i>ホーム
                    </NavItem>
                    <NavItem to="/search">
                        <i className="ti ti-search text-2xl text-neutral-600"></i>検索
                    </NavItem>
                    <NavItem to="/timeline/public">
                        <i className="ti ti-layout-grid text-2xl text-neutral-600"></i>お惣菜
                    </NavItem>
                    <NavItem to="/tag">
                        <i className="ti ti-tags text-2xl text-neutral-600"></i>タグ一覧
                    </NavItem>
                </ul>
                {me && (
                    <>
                        <h2 className="mt-4 mb-2 px-2 text-sm font-bold">記録する</h2>
                        <ul>
                            <NavItem to="/checkin">
                                <i className="ti ti-send text-2xl text-neutral-600"></i>チェックイン
                            </NavItem>
                            <NavItem to="/collect">
                                <i className="ti ti-folder-plus text-2xl text-neutral-600"></i>コレクションに追加
                            </NavItem>
                        </ul>
                        <h2 className="mt-4 mb-2 px-2 text-sm font-bold">マイデータ</h2>
                        <ul>
                            <NavItem to={`/user/${me.name}/checkins`}>
                                <i className="ti ti-list text-2xl text-neutral-600"></i>チェックイン
                            </NavItem>
                            <NavItem to={`/user/${me.name}/stats`}>
                                <i className="ti ti-timeline text-2xl text-neutral-600"></i>グラフ
                            </NavItem>
                            <NavItem to={`/user/${me.name}/likes`}>
                                <i className="ti ti-heart text-2xl text-neutral-600"></i>いいね
                            </NavItem>
                            <NavItem to={`/user/${me.name}/collections`}>
                                <i className="ti ti-folder text-2xl text-neutral-600"></i>コレクション
                            </NavItem>
                        </ul>
                    </>
                )}
                <div className="mt-8 px-2 text-xs text-secondary">
                    <ul className="mb-1 flex gap-2">
                        {import.meta.env.VITE_APP_SUPPORT_LINK && (
                            <li>
                                <a className="hover:underline" href={import.meta.env.VITE_APP_SUPPORT_LINK}>
                                    お問い合わせ
                                </a>
                            </li>
                        )}
                        <li>
                            <a className="hover:underline" href="https://github.com/shikorism/tissue">
                                GitHub
                            </a>
                        </li>
                        <li>
                            <a className="hover:underline" href="/apidoc.html">
                                API
                            </a>
                        </li>
                    </ul>
                    <p>&copy; 2017-2025 shikorism.net</p>
                </div>
            </div>
            {me && (
                <DropdownMenu.Root>
                    <DropdownMenu.Trigger asChild>
                        <button className="m-4 mt-2 p-2 flex items-center gap-1 rounded-sm hover:bg-neutral-200 data-[state=open]:bg-neutral-200 cursor-pointer">
                            <img
                                className="rounded inline-block align-bottom mr-1"
                                src={me.profile_image_url}
                                alt={`${me.display_name}'s Avatar`}
                                width={40}
                                height={40}
                            />
                            <span className="text-sm overflow-ellipsis overflow-hidden text-nowrap">
                                {me.display_name}
                            </span>
                        </button>
                    </DropdownMenu.Trigger>

                    <DropdownMenu.Portal>
                        <DropdownMenu.Content
                            className="p-2 w-(--radix-dropdown-menu-trigger-width) bg-white border-1 border-gray-border rounded shadow-md"
                            side="top"
                            align="start"
                        >
                            <DropdownMenu.Item
                                asChild
                                className="block p-2 rounded data-highlighted:bg-neutral-200 select-none outline-0"
                            >
                                <a href="/setting/profile">設定</a>
                            </DropdownMenu.Item>
                            <DropdownMenu.Item
                                className="block p-2 rounded data-highlighted:bg-neutral-200 select-none outline-0 cursor-pointer"
                                onSelect={handleLogout}
                            >
                                ログアウト
                            </DropdownMenu.Item>
                        </DropdownMenu.Content>
                    </DropdownMenu.Portal>
                </DropdownMenu.Root>
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
