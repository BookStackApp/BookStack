const path = require('path');
const dev = process.env.NODE_ENV !== 'production';

const config = {
    target: 'web',
    mode: dev? 'development' : 'production',
    entry: {
        app: './resources/js/index.js',
    },
    output: {
        filename: '[name].js',
        path: path.resolve(__dirname, 'public/dist')
    },
};

if (dev) {
    config['devtool'] = 'inline-source-map';
}

module.exports = config;