/// <reference types="vite/client" />

interface ViteTypeOptions {
    strictImportMetaEnv: unknown;
}

// interface ImportMetaEnv {}

interface ImportMeta {
    readonly env: ImportMetaEnv;
}
