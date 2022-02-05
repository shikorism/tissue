import React from 'react';
import { Button, Dropdown, OverlayTrigger, Tooltip } from 'react-bootstrap';
import { fetchPostJson, ResponseError } from '../fetch';
import { showToast } from '../tissue';

const ToggleButton = React.forwardRef<HTMLButtonElement>((props, ref) => (
    <Button {...props} ref={ref} variant="link" className="text-secondary">
        <span className="oi oi-plus" />
    </Button>
));
ToggleButton.displayName = 'ToggleButton';

type AddToCollectionButtonProps = {
    link: string;
    collections: Tissue.Collection[] | undefined;
};

export const AddToCollectionButton: React.FC<AddToCollectionButtonProps> = ({ link, collections }) => {
    const handleSelect = async (eventKey: string | null) => {
        const collection = collections?.find((collection) => collection.id == eventKey);
        if (!collection) {
            return;
        }
        try {
            const response = await fetchPostJson(`/api/collections/${collection.id}/items`, { link });
            if (response.ok) {
                showToast(`${collection.title} に追加しました`, { color: 'success', delay: 5000 });
            } else {
                throw new ResponseError(response);
            }
        } catch (e) {
            console.error(e);
            if (e instanceof ResponseError && e.response.status == 422) {
                const data = await e.response.json();
                if (data.error?.violations && data.error.violations.some((v: any) => v.field === 'link')) {
                    showToast('すでに登録されています', { color: 'danger', delay: 5000 });
                    return;
                }
            }
            showToast(`${collection.title} に追加できませんでした`, { color: 'danger', delay: 5000 });
        }
    };

    return (
        <Dropdown as="span" onSelect={handleSelect}>
            <OverlayTrigger placement="bottom" overlay={<Tooltip id="add_collection">コレクションに追加</Tooltip>}>
                <Dropdown.Toggle as={ToggleButton} />
            </OverlayTrigger>
            <Dropdown.Menu>
                <Dropdown.Header>コレクションに追加</Dropdown.Header>
                {collections?.map((collection) => (
                    <Dropdown.Item key={collection.id} eventKey={collection.id}>
                        {collection.title}
                    </Dropdown.Item>
                ))}
            </Dropdown.Menu>
        </Dropdown>
    );
};
