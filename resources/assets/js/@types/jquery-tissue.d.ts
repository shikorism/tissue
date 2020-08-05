// tissue.ts で定義されているjQuery Pluginの型定義
declare namespace JQueryTissue {
    interface LinkCardOptions {
        endpoint: string;
    }
}

interface JQuery<TElement = HTMLElement> {
    linkCard: (options?: JQueryTissue.LinkCardOptions) => this;
    deleteCheckinModal: () => this;
}
