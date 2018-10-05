const path = require('path')
const BrowserSyncWebpackPlugin = require('browser-sync-webpack-plugin')

// Plugin setup
const BrowserSyncPlugin = new BrowserSyncWebpackPlugin(
    require('./browser-sync.js'),
    { reload: false }
)

module.exports = {
    entry: { main: './src/index.js' },
    output: {
        path: path.resolve('./dist/'),
        filename: '[name].js'
    },
    devtool: 'inline-source-map',
    devServer: {
        openPage: ''
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: {
                    loader: 'babel-loader'
                }
            },
            {
                test: /\.(scss|css)$/,
                exclude: /node_modules/,
                use: [
                    {
                        loader: 'style-loader'
                    },
                    {
                        loader: 'css-loader',
                        options: {
                            modules: true,
                            importLoaders: 1,
                            localIdentName: '[name]_[local]_[hash:base64]',
                            sourceMap: true,
                            minimize: true
                        }
                    },
                    {
                        loader: 'scss-loader',
                        options: {
                            modules: true,
                            importLoaders: 1,
                            localIdentName: '[name]_[local]_[hash:base64]',
                            sourceMap: true,
                            minimize: true
                        }
                    }
                ]
            }
        ]
    },
    plugins: [BrowserSyncPlugin]
}
