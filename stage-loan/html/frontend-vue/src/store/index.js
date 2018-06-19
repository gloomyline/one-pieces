/*
* @Author: AlanWang
* @Date:   2017-10-09 09:15:34
 * @Last modified by:
 * @Last modified time: 2017-12-25T11:17:33+08:00
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
import user from './modules/user'
// import forgetLoginPwd from './modules/user/forgetLoginPwd'
import container from './modules/container'

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
    user,
    container,
    // forgetLoginPwd,
    authenticationCenter,
    personalCenter,
    home
  },
  strict: debug,
  plugins: debug ? [logger, httpInterceptor] : [httpInterceptor]
})

export default store
