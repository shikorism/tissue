import React from 'react';
import { createSearchParams, Link, useSearchParams } from 'react-router';
import { cn } from '../lib/cn';

type PaginationProps = {
    className?: string;
    perPage: number;
    totalCount: number;
};

const range = (begin: number, end: number) => [...new Array(end - begin + 1)].map((_, i) => i + begin);

function slidedPageNumbers(page: number, totalPages: number, slide = 3) {
    const window = slide * 2;
    if (totalPages < window + 6) {
        return [range(1, totalPages)];
    }

    if (page <= window) {
        return [range(1, window + 2), [totalPages - 1, totalPages]];
    }
    if (page > totalPages - window) {
        return [[1, 2], range(totalPages - window - 1, totalPages)];
    }

    return [[1, 2], range(page - slide, page + slide), [totalPages - 1, totalPages]];
}

function pageQuery(searchParams: URLSearchParams, page: number): string {
    const sp = createSearchParams(searchParams);
    sp.set('page', page.toString());
    return sp.toString();
}

export const Pagination: React.FC<PaginationProps> = ({ className, perPage, totalCount }) => {
    const [searchParams, setSearchParams] = useSearchParams();
    const page = parseInt(searchParams.get('page') || '1', 10);
    const totalPages = Math.ceil(totalCount / perPage);
    if (totalPages < 2) {
        return null;
    }

    const handleChangePage = (e: React.ChangeEvent<HTMLSelectElement>) => {
        searchParams.set('page', e.target.value);
        setSearchParams(searchParams);
    };

    return (
        <>
            <ul className={cn('hidden lg:flex justify-center', className)}>
                {page === 1 ? (
                    <li>
                        <span className="block px-4 py-2 rounded-l border-1 border-gray-border text-center text-neutral-500 pointer-events-none cursor-auto">
                            &laquo;
                        </span>
                    </li>
                ) : (
                    <li>
                        <Link
                            className="block px-4 py-2 rounded-l border-1 border-gray-border text-center text-primary hover:bg-neutral-100"
                            to={{ search: pageQuery(searchParams, page - 1) }}
                            rel="prev"
                        >
                            &laquo;
                        </Link>
                    </li>
                )}
                {slidedPageNumbers(page, totalPages).map((chunk, i) => (
                    <React.Fragment key={i}>
                        {i !== 0 && (
                            <li>
                                <span className="block -ml-px px-4 py-2 border-1 border-gray-border text-center text-neutral-500 pointer-events-none cursor-auto">
                                    ...
                                </span>
                            </li>
                        )}
                        {chunk.map((p) =>
                            p === page ? (
                                <li key={p}>
                                    <span className="block -ml-px px-4 py-2 border-1 border-primary text-center text-white bg-primary relative z-10">
                                        {p}
                                    </span>
                                </li>
                            ) : (
                                <li key={p} className="page-item">
                                    <Link
                                        className="block -ml-px px-4 py-2 border-1 border-gray-border text-center text-primary hover:bg-neutral-100"
                                        to={{ search: pageQuery(searchParams, p) }}
                                    >
                                        {p}
                                    </Link>
                                </li>
                            ),
                        )}
                    </React.Fragment>
                ))}
                {page === totalPages ? (
                    <li>
                        <span className="block -ml-px px-4 py-2 rounded-r border-1 border-gray-border text-center text-neutral-500 pointer-events-none cursor-auto">
                            &raquo;
                        </span>
                    </li>
                ) : (
                    <li>
                        <Link
                            className="block -ml-px px-4 py-2 rounded-r border-1 border-gray-border text-center text-primary hover:bg-neutral-100"
                            to={{ search: pageQuery(searchParams, page + 1) }}
                            rel="prev"
                        >
                            &raquo;
                        </Link>
                    </li>
                )}
            </ul>
            <ul className={cn('flex lg:hidden justify-center', className)}>
                {page === 1 ? (
                    <li className="w-1/4">
                        <span className="block py-2 rounded-l border-1 border-gray-border text-center text-neutral-500 pointer-events-none cursor-auto">
                            &laquo;
                        </span>
                    </li>
                ) : (
                    <li className="w-1/4">
                        <Link
                            className="block py-2 rounded-l border-1 border-gray-border text-center text-primary"
                            to={{ search: pageQuery(searchParams, page - 1) }}
                            rel="prev"
                        >
                            &laquo;
                        </Link>
                    </li>
                )}
                <li className="page-item w-25 text-center">
                    <select
                        className="inline-block w-full h-full px-2"
                        aria-label="Page"
                        value={page}
                        onChange={handleChangePage}
                    >
                        {range(1, totalPages).map((i) => (
                            <option key={i} value={i}>
                                {i}
                            </option>
                        ))}
                    </select>
                </li>
                {page === totalPages ? (
                    <li className="w-1/4">
                        <span className="block -ml-px py-2 rounded-r border-1 border-gray-border text-center text-neutral-500 pointer-events-none cursor-auto">
                            &raquo;
                        </span>
                    </li>
                ) : (
                    <li className="w-1/4">
                        <Link
                            className="block -ml-px py-2 rounded-r border-1 border-gray-border text-center text-primary"
                            to={{ search: pageQuery(searchParams, page + 1) }}
                            rel="prev"
                        >
                            &raquo;
                        </Link>
                    </li>
                )}
            </ul>
        </>
    );
};
