import {StreamLanguage} from "@codemirror/language"

import {css} from '@codemirror/legacy-modes/mode/css';
import {c, java, cpp, csharp, kotlin, scala} from '@codemirror/legacy-modes/mode/clike';
import {diff} from '@codemirror/legacy-modes/mode/diff';
import {fortran} from '@codemirror/legacy-modes/mode/fortran';
import {go} from '@codemirror/legacy-modes/mode/go';
import {haskell} from '@codemirror/legacy-modes/mode/haskell';
import {html} from '@codemirror/lang-html';
import {javascript} from '@codemirror/lang-javascript';
import {json} from '@codemirror/lang-json';
import {julia} from '@codemirror/legacy-modes/mode/julia';
import {lua} from '@codemirror/legacy-modes/mode/lua';
import {markdown} from '@codemirror/lang-markdown';
import {oCaml, fSharp, sml} from '@codemirror/legacy-modes/mode/mllike';
import {nginx} from '@codemirror/legacy-modes/mode/nginx';
import {perl} from '@codemirror/legacy-modes/mode/perl';
import {pascal} from '@codemirror/legacy-modes/mode/pascal';
import {php} from '@codemirror/lang-php';
import {powerShell} from '@codemirror/legacy-modes/mode/powershell';
import {properties} from '@codemirror/legacy-modes/mode/properties';
import {python} from '@codemirror/legacy-modes/mode/python';
import {ruby} from '@codemirror/legacy-modes/mode/ruby';
import {rust} from '@codemirror/legacy-modes/mode/rust';
import {shell} from '@codemirror/legacy-modes/mode/shell';
import {sql} from '@codemirror/legacy-modes/mode/sql';
import {stex} from '@codemirror/legacy-modes/mode/stex';
import {toml} from '@codemirror/legacy-modes/mode/toml';
import {vb} from '@codemirror/legacy-modes/mode/vb';
import {vbScript} from '@codemirror/legacy-modes/mode/vbscript';
import {xml} from '@codemirror/legacy-modes/mode/xml';
import {yaml} from '@codemirror/legacy-modes/mode/yaml';


// Mapping of possible languages or formats from user input to their codemirror modes.
// Value can be a mode string or a function that will receive the code content & return the mode string.
// The function option is used in the event the exact mode could be dynamic depending on the code.
const modeMap = {
    bash: () => StreamLanguage.define(shell),
    css: () => StreamLanguage.define(css),
    c: () => StreamLanguage.define(c),
    java: () => StreamLanguage.define(java),
    scala: () => StreamLanguage.define(scala),
    kotlin: () => StreamLanguage.define(kotlin),
    'c++': () => StreamLanguage.define(cpp),
    'c#': () => StreamLanguage.define(csharp),
    csharp: () => StreamLanguage.define(csharp),
    diff: () => StreamLanguage.define(diff),
    for: () => StreamLanguage.define(fortran),
    fortran: () => StreamLanguage.define(fortran),
    'f#': () => StreamLanguage.define(fSharp),
    fsharp: () => StreamLanguage.define(fSharp),
    go: () => StreamLanguage.define(go),
    haskell: () => StreamLanguage.define(haskell),
    hs: () => StreamLanguage.define(haskell),
    html: () => html(),
    ini: () => StreamLanguage.define(properties),
    javascript: () => javascript(),
    json: () => json(),
    js: () => javascript(),
    jl: () => StreamLanguage.define(julia),
    julia: () => StreamLanguage.define(julia),
    latex: () => StreamLanguage.define(stex),
    lua: () => StreamLanguage.define(lua),
    md: () => StreamLanguage.define(markdown),
    mdown: () => StreamLanguage.define(markdown),
    markdown: () => StreamLanguage.define(markdown),
    ml: () => StreamLanguage.define(sml),
    nginx: () => StreamLanguage.define(nginx),
    perl: () => StreamLanguage.define(perl),
    pl: () => StreamLanguage.define(perl),
    powershell: () => StreamLanguage.define(powerShell),
    properties: () => StreamLanguage.define(properties),
    ocaml: () => StreamLanguage.define(oCaml),
    pascal: () => StreamLanguage.define(pascal),
    pas: () => StreamLanguage.define(pascal),
    php: (code) => {
        const hasTags = code.includes('<?php');
        return php({plain: !hasTags});
    },
    py: () => StreamLanguage.define(python),
    python: () => StreamLanguage.define(python),
    ruby: () => StreamLanguage.define(ruby),
    rust: () => StreamLanguage.define(rust),
    rb: () => StreamLanguage.define(ruby),
    rs: () => StreamLanguage.define(rust),
    shell: () => StreamLanguage.define(shell),
    sh: () => StreamLanguage.define(shell),
    stext: () => StreamLanguage.define(stex),
    toml: () => StreamLanguage.define(toml),
    ts: () => javascript({typescript: true}),
    typescript: () => javascript({typescript: true}),
    sql: () => StreamLanguage.define(sql),
    vbs: () => StreamLanguage.define(vbScript),
    vbscript: () => StreamLanguage.define(vbScript),
    'vb.net': () => StreamLanguage.define(vb),
    vbnet: () => StreamLanguage.define(vb),
    xml: () => StreamLanguage.define(xml),
    yaml: () => StreamLanguage.define(yaml),
    yml: () => StreamLanguage.define(yaml),
};

/**
 * Get the relevant codemirror language extension based upon the given language
 * suggestion and content.
 * @param {String} langSuggestion
 * @param {String} content
 * @returns {StreamLanguage}
 */
export function getLanguageExtension(langSuggestion, content) {
    const suggestion = langSuggestion.trim().replace(/^\./g, '').toLowerCase();

    const language = modeMap[suggestion];

    if (typeof language === 'undefined') {
        return undefined;
    }

    return language(content);
}