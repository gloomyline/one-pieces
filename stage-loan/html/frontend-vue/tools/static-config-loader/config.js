/*
* @Author: AlanWang
* @Date:   2018-01-03 15:21:57
 * @Last modified by:
 * @Last modified time: 2018-01-29T18:05:03+08:00
*/
const path = require('path')
const proConfig = require('../../config')
const env = process.env.NODE_ENV === 'development' ? 'dev' : 'build'

module.exports = {
  apiUrl: `${proConfig[env].apiConfig.urlPrefix}get-static-config`,
  types: [
    'limuHouseFundAreas',
    'limuSocialSecurityAreas',
    'shopAreas',
    'shops',
    'category'
  ],
  output: {
    path: path.resolve(__dirname, '../../src/assets/static-configs')
  }
}
