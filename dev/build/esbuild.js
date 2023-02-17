#!/usr/bin/env node

const esbuild = require('esbuild');
const path = require('path');

// Check if we're building for production
// (Set via passing `production` as first argument)
const isProd = process.argv[2] === 'production';

// Gather our input files
const entryPoints = {
    app: path.join(__dirname, '../../resources/js/app.js'),
    code: path.join(__dirname, '../../resources/js/code/index.mjs'),
};

// Locate our output directory
const outdir = path.join(__dirname, '../../public/dist');

// Build via esbuild
esbuild.build({
    bundle: true,
    entryPoints,
    outdir,
    sourcemap: true,
    target: 'es2020',
    mainFields: ['module', 'main'],
    format: 'esm',
    minify: isProd,
    logLevel: "info",
}).catch(() => process.exit(1));