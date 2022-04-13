#!/usr/bin/env node

const esbuild = require('esbuild');
const fs = require('fs');
const path = require('path');

// Check if we're building for production
// (Set via passing `production` as first argument)
const isProd = process.argv[2] === 'production';

// Gather our input files
const jsInDir = path.join(__dirname, '../../resources/js');
const jsInDirFiles = fs.readdirSync(jsInDir, 'utf8');
const entryFiles = jsInDirFiles
    .filter(f => f.endsWith('.js') || f.endsWith('.mjs'))
    .map(f => path.join(jsInDir, f));

// Locate our output directory
const outDir = path.join(__dirname, '../../public/dist');

// Build via esbuild
esbuild.build({
    bundle: true,
    entryPoints: entryFiles,
    outdir: outDir,
    sourcemap: true,
    target: 'es2020',
    mainFields: ['module', 'main'],
    format: 'esm',
    minify: isProd,
    logLevel: "info",
}).catch(() => process.exit(1));