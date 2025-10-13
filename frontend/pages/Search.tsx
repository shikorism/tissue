import React, { FormEventHandler } from 'react';
import { Link, Outlet, useLocation, useNavigate, useSearchParams } from 'react-router';
import { cn } from '../lib/cn';
import { Tab, Tabs } from '../components/Tabs';

export const Search: React.FC = () => {
    const location = useLocation();
    const navigate = useNavigate();
    const [searchParams] = useSearchParams();

    const handleSubmit: FormEventHandler<HTMLFormElement> = (event) => {
        event.preventDefault();

        const data = new FormData(event.currentTarget);
        const params = new URLSearchParams(searchParams);
        params.set('q', `${data.get('q')}`);
        navigate({ search: params.toString() });
    };

    return (
        <>
            <div className="px-4 pt-4">
                <form className="relative" onSubmit={handleSubmit}>
                    <input
                        type="search"
                        name="q"
                        className={cn(
                            'block w-full rounded-full border pl-10 pr-4 py-2 transition duration-150 ease-in-out focus:outline-none focus:ring-4',
                            'border-neutral-300 focus:border-primary-400 focus:ring-primary-400/25',
                        )}
                        required
                        placeholder="キーワードを入力..."
                        key={searchParams.get('q') ?? ''}
                        defaultValue={searchParams.get('q') ?? ''}
                    />
                    <i className="ti ti-search text-neutral-500 absolute left-3 top-1/2 -translate-y-1/2 text-xl" />
                </form>
                <div className="-mx-4 mt-2 px-4 border-b-1 border-gray-border">
                    <Tabs className="flex-nowrap overflow-auto">
                        <Tab active={location.pathname === '/search' || location.pathname === '/search/checkin'}>
                            <Link
                                to={{ pathname: '/search/checkin', search: searchParams.toString() }}
                                className="block px-4 md:px-5 py-3"
                            >
                                チェックイン
                            </Link>
                        </Tab>
                        <Tab active={location.pathname === '/search/collection'}>
                            <Link
                                to={{ pathname: '/search/collection', search: searchParams.toString() }}
                                className="block px-4 md:px-5 py-3"
                            >
                                コレクション
                            </Link>
                        </Tab>
                        <Tab active={location.pathname === '/search/related-tag'}>
                            <Link
                                to={{ pathname: '/search/related-tag', search: searchParams.toString() }}
                                className="block px-4 md:px-5 py-3"
                            >
                                関連するタグ
                            </Link>
                        </Tab>
                    </Tabs>
                </div>
            </div>
            <Outlet />
        </>
    );
};

export default Search;
