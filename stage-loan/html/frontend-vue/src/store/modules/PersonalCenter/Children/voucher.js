/*
* @Author: AlanWang
* @Date:   2017-10-11 13:41:58
* @Last Modified by:   AlanWang
* @Last Modified time: 2017-10-19 13:30:25
*/

/**
 * 代金券 module
 */

import Api from '@/api'

import * as types from '@/store/mutation-types'

// state
const state = {
  vouchers: [],
  hasMore: true,
  loadedCount: 0,
  loadLimit: 10
}

// getters
const getters = {
  vouchers: state => state.vouchers,
  hasMore: state => state.hasMore
}

// actions
const actions = {
  async fetchVouchers ({commit, state}) {
    let params = {
      offset: state.loadedCount * state.loadLimit,
      limit: state.loadLimit
    }
    let res = await Api.fetchVouchers(params)
    let vouchers = res.results
    let hasMore = res.has_more
    commit(types.UPDATE_VOUCHERS, { vouchers, hasMore })
  }
}

// mutations
const mutations = {
  [types.UPDATE_VOUCHERS] (state, { vouchers, hasMore }) {
    ++state.loadedCount
    state.vouchers = Array.prototype.concat.call(state.vouchers, vouchers)
    state.hasMore = hasMore
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
