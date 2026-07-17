// This is a basic transformer stub to help jest handle SVG files.
// Essentially blanks them since we don't really need to involve them
// in our tests (yet).
module.exports = {
    process() {
        return {
            code: 'module.exports = \'\';',
        };
    },
    getCacheKey() {
        // The output is always the same.
        return 'svgTransform';
    },
};
