import React from 'react';
import { Link } from 'react-router';

interface Props {
    className?: string;
    tags: { name: string; count: number }[];
}

export const TagRanking: React.FC<Props> = ({ className, tags }) => (
    <table className={className}>
        <tbody>
            {tags.map((tag) => (
                <tr key={tag.name} className="border-1 border-gray-border odd:bg-gray-back">
                    <td>
                        <Link
                            to={{ pathname: `/search`, search: `?q=${tag.name}` }}
                            className="block px-4 py-3 break-all group"
                        >
                            <i className="ti ti-tag text-secondary mr-2"></i>
                            <span className="group-hover:underline">{tag.name}</span>
                        </Link>
                    </td>
                    <td>
                        <Link
                            to={{ pathname: `/search`, search: `?q=${tag.name}` }}
                            className="block px-4 py-3 text-end"
                        >
                            {tag.count}
                        </Link>
                    </td>
                </tr>
            ))}
        </tbody>
    </table>
);
