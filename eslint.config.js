import js from '@eslint/js';
import ts from 'typescript-eslint';
import globals from 'globals';
import prettierRecommended from 'eslint-plugin-prettier/recommended';
import jquery from 'eslint-plugin-jquery';

export default [
    js.configs.recommended,
    ...ts.configs.recommended,
    prettierRecommended,
    {
        files: ['**/*.js', '**/*.ts', '**/*.tsx'],
        plugins: {
            jquery,
        },
        languageOptions: {
            sourceType: 'module',
            ecmaVersion: 11,
            parserOptions: {
                ecmaFeatures: {
                    jsx: true,
                },
            },
            globals: {
                ...globals.browser,
                ...globals.es2020,
                ...globals.node,
            },
        },
        rules: {
            '@typescript-eslint/explicit-module-boundary-types': 0,
            '@typescript-eslint/no-explicit-any': 0,
            '@typescript-eslint/no-unused-vars': ['warn', { argsIgnorePattern: '^_' }],
            'jquery/no-ajax': 2,
            'jquery/no-ajax-events': 2,
            'react/prop-types': 0,
        },
        settings: {
            react: {
                version: 'detect',
            },
        },
    },
];
