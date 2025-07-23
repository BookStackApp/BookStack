#!/usr/bin/env node

const esbuild = require('esbuild');
const path = require('path');
const fs = require('fs');

// Check if we're building for production
// (Set via passing `production` as first argument)
const isProd = process.argv[2] === 'production';

// Gather our input files
const entryPoints = {
    app: path.join(__dirname, '../../resources/js/app.ts'),
    code: path.join(__dirname, '../../resources/js/code/index.mjs'),
    'legacy-modes': path.join(__dirname, '../../resources/js/code/legacy-modes.mjs'),
    markdown: path.join(__dirname, '../../resources/js/markdown/index.mts'),
    wysiwyg: path.join(__dirname, '../../resources/js/wysiwyg/index.ts'),
};

// Locate our output directory
const outdir = path.join(__dirname, '../../public/dist');

// Build via esbuild
esbuild.build({
    bundle: true,
    metafile: true,
    entryPoints,
    outdir,
    sourcemap: true,
    target: 'es2021',
    mainFields: ['module', 'main'],
    format: 'esm',
    minify: isProd,
    logLevel: 'info',
    loader: {
        '.svg': 'text',
    },
    absWorkingDir: path.join(__dirname, '../..'),
    alias: {
        '@icons': './resources/icons',
        lexical: './resources/js/wysiwyg/lexical/core',
        '@lexical': './resources/js/wysiwyg/lexical',
    },
    banner: {
        js: '// See the "/licenses" URI for full package license details',
        css: '/* See the "/licenses" URI for full package license details */',
    },
}).then(result => {
    fs.writeFileSync('esbuild-meta.json', JSON.stringify(result.metafile));
}).catch(() => process.exit(1));
