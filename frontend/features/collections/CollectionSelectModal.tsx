import React, { useEffect, useState } from 'react';
import { useQuery } from '@tanstack/react-query';
import type { components } from '../../api/schema';
import { Modal, ModalHeader, ModalBody, ModalProps } from '../../components/Modal';
import { getMyCollectionsQuery } from '../../api/query';
import { SortKeySelect } from './SortKeySelect';
import { sortAndFilteredCollections } from './search';

type Collection = components['schemas']['Collection'];
type SortKey = 'id:asc' | 'id:desc' | 'name:asc' | 'name:desc' | 'updated_at:asc' | 'updated_at:desc';

interface CollectionSelectModalProps extends Omit<ModalProps, 'children'> {
    title: string;
    onSelectCollection: (collection: Collection) => void;
}

export const CollectionSelectModal: React.FC<CollectionSelectModalProps> = ({
    title,
    onSelectCollection,
    isOpen,
    ...rest
}) => {
    const { data: collections } = useQuery(getMyCollectionsQuery());

    const [filter, setFilter] = useState('');
    const [sort, setSort] = useState<SortKey>('updated_at:desc');

    useEffect(() => {
        if (isOpen) {
            setFilter('');
            setSort('updated_at:desc');
        }
    }, [isOpen]);

    return (
        <Modal isOpen={isOpen} {...rest}>
            <ModalHeader closeButton>{title}</ModalHeader>
            <ModalBody className="p-0 flex flex-col max-h-[70svh]">
                <div className="p-4 border-b-1 border-gray-border flex flex-col gap-2">
                    <input
                        className="block w-full rounded border px-3 py-2 transition duration-150 ease-in-out focus:outline-none focus:ring-4 border-neutral-300 focus:border-primary-400 focus:ring-primary-400/25"
                        type="search"
                        placeholder="検索"
                        value={filter}
                        onChange={(e) => setFilter(e.target.value)}
                    />
                    <SortKeySelect value={sort} onChange={setSort} />
                </div>
                <ul className="shrink rounded-b overflow-y-auto *:not-first:border-t-1 *:not-first:border-gray-border">
                    {collections &&
                        sortAndFilteredCollections(collections, sort, filter).map((collection) => (
                            <li
                                className="px-4 py-3 break-all select-none cursor-pointer hover:bg-neutral-100"
                                key={collection.id}
                                onClick={() => onSelectCollection(collection)}
                            >
                                <i className="ti ti-folder mr-2 text-secondary" />
                                {collection.title}
                            </li>
                        ))}
                </ul>
            </ModalBody>
        </Modal>
    );
};
