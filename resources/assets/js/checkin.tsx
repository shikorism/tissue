import Vue from 'vue';
import TagInput from './components/TagInput.vue';
import MetadataPreview from './components/MetadataPreview.vue';
import { fetchGet, ResponseError } from './fetch';
import * as React from 'react';
import * as ReactDOM from 'react-dom';
import TagInput2 from './components/TagInput2';

export const bus = new Vue({ name: 'EventBus' });

export enum MetadataLoadState {
    Inactive,
    Loading,
    Success,
    Failed,
}

export type Metadata = {
    url: string;
    title: string;
    description: string;
    image: string;
    expires_at: string | null;
    tags: {
        name: string;
    }[];
};

new Vue({
    el: '#app',
    data: {
        metadata: null,
        metadataLoadState: MetadataLoadState.Inactive,
    },
    components: {
        TagInput,
        MetadataPreview,
    },
    mounted() {
        // オカズリンクにURLがセットされている場合は、すぐにメタデータを取得する
        const linkInput = this.$el.querySelector<HTMLInputElement>('#link');
        if (linkInput && /^https?:\/\//.test(linkInput.value)) {
            this.fetchMetadata(linkInput.value);
        }
    },
    methods: {
        // オカズリンクの変更時
        onChangeLink(event: Event) {
            if (event.target instanceof HTMLInputElement) {
                const url = event.target.value;

                if (url.trim() === '' || !/^https?:\/\//.test(url)) {
                    this.metadata = null;
                    this.metadataLoadState = MetadataLoadState.Inactive;
                    return;
                }

                this.fetchMetadata(url);
            }
        },
        // メタデータの取得
        fetchMetadata(url: string) {
            this.metadataLoadState = MetadataLoadState.Loading;

            fetchGet('/api/checkin/card', { url })
                .then((response) => {
                    if (!response.ok) {
                        throw new ResponseError(response);
                    }
                    return response.json();
                })
                .then((data) => {
                    this.metadata = data;
                    this.metadataLoadState = MetadataLoadState.Success;
                })
                .catch(() => {
                    this.metadata = null;
                    this.metadataLoadState = MetadataLoadState.Failed;
                });
        },
    },
});

ReactDOM.render(
    <TagInput2 id={'tagInput2'} name={'tags2'} value={''} isInvalid={false} />,
    document.querySelector('#tagInput2')
);
