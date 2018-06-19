/**
 * @Date:   2017-12-27T15:37:57+08:00
 * @Last modified time: 2018-01-30T14:00:59+08:00
 */
const fs = require('fs')
const path = require('path')
const chalk = require('chalk')

const request = require('bluebird').promisify(require('request'))
const config = require('./config')

class Loader {
  constructor (opts) {
    this.opts = opts || {}
    this.url = opts.apiUrl
    this.types = opts.types
    this.outputPath = opts.output.path
    this.init()
  }

  init() {
    // detect static configs folder is existed or not
    fs.access(this.outputPath, err => {
      if (err) {
        fs.mkdirSync(this.outputPath, '0o777')
      }
    })
  }

  run () {
    const requestQueue = this.load()
    requestQueue.forEach(item => {
      item.promise.then(res => {
        this.log(`end loading ${item.type} config`)
        try {
          let parsedData = this.parse(res.body, item.type)
          this.write(item.filename, parsedData)
        } catch (e) {
          console.log(chalk.red(e))
        }
      })
    })
  }

  error (errMsg) {
    console.log(chalk.bold.red(errMsg))
    throw new Error(errMsg)
  }

  log (msg) {
    console.log(chalk.green(msg))
  }

  load () {
    this.log('start to load configs')
    if (!this.types) {
      this.error('Missed types')
      return
    }
    return this.types.map(type => {
      const filename = path.resolve(this.outputPath, `${type}_config.json`)
      return {
        type,
        filename,
        promise: request({
          url: `${this.url}?type=${type}`,
          method: 'GET'
        })
      }
    })
  }

  parse (str, type) { // convert to json string
    /**
     * due to response body is json string,
     * so parse to json object before geting the results data
     * @type {[type]}
     */
    let results = JSON.parse(str).results
    // format the fileds of these data from { area_code: '', area_name: '' } to
    // { id: '', name: '' }
    if (type === 'limuHouseFundAreas' || type === 'limuSocialSecurityAreas') {
      results = results.map(item => {
        return {
          value: String(item.area_code), // ensure the type of the val is String
          name: item.area_name
        }
      })
    }
    // link: https://www.jianshu.com/p/jtzqxp
    // format json object to nice json string
    return JSON.stringify(results, null, '\t')
  }

  write (filename, str) {
    fs.writeFile(filename, str, err => {
      if (err) {
        this.error(`write ${filename} failed!`)
      }
      this.log(`${filename} has been updated`)
    })
  }
}

module.exports = new Loader(config)
