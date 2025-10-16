import React from 'react';
import { Link, useLoaderData } from 'react-router';
import { useSuspenseQuery } from '@tanstack/react-query';
import { LoaderData } from './Tags.loader';
import { getTags } from '../api/query';
import { Pagination } from '../components/Pagination';

export const Tags: React.FC = () => {
    const { query } = useLoaderData<LoaderData>();
    const { data: tags } = useSuspenseQuery(getTags(query));

    return (
        <div className="p-4">
            <h1 className="text-3xl">タグ一覧</h1>
            <p className="my-4 pb-4 text-sm text-secondary border-b-1 border-gray-border">
                公開チェックインに付けられているタグを、チェックイン数の多い順で表示しています。
            </p>
            <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                {tags.data.map((tag) => (
                    <div key={tag.name}>
                        <Link
                            to={{ pathname: `/search`, search: `?q=${tag.name}` }}
                            className="block px-4 py-2 text-center bg-neutral-200 hover:bg-neutral-300 focus:bg-neutral-300 outline-none transition-colors [clip-path:polygon(20px_0%,100%_0%,100%_100%,20px_100%,0%_50%)]"
                        >
                            {tag.name} ({tag.checkins_count})
                        </Link>
                    </div>
                ))}
            </div>
            {tags?.totalCount && <Pagination className="mt-4" totalCount={tags.totalCount} perPage={100} />}
        </div>
    );
};

export default Tags;
