<template>
    <div class="form-control h-auto" @click="$refs.input.focus()">
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
    import {Vue, Component, Prop} from "vue-property-decorator";

    function getElementByName(elementName: string): HTMLElement | null {
        const elements = document.getElementsByName(elementName);
        if (elements.length) {
            return elements[0];
        }
        return null;
    }

    @Component
    export default class TagInput extends Vue {
        @Prop(String) readonly id!: string;
        @Prop(String) readonly input!: string;
        @Prop(Boolean) readonly isInvalid!: boolean;

        tags: string[] = [];
        buffer: string = "";

        mounted() {
            const tags = getElementByName(this.input);
            if (tags instanceof HTMLInputElement && tags.value.trim() !== "") {
                this.tags = tags.value.split(" ")
            }
        }

        onKeyDown(event: KeyboardEvent) {
            if (this.buffer.trim() !== "") {
                switch (event.key) {
                    case 'Tab':
                    case 'Enter':
                    case ' ':
                        if ((event as any).isComposing !== true) {
                            this.tags.push(this.buffer);
                            this.buffer = "";
                            this.sync();
                        }
                        event.preventDefault();
                        break;
                }
            } else if (event.key === "Enter") {
                // 誤爆防止
                event.preventDefault();
            }
        }

        removeTag(index: number) {
            this.tags.splice(index, 1);
            this.sync();
        }

        sync() {
            const tags = getElementByName(this.input);
            if (tags instanceof HTMLInputElement) {
                tags.value = this.tags.join(" ");
            }
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