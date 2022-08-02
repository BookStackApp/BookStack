import {StreamLanguage} from "@codemirror/language"

import {css as langCss} from '@codemirror/legacy-modes/mode/css';
import {clike as langClike} from '@codemirror/legacy-modes/mode/clike';
import {diff as langDiff} from '@codemirror/legacy-modes/mode/diff';
import {fortran as langFortran} from '@codemirror/legacy-modes/mode/fortran';
import {go as langGo} from '@codemirror/legacy-modes/mode/go';
import {haskell as langHaskell} from '@codemirror/legacy-modes/mode/haskell';
// import {htmlmixed as langHtmlmixed} from '@codemirror/legacy-modes/mode/htmlmixed';
import {javascript as langJavascript} from '@codemirror/legacy-modes/mode/javascript';
import {julia as langJulia} from '@codemirror/legacy-modes/mode/julia';
import {lua as langLua} from '@codemirror/legacy-modes/mode/lua';
// import {markdown as langMarkdown} from '@codemirror/legacy-modes/mode/markdown';
import {oCaml as langMllike} from '@codemirror/legacy-modes/mode/mllike';
import {nginx as langNginx} from '@codemirror/legacy-modes/mode/nginx';
import {perl as langPerl} from '@codemirror/legacy-modes/mode/perl';
import {pascal as langPascal} from '@codemirror/legacy-modes/mode/pascal';
// import {php as langPhp} from '@codemirror/legacy-modes/mode/php';
import {powerShell as langPowershell} from '@codemirror/legacy-modes/mode/powershell';
import {properties as langProperties} from '@codemirror/legacy-modes/mode/properties';
import {python as langPython} from '@codemirror/legacy-modes/mode/python';
import {ruby as langRuby} from '@codemirror/legacy-modes/mode/ruby';
import {rust as langRust} from '@codemirror/legacy-modes/mode/rust';
import {shell as langShell} from '@codemirror/legacy-modes/mode/shell';
import {sql as langSql} from '@codemirror/legacy-modes/mode/sql';
import {stex as langStex} from '@codemirror/legacy-modes/mode/stex';
import {toml as langToml} from '@codemirror/legacy-modes/mode/toml';
import {vb as langVb} from '@codemirror/legacy-modes/mode/vb';
import {vbScript as langVbscript} from '@codemirror/legacy-modes/mode/vbscript';
import {xml as langXml} from '@codemirror/legacy-modes/mode/xml';
import {yaml as langYaml} from '@codemirror/legacy-modes/mode/yaml';

export const modes = [
    langCss,
    langClike,
    langDiff,
    langFortran,
    langGo,
    langHaskell,
    // langHtmlmixed,
    langJavascript,
    langJulia,
    langLua,
    // langMarkdown,
    langMllike,
    langNginx,
    langPerl,
    langPascal,
    // langPhp,
    langPowershell,
    langProperties,
    langPython,
    langRuby,
    langRust,
    langShell,
    langSql,
    langStex,
    langToml,
    langVb,
    langVbscript,
    langXml,
    langYaml,
];

// Mapping of possible languages or formats from user input to their codemirror modes.
// Value can be a mode string or a function that will receive the code content & return the mode string.
// The function option is used in the event the exact mode could be dynamic depending on the code.
export const modeMap = {
    bash: 'shell',
    css: 'css',
    c: 'text/x-csrc',
    java: 'text/x-java',
    scala: 'text/x-scala',
    kotlin: 'text/x-kotlin',
    'c++': 'text/x-c++src',
    'c#': 'text/x-csharp',
    csharp: 'text/x-csharp',
    diff: 'diff',
    for: 'fortran',
    fortran: 'fortran',
    'f#': 'text/x-fsharp',
    fsharp: 'text/x-fsharp',
    go: 'go',
    haskell: 'haskell',
    hs: 'haskell',
    html: 'htmlmixed',
    ini: 'properties',
    javascript: 'text/javascript',
    json: 'application/json',
    js: 'text/javascript',
    jl: 'text/x-julia',
    julia: 'text/x-julia',
    latex: 'text/x-stex',
    lua: 'lua',
    md: 'markdown',
    mdown: 'markdown',
    markdown: 'markdown',
    ml: 'mllike',
    nginx: 'nginx',
    perl: 'perl',
    pl: 'perl',
    powershell: 'powershell',
    properties: 'properties',
    ocaml: 'text/x-ocaml',
    pascal: 'text/x-pascal',
    pas: 'text/x-pascal',
    php: (content) => {
        return content.includes('<?php') ? 'php' : 'text/x-php';
    },
    py: 'python',
    python: 'python',
    ruby: 'ruby',
    rust: 'rust',
    rb: 'ruby',
    rs: 'rust',
    shell: 'shell',
    sh: 'shell',
    stext: 'text/x-stex',
    toml: 'toml',
    ts: 'text/typescript',
    typescript: 'text/typescript',
    sql: 'text/x-sql',
    vbs: 'vbscript',
    vbscript: 'vbscript',
    'vb.net': 'text/x-vb',
    vbnet: 'text/x-vb',
    xml: 'xml',
    yaml: 'yaml',
    yml: 'yaml',
};

export function modesAsStreamLanguages() {
    return modes.map(mode => StreamLanguage.define(mode));
}