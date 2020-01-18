<template>
    <div :class="containerClass" @click="$refs.input.focus()">
        <input :name="name" type="hidden" :value="tagValue">
        <ul class="list-inline d-inline">
            <li v-for="(tag, i) in tags"
                class="list-inline-item badge badge-primary tag-item"
                @click="removeTag(i)"><span class="oi oi-tag"></span> {{ tag }} | x</li>
        </ul>
        <input :id="id"
               ref="input"
               type="text"
               class="tag-input"
               v-model="buffer"
               @keydown="onKeyDown">
    </div>
</template>

<script lang="ts">
    import {Vue, Component, Prop, Watch} from "vue-property-decorator";
    import {bus} from "../checkin";

    @Component
    export default class TagInput extends Vue {
        @Prop(String) readonly id!: string;
        @Prop(String) readonly name!: string;
        @Prop(String) readonly value!: string;
        @Prop(Boolean) readonly isInvalid!: boolean;

        tags: string[] = this.value.trim() !== "" ? this.value.trim().split(" ") : [];
        buffer: string = "";

        created() {
            bus.$on("add-tag", (tag: string) => this.tags.indexOf(tag) === -1 && this.tags.push(tag));
            bus.$on("resend-tag", () => bus.$emit("change-tag", this.tags));
        }

        onKeyDown(event: KeyboardEvent) {
            if (this.buffer.trim() !== "") {
                switch (event.key) {
                    case 'Tab':
                    case 'Enter':
                    case ' ':
                        if ((event as any).isComposing !== true) {
                            this.tags.push(this.buffer.trim());
                            this.buffer = "";
                        }
                        event.preventDefault();
                        break;
                    case 'Unidentified':
                        // 実際にテキストボックスに入力されている文字を見に行く (フォールバック処理)
                        if (event.srcElement && (event.srcElement as HTMLInputElement).value.slice(-1) == ' ') {
                            this.tags.push(this.buffer.trim());
                            this.buffer = "";
                            event.preventDefault();
                        }
                        break;
                }
            } else if (event.key === "Enter") {
                // 誤爆防止
                event.preventDefault();
            }
        }

        removeTag(index: number) {
            this.tags.splice(index, 1);
        }

        @Watch("tags")
        onTagsChanged() {
            bus.$emit("change-tag", this.tags);
        }

        get containerClass(): object {
            return {
                "form-control": true,
                "h-auto": true,
                "is-invalid": this.isInvalid
            };
        }

        get tagValue(): string {
            return this.tags.join(" ");
        }
    }
</script>

<style lang="scss" scoped>
    .tag-item {
        cursor: pointer;
    }

    .tag-input {
        border: 0;
        outline: 0;
    }
</style>
