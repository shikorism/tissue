import React from 'react';
import { createRoot } from 'react-dom/client';
import type { Root } from 'react-dom/client';
import { fetchGet, ResponseError } from './fetch';
import { AddToCollectionButton } from './components/collections/AddToCollectionButton';

export async function initAddToCollectionButtons() {
    const addToCollectionButtons: { root: Root; link: string; tags: string[] }[] = [];
    document.querySelectorAll<HTMLElement>('.add-to-collection-button').forEach((el) => {
        const link = el.dataset.link;
        if (!link) {
            return;
        }
        const tags = (el.dataset.tags || '').split(' ').filter((tag) => tag !== '');
        addToCollectionButtons.push({ root: createRoot(el), link, tags });
    });

    if (addToCollectionButtons.length > 0) {
        addToCollectionButtons.forEach(({ root, link, tags }) => {
            root.render(<AddToCollectionButton link={link} tags={tags} />);
        });

        const response = await fetchGet(`/api/collections`);
        if (response.ok) {
            const data = (await response.json()) as Tissue.Collection[];
            addToCollectionButtons.forEach(({ root, link, tags }) => {
                root.render(
                    <AddToCollectionButton
                        link={link}
                        tags={tags}
                        collections={data}
                        onCreateCollection={initAddToCollectionButtons}
                    />,
                );
            });
        } else {
            console.error(new ResponseError(response));
        }
    }
}
