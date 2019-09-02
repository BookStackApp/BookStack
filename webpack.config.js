const path = require('path');
const dev = process.env.NODE_ENV !== 'production';

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

module.exports = config;