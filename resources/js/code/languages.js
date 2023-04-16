import {StreamLanguage} from "@codemirror/language"

import {css} from '@codemirror/lang-css';
import {json} from '@codemirror/lang-json';
import {javascript} from '@codemirror/lang-javascript';
import {html} from "@codemirror/lang-html";
import {markdown} from '@codemirror/lang-markdown';
import {php} from '@codemirror/lang-php';
import {twig} from "@ssddanbrown/codemirror-lang-twig";
import {xml} from "@codemirror/lang-xml";

const legacyLoad = async (mode) => {
    const modes = await window.importVersioned('legacy-modes');
    return StreamLanguage.define(modes[mode]);
};


// Mapping of possible languages or formats from user input to their codemirror modes.
// Value can be a mode string or a function that will receive the code content & return the mode string.
// The function option is used in the event the exact mode could be dynamic depending on the code.
const modeMap = {
    bash: () => legacyLoad('shell'),
    c: () => legacyLoad('c'),
    css: async () => css(),
    'c++': () => legacyLoad('cpp'),
    'c#': () => legacyLoad('csharp'),
    csharp: () => legacyLoad('csharp'),
    dart: () => legacyLoad('dart'),
    diff: () => legacyLoad('diff'),
    for: () => legacyLoad('fortran'),
    fortran: () => legacyLoad('fortran'),
    'f#': () => legacyLoad('fSharp'),
    fsharp: () => legacyLoad('fSharp'),
    go: () => legacyLoad('go'),
    haskell: () => legacyLoad('haskell'),
    hs: () => legacyLoad('haskell'),
    html: async () => html(),
    ini: () => legacyLoad('properties'),
    java: () => legacyLoad('java'),
    javascript: async () => javascript(),
    json: async () => json(),
    js: async () => javascript(),
    jl: () => legacyLoad('julia'),
    julia: () => legacyLoad('julia'),
    kotlin: () => legacyLoad('kotlin'),
    latex: () => legacyLoad('stex'),
    lua: () => legacyLoad('lua'),
    markdown: async () => markdown(),
    matlab: () => legacyLoad('octave'),
    md: async () => markdown(),
    mdown: async () => markdown(),
    ml: () => legacyLoad('sml'),
    mssql: () => legacyLoad('msSQL'),
    mysql: () => legacyLoad('mySQL'),
    nginx: () => legacyLoad('nginx'),
    octave: () => legacyLoad('octave'),
    pas: () => legacyLoad('pascal'),
    pascal: () => legacyLoad('pascal'),
    perl: () => legacyLoad('perl'),
    pgsql: () => legacyLoad('pgSQL'),
    php: async (code) => {
        const hasTags = code.includes('<?php');
        return php({plain: !hasTags});
    },
    pl: () => legacyLoad('perl'),
    'pl/sql': () => legacyLoad('plSQL'),
    postgresql: () => legacyLoad('pgSQL'),
    powershell: () => legacyLoad('powerShell'),
    properties: () => legacyLoad('properties'),
    ocaml: () => legacyLoad('oCaml'),
    py: () => legacyLoad('python'),
    python: () => legacyLoad('python'),
    rb: () => legacyLoad('ruby'),
    rs: () => legacyLoad('rust'),
    ruby: () => legacyLoad('ruby'),
    rust: () => legacyLoad('rust'),
    scala: () => legacyLoad('scala'),
    scheme: () => legacyLoad('scheme'),
    shell: () => legacyLoad('shell'),
    sh: () => legacyLoad('shell'),
    smarty: () => legacyLoad('smarty'),
    stext: () => legacyLoad('stex'),
    swift: () => legacyLoad('swift'),
    toml: () => legacyLoad('toml'),
    ts: async () => javascript({typescript: true}),
    twig: async () => twig(),
    typescript: async () => javascript({typescript: true}),
    sql: () => legacyLoad('standardSQL'),
    sqlite: () => legacyLoad('sqlite'),
    vbs: () => legacyLoad('vbScript'),
    vbscript: () => legacyLoad('vbScript'),
    'vb.net': () => legacyLoad('vb'),
    vbnet: () => legacyLoad('vb'),
    xml: async () => xml(),
    yaml: () => legacyLoad('yaml'),
    yml: () => legacyLoad('yaml'),
};

/**
 * Get the relevant codemirror language extension based upon the given language
 * suggestion and content.
 * @param {String} langSuggestion
 * @param {String} content
 * @returns {Promise<StreamLanguage|LanguageSupport>}
 */
export function getLanguageExtension(langSuggestion, content) {
    const suggestion = langSuggestion.trim().replace(/^\./g, '').toLowerCase();

    const language = modeMap[suggestion];

    if (typeof language === 'undefined') {
        return undefined;
    }

    return language(content);
}