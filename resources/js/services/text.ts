/**
 * Convert a kebab-case string to camelCase
 */
export function kebabToCamel(kebab: string): string {
    const ucFirst = (word: string) => word.slice(0, 1).toUpperCase() + word.slice(1);
    const words = kebab.split('-');
    return words[0] + words.slice(1).map(ucFirst).join('');
}

/**
 * Convert a camelCase string to a kebab-case string.
 */
export function camelToKebab(camelStr: string): string {
    return camelStr.replace(/[A-Z]/g, (str, offset) => (offset > 0 ? '-' : '') + str.toLowerCase());
}
