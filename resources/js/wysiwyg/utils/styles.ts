
export type StyleMap = Map<string, string>;

export function createStyleMapFromDomStyles(domStyles: CSSStyleDeclaration): StyleMap {
    const styleMap: StyleMap = new Map();
    const styleNames: string[] = Array.from(domStyles);
    for (const style of styleNames) {
        styleMap.set(style, domStyles.getPropertyValue(style));
    }
    return styleMap;
}