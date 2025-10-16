/// <reference types="vite/client" />

interface ViteTypeOptions {
    strictImportMetaEnv: unknown;
}

interface ImportMetaEnv {
    readonly VITE_APP_SUPPORT_LINK: string;
}

interface ImportMeta {
    readonly env: ImportMetaEnv;
}
