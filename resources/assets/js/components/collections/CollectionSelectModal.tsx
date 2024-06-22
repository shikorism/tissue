import React from 'react';
import { ListGroup, ListGroupItem, Modal, ModalProps } from 'react-bootstrap';

interface CollectionSelectModalProps extends ModalProps {
    title: string;
    collections: Tissue.Collection[];
    onSelectCollection: (collection: Tissue.Collection) => void;
}

export const CollectionSelectModal: React.FC<CollectionSelectModalProps> = ({
    title,
    collections,
    onSelectCollection,
    ...rest
}) => {
    return (
        <Modal {...rest}>
            <Modal.Header closeButton>
                <Modal.Title as="h5">{title}</Modal.Title>
            </Modal.Header>
            <Modal.Body>
                <ListGroup variant="flush" className="m-n3 rounded">
                    {collections.map((collection) => (
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
