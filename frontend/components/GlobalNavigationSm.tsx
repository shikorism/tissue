import React from 'react';
import { Link } from 'react-router';
import { useCurrentUser } from './AuthProvider';
import { Button } from './Button';

export const GlobalNavigationSm: React.FC = () => {
    const { user: me } = useCurrentUser();

    return (
        <nav className="flex md:hidden fixed left-0 right-0 bottom-0 h-(--global-nav-height) z-10 bg-gray-back items-center text-center">
            {me ? (
                <>
                    <Link to="/" className="flex-1 py-4 active:bg-neutral-300">
                        <i className="ti ti-home text-2xl text-neutral-600" />
                    </Link>
                    <Link to="/search" className="flex-1 py-4 active:bg-neutral-300">
                        <i className="ti ti-search text-2xl text-neutral-600" />
                    </Link>
                    <Link to={`/user/${me.name}`} className="flex-1 py-4 active:bg-neutral-300">
                        <i className="ti ti-user text-2xl text-neutral-600"></i>
                    </Link>
                    <Link to={`/user/${me.name}/collections`} className="flex-1 py-4 active:bg-neutral-300">
                        <i className="ti ti-folder text-2xl text-neutral-600"></i>
                    </Link>
                    <Link to="/checkin" className="flex-1 py-4 active:bg-neutral-300">
                        <i className="ti ti-send text-2xl text-neutral-600" />
                    </Link>
                </>
            ) : (
                <>
                    <Button as="a" href="/register" className="flex-1 ml-4 mr-2" variant="primary">
                        アカウントを作成
                    </Button>
                    <Button as="a" href="/login" className="flex-1 mr-4 ml-2">
                        ログイン
                    </Button>
                </>
            )}
        </nav>
    );
};
