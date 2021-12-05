import React from 'react';
import { createSearchParams, Link, useSearchParams } from 'react-router-dom';

type PaginationProps = {
    className?: string;
    perPage: number;
    totalCount: number;
};

function* pageNumbers(totalPages: number): Generator<number> {
    for (let i = 0; i < totalPages; i++) {
        yield i + 1;
    }
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
                {Array.from(pageNumbers(totalPages)).map((i) =>
                    i === page ? (
                        <li key={i} className="page-item active">
                            <span className="page-link">{i}</span>
                        </li>
                    ) : (
                        <li key={i} className="page-item">
                            <Link className="page-link" to={{ search: pageQuery(searchParams, i) }}>
                                {i}
                            </Link>
                        </li>
                    )
                )}
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
                    <select className="custom-select tis-page-selector" aria-label="Page" onChange={handleChangePage}>
                        {Array.from(pageNumbers(totalPages)).map((i) => (
                            <option key={i} value={i} selected={i === page}>
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
