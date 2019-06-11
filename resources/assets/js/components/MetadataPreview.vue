<template>
    <div class="form-row" v-if="metadata !== null">
        <div class="form-group col-sm-12">
            <div class="card link-card-mini mb-2 px-0">
                <div class="row no-gutters">
                    <div class="col-4 justify-content-center align-items-center">
                        <img :src="metadata.image" alt="Thumbnail" class="card-img-top-to-left bg-secondary">
                    </div>
                    <div class="col-8">
                        <div class="card-body">
                            <h6 class="card-title font-weight-bold" style="font-size: small;">{{ metadata.title }}</h6>
                            <template v-if="suggestions.length > 0">
                                <p class="card-text mb-2" style="font-size: small;">タグ候補<br><span class="text-secondary">(クリックするとタグ入力欄にコピーできます)</span></p>
                                <ul class="list-inline d-inline">
                                    <li v-for="tag in suggestions"
                                        class="list-inline-item badge badge-primary metadata-tag-item"
                                        @click="addTag(tag)"><span class="oi oi-tag"></span> {{ tag }}</li>
                                </ul>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
    import {Vue, Component, Prop} from "vue-property-decorator";
    import {bus} from "../checkin";

    type Metadata = {
        url: string,
        title: string | null,
        description: string | null,
        image: string | null,
        expires_at: string | null,
        tags: {
            name: string
        }[],
    };

    @Component
    export default class MetadataPreview extends Vue {
        @Prop() readonly metadata!: Metadata | null;

        addTag(tag: string) {
            bus.$emit("add-tag", tag);
        }

        get suggestions() {
            if (this.metadata === null) {
                return [];
            }

            return this.metadata.tags.map(t => t.name);
        }
    }
</script>

<style lang="scss" scoped>
    .metadata-tag-item {
        cursor: pointer;
        user-select: none;
    }
</style>
