import React from 'react';
import { createSearchParams, Link, useSearchParams } from 'react-router';

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
            <ul className={`pagination d-none d-lg-flex ${className}`}>
                {page === 1 ? (
                    <li className="page-item disabled">
                        <span className="page-link">&laquo;</span>
                    </li>
                ) : (
                    <li className="page-item">
                        <Link className="page-link" to={{ search: pageQuery(searchParams, page - 1) }} rel="prev">
                            &laquo;
                        </Link>
                    </li>
                )}
                {slidedPageNumbers(page, totalPages).map((chunk, i) => (
                    <React.Fragment key={i}>
                        {i !== 0 && (
                            <li className="page-item disabled">
                                <span className="page-link">...</span>
                            </li>
                        )}
                        {chunk.map((p) =>
                            p === page ? (
                                <li key={p} className="page-item active">
                                    <span className="page-link">{p}</span>
                                </li>
                            ) : (
                                <li key={p} className="page-item">
                                    <Link className="page-link" to={{ search: pageQuery(searchParams, p) }}>
                                        {p}
                                    </Link>
                                </li>
                            ),
                        )}
                    </React.Fragment>
                ))}
                {page === totalPages ? (
                    <li className="page-item disabled">
                        <span className="page-link">&raquo;</span>
                    </li>
                ) : (
                    <li className="page-item">
                        <Link className="page-link" to={{ search: pageQuery(searchParams, page + 1) }} rel="prev">
                            &raquo;
                        </Link>
                    </li>
                )}
            </ul>
            <ul className={`pagination d-flex d-lg-none ${className}`}>
                {page === 1 ? (
                    <li className="page-item w-25 text-center disabled">
                        <span className="page-link">&laquo;</span>
                    </li>
                ) : (
                    <li className="page-item w-25 text-center">
                        <Link className="page-link" to={{ search: pageQuery(searchParams, page - 1) }} rel="prev">
                            &laquo;
                        </Link>
                    </li>
                )}
                <li className="page-item w-25 text-center">
                    <select
                        className="custom-select tis-page-selector"
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
                    <li className="page-item w-25 text-center disabled">
                        <span className="page-link">&raquo;</span>
                    </li>
                ) : (
                    <li className="page-item w-25 text-center">
                        <Link className="page-link" to={{ search: pageQuery(searchParams, page + 1) }} rel="prev">
                            &raquo;
                        </Link>
                    </li>
                )}
            </ul>
        </>
    );
};
