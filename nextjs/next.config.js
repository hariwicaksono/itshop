// next.config.js

const path = require('path')
//const isDev = process.env.NODE_ENV === 'development'
const isProd = process.env.NODE_ENV === 'production'
const basePath = '';

module.exports = {
    basePath: isProd ? basePath : '',
    env: {
      BASE_PATH: isProd ? basePath : '',
    },
    trailingSlash: isProd ? true : false,
    images: {
      domains: ['localhost'],
    },
    //assetPrefix: isProd ? 'https://cdn.mydomain.com' : '',
    //assetPrefix: isProd ? 'http://localhost/companyprofileci4-nextjs/out' : '',
}

module.exports = {
  sassOptions: {
    includePaths: [path.join(__dirname, 'styles')],
  },
}