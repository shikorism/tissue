import React, { useEffect, useState } from 'react';
import { ListGroup, ListGroupItem, Modal, ModalProps } from 'react-bootstrap';
import { SortKeySelect } from './SortKeySelect';
import { sortAndFilteredCollections } from './search';

type SortKey = 'id:asc' | 'id:desc' | 'name:asc' | 'name:desc' | 'updated_at:asc' | 'updated_at:desc';

interface CollectionSelectModalProps extends ModalProps {
    title: string;
    collections: Tissue.Collection[];
    onSelectCollection: (collection: Tissue.Collection) => void;
}

export const CollectionSelectModal: React.FC<CollectionSelectModalProps> = ({
    title,
    collections,
    onSelectCollection,
    show,
    ...rest
}) => {
    const [filter, setFilter] = useState('');
    const [sort, setSort] = useState<SortKey>('updated_at:desc');

    useEffect(() => {
        if (show) {
            setFilter('');
            setSort('updated_at:desc');
        }
    }, [show]);

    return (
        <Modal show={show} {...rest}>
            <Modal.Header closeButton>
                <Modal.Title as="h5">{title}</Modal.Title>
            </Modal.Header>
            <Modal.Body>
                <ListGroup variant="flush" className="m-n3 rounded">
                    <ListGroupItem>
                        <div>
                            <input
                                className="form-control"
                                type="search"
                                placeholder="検索"
                                value={filter}
                                onChange={(e) => setFilter(e.target.value)}
                            />
                        </div>
                        <SortKeySelect className="mt-2" value={sort} onChange={setSort} />
                    </ListGroupItem>
                    {sortAndFilteredCollections(collections, sort, filter).map((collection) => (
                        <ListGroupItem key={collection.id} action onClick={() => onSelectCollection(collection)}>
                            <i className="ti ti-folder mr-2 text-secondary" />
                            {collection.title}
                        </ListGroupItem>
                    ))}
                </ListGroup>
            </Modal.Body>
        </Modal>
    );
};
