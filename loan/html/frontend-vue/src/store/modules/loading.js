/*
* @Author: AlanWang
* @Date:   2017-10-09 10:01:56
* @Last Modified by:   AlanWang
* @Last Modified time: 2017-10-09 11:47:58
*/

/**
 * http request is be going to response, represent the status of loading
 */

import * as types from '../mutation-types'

// state
const state = {
  isLoading: false
}

// getters
const getters = {
  // isLoading: state => state.isLoading
}

// actions
const actions = {
}

// mutations
const mutations = {
  [types.SEND_HTTP_REQUEST] (state) {
    state.isLoading = true
  },
  [types.RESPONSE_SUCCESS] (state) {
    state.isLoading = false
  },
  [types.RESPONSE_FAIL] (state) {
    state.isLoading = false
  }
}

export default {
  state,
  getters,
  actions,
  mutations
}
