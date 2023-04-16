import {tags} from "@lezer/highlight";
import {HighlightStyle, syntaxHighlighting} from "@codemirror/language";
import {EditorView} from "@codemirror/view";
import {oneDarkHighlightStyle, oneDarkTheme} from "@codemirror/theme-one-dark";

const defaultLightHighlightStyle = HighlightStyle.define([
    { tag: tags.meta,
        color: "#388938" },
    { tag: tags.link,
        textDecoration: "underline" },
    { tag: tags.heading,
        textDecoration: "underline",
        fontWeight: "bold" },
    { tag: tags.emphasis,
        fontStyle: "italic" },
    { tag: tags.strong,
        fontWeight: "bold" },
    { tag: tags.strikethrough,
        textDecoration: "line-through" },
    { tag: tags.keyword,
        color: "#708" },
    { tag: [tags.atom, tags.bool, tags.url, tags.contentSeparator, tags.labelName],
        color: "#219" },
    { tag: [tags.literal, tags.inserted],
        color: "#164" },
    { tag: [tags.string, tags.deleted],
        color: "#a11" },
    { tag: [tags.regexp, tags.escape, tags.special(tags.string)],
        color: "#e40" },
    { tag: tags.definition(tags.variableName),
        color: "#00f" },
    { tag: tags.local(tags.variableName),
        color: "#30a" },
    { tag: [tags.typeName, tags.namespace],
        color: "#085" },
    { tag: tags.className,
        color: "#167" },
    { tag: [tags.special(tags.variableName), tags.macroName],
        color: "#256" },
    { tag: tags.definition(tags.propertyName),
        color: "#00c" },
    { tag: tags.compareOperator,
        color: "#708" },
    { tag: tags.comment,
        color: "#940" },
    { tag: tags.invalid,
        color: "#f00" }
]);

const defaultThemeSpec = {
    "&": {
        color: "#000",
    },
    "&.cm-focused": {
        outline: "none",
    },
    ".cm-line": {
        lineHeight: "1.6",
    },
};

/**
 * Get the theme extension to use for editor view instance.
 * @returns {Extension[]}
 */
export function getTheme(viewParentEl) {
    const darkMode = document.documentElement.classList.contains('dark-mode');
    let viewTheme = darkMode ? oneDarkTheme : EditorView.theme(defaultThemeSpec);
    let highlightStyle = darkMode ? oneDarkHighlightStyle : defaultLightHighlightStyle;

    const eventData = {
        darkModeActive: darkMode,
        registerViewTheme(builder) {
            const spec = builder();
            if (spec) {
                viewTheme = EditorView.theme(spec);
            }
        },
        registerHighlightStyle(builder) {
            const tagStyles = builder(tags) || [];
            if (tagStyles.length) {
                highlightStyle = HighlightStyle.define(tagStyles);
            }
        }
    };

    window.$events.emitPublic(viewParentEl, 'library-cm6::configure-theme', eventData);

    return [viewTheme, syntaxHighlighting(highlightStyle)];
}