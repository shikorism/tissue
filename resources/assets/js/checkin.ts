import Vue from 'vue';
import TagInput from "./components/TagInput.vue";
import MetadataPreview from './components/MetadataPreview.vue';

export const bus = new Vue({name: "EventBus"});

new Vue({
    el: '#app',
    data: {
        metadata: null
    },
    components: {
        TagInput,
        MetadataPreview
    },
    mounted() {
        // オカズリンクにURLがセットされている場合は、すぐにメタデータを取得する
        const linkInput = this.$el.querySelector<HTMLInputElement>("#link");
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
                    return;
                }

                this.fetchMetadata(url);
            }
        },
        // メタデータの取得
        fetchMetadata(url: string) {
            $.ajax({
                url: '/api/checkin/card',
                method: 'get',
                type: 'json',
                data: {
                    url
                }
            }).then(data => {
                this.metadata = data;
            }).catch(e => {
                this.metadata = null;
            });
        }
    }
});
