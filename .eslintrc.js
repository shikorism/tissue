module.exports = {
    env: {
        browser: true,
        es2020: true,
        node: true,
    },
    extends: [
        'eslint:recommended',
        'plugin:vue/essential',
        'plugin:prettier/recommended',
        'plugin:@typescript-eslint/recommended',
        'prettier',
        'prettier/@typescript-eslint',
        'prettier/vue',
    ],
    parser: 'vue-eslint-parser',
    parserOptions: {
        ecmaVersion: 11,
        parser: '@typescript-eslint/parser',
        sourceType: 'module',
    },
    plugins: ['prettier', 'vue', '@typescript-eslint', 'jquery'],
    rules: {
        '@typescript-eslint/explicit-module-boundary-types': 0,
        '@typescript-eslint/no-explicit-any': 0,
        '@typescript-eslint/no-unused-vars': ['warn', { argsIgnorePattern: '^_' }],
        'jquery/no-ajax': 2,
        'jquery/no-ajax-events': 2,
    },
};
