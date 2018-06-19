/*
* @Author: AlanWang
* @Date:   2017-10-09 09:15:34
* @Last Modified by:   AlanWang
* @Last Modified time: 2017-10-31 14:09:28
*/

/**
 * store entry file
 */

import Vue from 'vue'
import Vuex from 'vuex'

import * as getters from './getters'
import * as actions from './actions'

// import modules here
import loading from './modules/loading'
import forgetLoginPwd from './modules/forgetLoginPwd'
import authenticationCenter from './modules/AuthenticationCenter'
import personalCenter from './modules/PersonalCenter'
import home from './modules/Home'

// import tools logger as plugin
import logger from './plugins/logger'
// import tools http interceptors as plugin
import httpInterceptor from './plugins/httpInterceptor'

Vue.use(Vuex)

// turn the debug in the development environment
const debug = process.env.NODE_ENV !== 'production'

const store = new Vuex.Store({
  getters,
  actions,
  modules: {
    loading,
    forgetLoginPwd,
    authenticationCenter,
    personalCenter,
    home
  },
  strict: debug,
  plugins: debug ? [logger, httpInterceptor] : [httpInterceptor]
})

export default store
