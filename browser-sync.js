const path = require('path')
const url = 'local.artsatlas.com.au'
const certPath =
    '/Users/gg3/Library/Application Support/Local by Flywheel/routes/certs'

module.exports = {
    proxy: `https://${url}`,
    https: {
        key: path.resolve(`${certPath}/${url}.key`),
        cert: path.resolve(`${certPath}/${url}.crt`)
    },
    reloadDelay: 0,
    files: ['./templates', './src', './dist', '*.php']
}
