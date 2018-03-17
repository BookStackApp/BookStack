const path = require('path');
const dev = process.env.NODE_ENV !== 'production';

const UglifyJsPlugin = require('uglifyjs-webpack-plugin');

const config = {
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
                        presets: ['@babel/preset-env']
                    }
                }
            },
            {
                test: /\.scss$/,
                use: [{
                    loader: 'file-loader',
                    options: {
                        name: '[name].css',
                        context: './src/css/',
                        outputPath: './',
                        publicPath: 'public/'
                    }
                }, {
                    loader: 'extract-loader', options: {
                        publicPath: '',
                    }
                }, {
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
                }]
            }
        ]
    },
    plugins: []
};

if (dev) {
    config['devtool'] = 'inline-source-map';
}

if (!dev) {
    config.plugins.push(new UglifyJsPlugin());
}

module.exports = config;