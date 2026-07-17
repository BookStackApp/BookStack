import globals from 'globals';
import js from '@eslint/js';

export default [
    js.configs.recommended,
    {
        ignores: ['resources/**/*-stub.js', 'resources/**/*.ts'],
    }, {
        languageOptions: {
            globals: {
                ...globals.browser,
            },

            ecmaVersion: 'latest',
            sourceType: 'module',
        },

        rules: {
            indent: ['error', 4],
            'arrow-parens': ['error', 'as-needed'],

            'padded-blocks': ['error', {
                blocks: 'never',
                classes: 'always',
            }],

            'object-curly-spacing': ['error', 'never'],

            'space-before-function-paren': ['error', {
                anonymous: 'never',
                named: 'never',
                asyncArrow: 'always',
            }],

            'import/prefer-default-export': 'off',

            'no-plusplus': ['error', {
                allowForLoopAfterthoughts: true,
            }],

            'arrow-body-style': 'off',
            'no-restricted-syntax': 'off',
            'no-continue': 'off',
            'prefer-destructuring': 'off',
            'class-methods-use-this': 'off',
            'no-param-reassign': 'off',

            'no-console': ['warn', {
                allow: ['error', 'warn'],
            }],

            'no-new': 'off',

            'max-len': ['error', {
                code: 110,
                tabWidth: 4,
                ignoreUrls: true,
                ignoreComments: false,
                ignoreRegExpLiterals: true,
                ignoreStrings: true,
                ignoreTemplateLiterals: true,
            }],
        },
    }];
