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
  const httpInterceptorBlackList = [
    'lm-mobile',
    'lm-mobile-input',
    'lm-mobile-continue',
    'lm-jd-input',
    'lm-jd-continue',
    'lm-bill-continue'
  ]
  function checkIsRequestInBlack (request) {
    let arr = request.url.split('/')
    let path = arr[arr.length - 1]
    let index = httpInterceptorBlackList.indexOf(path)
    return index
  }

  return store => {
    Vue.http.interceptors.push((request, next) => {
      if (checkIsRequestInBlack(request) !== -1) {
        next()
      } else {
        store.commit('SEND_HTTP_REQUEST')
        next(response => {
          response.ok ? store.commit('RESPONSE_SUCCESS') : store.commit('RESPONSE_FAIL')
        })
      }
    })
  }
}

let httpInterceptor = createHttp()

export default httpInterceptor

