import React from 'react';
import ReactDOM from 'react-dom';
import { fetchGet, ResponseError } from './fetch';
import { AddToCollectionButton } from './components/collections/AddToCollectionButton';

export async function initAddToCollectionButtons() {
    const addToCollectionButtons = document.querySelectorAll<HTMLElement>('.add-to-collection-button');
    if (addToCollectionButtons.length > 0) {
        addToCollectionButtons.forEach((el) => {
            const link = el.dataset.link;
            const tags = (el.dataset.tags || '').split(' ').filter((tag) => tag !== '');
            if (link) {
                ReactDOM.render(<AddToCollectionButton link={link} tags={tags} />, el);
            }
        });

        const response = await fetchGet(`/api/collections`);
        if (response.ok) {
            const data = (await response.json()) as Tissue.Collection[];
            addToCollectionButtons.forEach((el) => {
                const link = el.dataset.link;
                const tags = (el.dataset.tags || '').split(' ').filter((tag) => tag !== '');
                if (link) {
                    ReactDOM.render(
                        <AddToCollectionButton
                            link={link}
                            tags={tags}
                            collections={data}
                            onCreateCollection={initAddToCollectionButtons}
                        />,
                        el
                    );
                }
            });
        } else {
            console.error(new ResponseError(response));
        }
    }
}
