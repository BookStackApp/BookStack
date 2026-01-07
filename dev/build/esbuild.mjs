#!/usr/bin/env node

import * as esbuild from 'esbuild';
import * as path from 'node:path';
import * as fs from 'node:fs';
import * as process from "node:process";

// Check if we're building for production
// (Set via passing `production` as first argument)
const mode = process.argv[2];
const isProd = mode === 'production';
const __dirname = import.meta.dirname;

// Gather our input files
const entryPoints = {
    app: path.join(__dirname, '../../resources/js/app.ts'),
    code: path.join(__dirname, '../../resources/js/code/index.mjs'),
    'legacy-modes': path.join(__dirname, '../../resources/js/code/legacy-modes.mjs'),
    markdown: path.join(__dirname, '../../resources/js/markdown/index.mts'),
    wysiwyg: path.join(__dirname, '../../resources/js/wysiwyg/index.ts'),
};

// Watch styles so we can reload on change
if (mode === 'watch') {
    entryPoints['styles-dummy'] = path.join(__dirname, '../../public/dist/styles.css');
}

// Locate our output directory
const outdir = path.join(__dirname, '../../public/dist');

// Define the options for esbuild
const options = {
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
        '.html': 'copy',
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
};

if (mode === 'watch') {
    options.inject = [
        path.join(__dirname, './livereload.js'),
    ];
}

const ctx = await esbuild.context(options);

if (mode === 'watch') {
    // Watch for changes and rebuild on change
    ctx.watch({});
    let {hosts, port} = await ctx.serve({
        servedir: path.join(__dirname, '../../public'),
        cors: {
            origin: '*',
        }
    });
} else {
    // Build with meta output for analysis
    const result = await ctx.rebuild();
    const outputs = result.metafile.outputs;
    const files = Object.keys(outputs);
    for (const file of files) {
        const output = outputs[file];
        console.log(`Written: ${file} @ ${Math.round(output.bytes / 1000)}kB`);
    }
    fs.writeFileSync('esbuild-meta.json', JSON.stringify(result.metafile));
    process.exit(0);
}
