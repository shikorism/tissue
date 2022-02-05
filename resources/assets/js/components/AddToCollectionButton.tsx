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
};

export const AddToCollectionButton: React.FC<AddToCollectionButtonProps> = ({ link }) => {
    const handleSelect = async (eventKey: any) => {
        console.log(`handleSelect: ${eventKey}`);
        try {
            // TODO: 選択したコレクションに登録する
            const response = await fetchPostJson('/api/collections/inbox', { link });
            if (response.ok) {
                showToast('あとで抜く に追加しました', { color: 'success', delay: 5000 });
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
            showToast('あとで抜く に追加できませんでした', { color: 'danger', delay: 5000 });
        }
    };

    return (
        <Dropdown as="span" onSelect={handleSelect}>
            <OverlayTrigger placement="bottom" overlay={<Tooltip id="add_collection">コレクションに追加</Tooltip>}>
                <Dropdown.Toggle as={ToggleButton} />
            </OverlayTrigger>
            <Dropdown.Menu>
                <Dropdown.Header>コレクションに追加</Dropdown.Header>
                <Dropdown.Item eventKey="1">あとで抜く</Dropdown.Item>
            </Dropdown.Menu>
        </Dropdown>
    );
};
