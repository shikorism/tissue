// @types/bootstrap に足りないもの
interface JQuery<TElement = HTMLElement> {
    modal(action: 'toggle' | 'show' | 'hide' | 'handleUpdate' | 'dispose', relatedTarget?: TElement): this;
}
