/*
* @Author: AlanWang
* @Date:   2017-10-09 10:45:03
* @Last Modified by:   AlanWang
* @Last Modified time: 2017-11-23 10:47:31
*/

import Vue from 'vue'
import VueResource from 'vue-resource'

Vue.use(VueResource)

function createHttp () {
  // The list of requests which are not required to intercept
  const httpInterceptorBlackList = [
    'lm-mobile',
    'lm-mobile-input',
    'lm-mobile-continue',
    'lm-jd-input',
    'lm-jd-continue',
    'lm-bill-continue',
    'loan-record', // the records of loan
    'repayments-record' // the records of repayment
  ]
  // helper function,
  // to do that checkout the url of request
  // is existed in the black list or not.
  function checkIsRequestInBlack (request) {
    let arr = request.url.split('/')
    let path = arr[arr.length - 1]
    let index = httpInterceptorBlackList.indexOf(path)
    return index
  }

  return store => {
    Vue.http.interceptors.push((request, next) => {
      if (checkIsRequestInBlack(request) !== -1) { // if not existed, do not intercept, just go on sending request
        next()
      } else {
        // markup starting to request
        store.commit('SEND_HTTP_REQUEST')
        next(response => {
          // markup ending to request
          response.ok ? store.commit('RESPONSE_SUCCESS') : store.commit('RESPONSE_FAIL')
        })
      }
    })
  }
}

let httpInterceptor = createHttp()

export default httpInterceptor
