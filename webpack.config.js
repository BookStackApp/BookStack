const path = require('path');
const dev = process.env.NODE_ENV !== 'production';

const UglifyJsPlugin = require('uglifyjs-webpack-plugin');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

const config = {
    target: 'web',
    mode: dev? 'development' : 'production',
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
                        presets: [[
                            '@babel/preset-env', {
                                useBuiltIns: 'usage'
                            }
                        ]]
                    }
                }
            },
            {
                test: /\.scss$/,
                use: [
                    {
                        loader: MiniCssExtractPlugin.loader,
                        options: {}
                    },
                    {
                        loader: "css-loader", options: {
                        sourceMap: dev
                    }
                    }, {
                        loader: 'postcss-loader',
                        options: {
                            ident: 'postcss',
                            sourceMap: dev,
                            plugins: (loader) => [
                                require('autoprefixer')(),
                            ]
                        }
                    }, {
                        loader: "sass-loader", options: {
                            sourceMap: dev
                        }
                    }
                ]
            }
        ]
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: "[name].css",
        }),
    ]
};

if (dev) {
    config['devtool'] = 'inline-source-map';
}

if (!dev) {
    config.plugins.push(new UglifyJsPlugin());
}

module.exports = config;