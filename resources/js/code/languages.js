import {StreamLanguage} from "@codemirror/language"

import {css} from '@codemirror/legacy-modes/mode/css';
import {c, cpp, csharp, java, kotlin, scala, dart} from '@codemirror/legacy-modes/mode/clike';
import {diff} from '@codemirror/legacy-modes/mode/diff';
import {fortran} from '@codemirror/legacy-modes/mode/fortran';
import {go} from '@codemirror/legacy-modes/mode/go';
import {haskell} from '@codemirror/legacy-modes/mode/haskell';
import {javascript, json, typescript} from '@codemirror/legacy-modes/mode/javascript';
import {julia} from '@codemirror/legacy-modes/mode/julia';
import {lua} from '@codemirror/legacy-modes/mode/lua';
import {markdown} from '@codemirror/lang-markdown';
import {oCaml, fSharp, sml} from '@codemirror/legacy-modes/mode/mllike';
import {nginx} from '@codemirror/legacy-modes/mode/nginx';
import {octave} from '@codemirror/legacy-modes/mode/octave';
import {perl} from '@codemirror/legacy-modes/mode/perl';
import {pascal} from '@codemirror/legacy-modes/mode/pascal';
import {php} from '@codemirror/lang-php';
import {powerShell} from '@codemirror/legacy-modes/mode/powershell';
import {properties} from '@codemirror/legacy-modes/mode/properties';
import {python} from '@codemirror/legacy-modes/mode/python';
import {ruby} from '@codemirror/legacy-modes/mode/ruby';
import {rust} from '@codemirror/legacy-modes/mode/rust';
import {scheme} from '@codemirror/legacy-modes/mode/scheme';
import {shell} from '@codemirror/legacy-modes/mode/shell';
import {smarty} from "@ssddanbrown/codemirror-lang-smarty";
import {standardSQL, pgSQL, msSQL, mySQL, sqlite, plSQL} from '@codemirror/legacy-modes/mode/sql';
import {stex} from '@codemirror/legacy-modes/mode/stex';
import {swift} from "@codemirror/legacy-modes/mode/swift";
import {toml} from '@codemirror/legacy-modes/mode/toml';
import {twig} from "@ssddanbrown/codemirror-lang-twig";
import {vb} from '@codemirror/legacy-modes/mode/vb';
import {vbScript} from '@codemirror/legacy-modes/mode/vbscript';
import {xml, html} from '@codemirror/legacy-modes/mode/xml';
import {yaml} from '@codemirror/legacy-modes/mode/yaml';


// Mapping of possible languages or formats from user input to their codemirror modes.
// Value can be a mode string or a function that will receive the code content & return the mode string.
// The function option is used in the event the exact mode could be dynamic depending on the code.
const modeMap = {
    bash: () => StreamLanguage.define(shell),
    c: () => StreamLanguage.define(c),
    css: () => StreamLanguage.define(css),
    'c++': () => StreamLanguage.define(cpp),
    'c#': () => StreamLanguage.define(csharp),
    csharp: () => StreamLanguage.define(csharp),
    dart: () => StreamLanguage.define(dart),
    diff: () => StreamLanguage.define(diff),
    for: () => StreamLanguage.define(fortran),
    fortran: () => StreamLanguage.define(fortran),
    'f#': () => StreamLanguage.define(fSharp),
    fsharp: () => StreamLanguage.define(fSharp),
    go: () => StreamLanguage.define(go),
    haskell: () => StreamLanguage.define(haskell),
    hs: () => StreamLanguage.define(haskell),
    html: () => StreamLanguage.define(html),
    ini: () => StreamLanguage.define(properties),
    java: () => StreamLanguage.define(java),
    javascript: () => StreamLanguage.define(javascript),
    json: () => StreamLanguage.define(json),
    js: () => StreamLanguage.define(javascript),
    jl: () => StreamLanguage.define(julia),
    julia: () => StreamLanguage.define(julia),
    kotlin: () => StreamLanguage.define(kotlin),
    latex: () => StreamLanguage.define(stex),
    lua: () => StreamLanguage.define(lua),
    markdown: () => markdown(),
    matlab: () => StreamLanguage.define(octave),
    md: () => markdown(),
    mdown: () => markdown(),
    ml: () => StreamLanguage.define(sml),
    mssql: () => StreamLanguage.define(msSQL),
    mysql: () => StreamLanguage.define(mySQL),
    nginx: () => StreamLanguage.define(nginx),
    octave: () => StreamLanguage.define(octave),
    pas: () => StreamLanguage.define(pascal),
    pascal: () => StreamLanguage.define(pascal),
    perl: () => StreamLanguage.define(perl),
    pgsql: () => StreamLanguage.define(pgSQL),
    php: (code) => {
        const hasTags = code.includes('<?php');
        return php({plain: !hasTags});
    },
    pl: () => StreamLanguage.define(perl),
    'pl/sql': () => StreamLanguage.define(plSQL),
    postgresql: () => StreamLanguage.define(pgSQL),
    powershell: () => StreamLanguage.define(powerShell),
    properties: () => StreamLanguage.define(properties),
    ocaml: () => StreamLanguage.define(oCaml),
    py: () => StreamLanguage.define(python),
    python: () => StreamLanguage.define(python),
    rb: () => StreamLanguage.define(ruby),
    rs: () => StreamLanguage.define(rust),
    ruby: () => StreamLanguage.define(ruby),
    rust: () => StreamLanguage.define(rust),
    scala: () => StreamLanguage.define(scala),
    scheme: () => StreamLanguage.define(scheme),
    shell: () => StreamLanguage.define(shell),
    sh: () => StreamLanguage.define(shell),
    smarty: () => StreamLanguage.define(smarty),
    stext: () => StreamLanguage.define(stex),
    swift: () => StreamLanguage.define(swift),
    toml: () => StreamLanguage.define(toml),
    ts: () => StreamLanguage.define(typescript),
    twig: () => twig(),
    typescript: () => StreamLanguage.define(typescript),
    sql: () => StreamLanguage.define(standardSQL),
    sqlite: () => StreamLanguage.define(sqlite),
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