import React, { FormEventHandler } from 'react';
import { Link, Outlet, useLocation, useNavigate, useSearchParams } from 'react-router';
import { Tab, Tabs } from '../components/ui/Tabs';
import { SearchInput } from '../components/ui/SearchInput';

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
                <form onSubmit={handleSubmit}>
                    <SearchInput
                        type="search"
                        name="q"
                        key={searchParams.get('q') ?? ''}
                        defaultValue={searchParams.get('q') ?? ''}
                        placeholder="キーワードを入力..."
                    />
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
                    </Tabs>
                </div>
            </div>
            <Outlet />
        </>
    );
};

export default Search;
