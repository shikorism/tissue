<template>
    <div class="form-row" v-if="state !== MetadataLoadState.Inactive">
        <div class="form-group col-sm-12">
            <div class="card link-card-mini mb-2 px-0">
                <div v-if="state === MetadataLoadState.Loading" class="row no-gutters">
                    <div class="col-12">
                        <div class="card-body">
                            <h6 class="card-title text-center font-weight-bold text-info" style="font-size: small;"><span class="oi oi-loop-circular"></span> オカズの情報を読み込んでいます…</h6>
                        </div>
                    </div>
                </div>
                <div v-else-if="state === MetadataLoadState.Success" class="row no-gutters">
                    <div v-if="hasImage" class="col-4 justify-content-center align-items-center">
                        <img :src="metadata.image" alt="Thumbnail" class="card-img-top-to-left bg-secondary">
                    </div>
                    <div :class="descClasses">
                        <div class="card-body">
                            <h6 class="card-title font-weight-bold" style="font-size: small;">{{ metadata.title }}</h6>
                            <template v-if="suggestions.length > 0">
                                <p class="card-text mb-2" style="font-size: small;">タグ候補<br><span class="text-secondary">(クリックするとタグ入力欄にコピーできます)</span></p>
                                <ul class="list-inline d-inline">
                                    <li v-for="tag in suggestions"
                                        :class="tagClasses(tag)"
                                        @click="addTag(tag.name)"><span class="oi oi-tag"></span> {{ tag.name }}</li>
                                </ul>
                            </template>
                        </div>
                    </div>
                </div>
                <div v-else class="row no-gutters">
                    <div class="col-12">
                        <div class="card-body">
                            <h6 class="card-title text-center font-weight-bold text-danger" style="font-size: small;"><span class="oi oi-circle-x"></span> オカズの情報を読み込めませんでした</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
    import {Vue, Component, Prop} from "vue-property-decorator";
    import {bus, MetadataLoadState} from "../checkin";

    type Metadata = {
        url: string,
        title: string,
        description: string,
        image: string,
        expires_at: string | null,
        tags: {
            name: string
        }[],
    };

    type Suggestion = {
        name: string,
        used: boolean,
    }

    @Component
    export default class MetadataPreview extends Vue {
        @Prop() readonly state!: MetadataLoadState;
        @Prop() readonly metadata!: Metadata | null;

        // for use in v-if
        private readonly MetadataLoadState = MetadataLoadState;

        tags: string[] = [];

        created() {
            bus.$on("change-tag", (tags: string[]) => this.tags = tags);
            bus.$emit("resend-tag");
        }

        addTag(tag: string) {
            bus.$emit("add-tag", tag);
        }

        tagClasses(s: Suggestion) {
            return {
                "list-inline-item": true,
                "badge": true,
                "badge-primary": !s.used,
                "badge-secondary": s.used,
                "metadata-tag-item": true,
            };
        }

        get suggestions(): Suggestion[] {
            if (this.metadata === null) {
                return [];
            }

            return this.metadata.tags.map(t => {
                return {
                    name: t.name,
                    used: this.tags.indexOf(t.name) !== -1
                };
            });
        }

        get hasImage() {
            return this.metadata !== null && this.metadata.image !== ''
        }

        get descClasses() {
            return {
                "col-8": this.hasImage,
                "col-12": !this.hasImage,
            };
        }
    }
</script>

<style lang="scss" scoped>
    .link-card-mini {
        $height: 150px;

        .row > div {
            overflow: hidden;
        }

        .row > div:first-child {
            display: flex;

            &:not([display=none]) {
                min-height: $height;

                img {
                    position: absolute;
                }
            }
        }

        .card-text {
            white-space: pre-line;
        }
    }

    .metadata-tag-item {
        cursor: pointer;
        user-select: none;
    }
</style>
