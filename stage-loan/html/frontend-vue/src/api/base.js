/*
* @Author: AlanWang
* @Date:   2017-09-12 10:10:37
 * @Last modified by:
 * @Last modified time: 2018-01-11T17:33:50+08:00
*/

/*
 * 通用api请求
 */

import Vue from 'vue'

import baseConfig from '../../config/index'
let env = process.env.NODE_ENV === 'development' ? 'dev' : 'build'
let config = baseConfig[env].apiConfig

// tools function logger
function log () {
  if (!config.logger) return
  if (arguments[0] === 1) {
    Function.call.apply(console.error, arguments)
  } else {
    Function.call.apply(console.log, arguments)
  }
}

/**
 * popup errMsg from server
 * @param  {[String]} errMsg  [errMsg dispatched by backend]
 * @param  {[Number]} delay   [delay to close the popup, unit is 's']
 * @return
 */
function showErrAlert (errMsg, delay) {
  if (timer) clearTimeout(timer)
  Vue.$vux.alert.show({
    content: errMsg,
    onHide: () => {
      if (timer) clearTimeout(timer)
    }
  })
  let timer = setTimeout(() => {
    Vue.$vux.alert.hide()
    if (timer) clearTimeout(timer)
  }, delay * 1000)
}

export function reqMByPost (apiPath, postData, options) {
  postData = postData || {}
  options = options || {}
  return new Promise((resolve, reject) => {
    Vue.http.post(`${config.urlPrefix}${apiPath}`, postData, options).then(res => {
      res = res.data
      const pathBlackList = [
        'lm-mobile-continue',
        'lm-mobile',
        'lm-jd',
        'lm-jd-continue',
        'lm-taobao',
        'lm-taobao-continue'
      ]
      if (pathBlackList.indexOf(apiPath) === -1) {
        if (res.status === 'SUCCESS') {
          resolve(res)
        } else {
          showErrAlert(res.error_message, 3)
        }
      } else {
        resolve(res)
      }
    }, err => {
      log(1, err)
    })
  })
}

export function reqMByPut (apiPath, putData, options) {
  putData = putData || {}
  options = options || {}
  return new Promise((resolve, reject) => {
    Vue.http.put(`${config.urlPrefix}${apiPath}`, putData, options).then(res => {
      res = res.data
      if (res.status === 'SUCCESS') {
        resolve(res)
      } else {
        showErrAlert(res.error_message, 3)
      }
    }, err => {
      log(1, err)
    })
  })
}

export function reqMByGet (apiPath, options) {
  options = options || {}
  return new Promise((resolve, reject) => {
    Vue.http.get(`${config.urlPrefix}${apiPath}`, options).then(res => {
      res = res.data
      const pathBlackList = [
        'shop-base'
      ]
      if (pathBlackList.indexOf(apiPath) === -1) {
        if (res.status === 'SUCCESS') {
          resolve(res)
        } else {
          showErrAlert(res.error_message, 3)
        }
      } else {
        resolve(res)
      }
    }, err => {
      log(1, err)
    })
  })
}

export function reqAByPost (apiPath, postData) {
  postData = postData || {}
  return new Promise((resolve, reject) => {
    Vue.http.post(`${config.apiUrlPrefix}${apiPath}`, postData).then(res => {
      res = res.data
      if (res.status === 'SUCCESS') {
        resolve(res)
      } else {
        showErrAlert(res.error_message, 3)
      }
    }, err => {
      log(1, err)
    })
  })
}

/**
 * 请求第三方接口地址(POST)
 * @param  {[type]} url      接口地址
 * @param  {Object} postData 请求参数
 * @return {[type]}          response
 */
export function reqTByPost (url, postData) {
  postData = postData || {}
  return new Promise((resolve, reject) => {
    Vue.http.post(url, postData).then(res => {
      res = res.data
      resolve(res)
    }, err => {
      // log(1, err)
      reject(err)
    })
  })
}
