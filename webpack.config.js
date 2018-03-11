const path = require('path');
const dev = process.env.NODE_ENV !== 'production';

const UglifyJsPlugin = require('uglifyjs-webpack-plugin');
const ExtractTextPlugin = require("extract-text-webpack-plugin");

const extractSass = new ExtractTextPlugin({
    filename: "[name].css"
    // disable: process.env.NODE_ENV === "development"
});

const config = {
    entry: {
        app: './resources/assets/js/index.js',
        styles: './resources/assets/sass/styles.scss',
        "export-styles": './resources/assets/sass/export-styles.scss',
        "print-styles": './resources/assets/sass/print-styles.scss',
    },
    output: {
        filename: '[name].js',
        path: path.resolve(__dirname, 'public/dist')
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /(node_modules)/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: ['@babel/preset-env']
                    }
                }
            },
            {
                test: /\.scss$/,
                use: extractSass.extract({
                    use: [{
                        loader: "css-loader", options: {
                            sourceMap: dev
                        }
                    }, {
                        loader: "sass-loader", options: {
                            sourceMap: dev
                        }
                    }],
                    // use style-loader in development
                    fallback: "style-loader"
                })
            }
        ]
    },
    plugins: [extractSass]
};

if (dev) {
    config['devtool'] = 'inline-source-map';
}

if (!dev) {
    config.plugins.push(new UglifyJsPlugin());
}

module.exports = config;