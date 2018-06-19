/**
 * @Date:   2018-01-09T17:43:29+08:00
 * @Last modified time: 2018-01-10T15:55:34+08:00
 */
import Api from '@/api'
import * as types from '@/store/mutation-types'

const state = {
  all: [],
  hasMore: true,
  offset: 0,
  limit: 10
}

const getters = {
  all: state => state.all,
  hasMore: state => state.hasMore
}

const mutations = {
  [types.RECEIVE_REFUNDS_DATA] (state, { refunds, hasMore }) {
    state.all = Array.prototype.concat.call(state.all, refunds)
    state.hasMore = hasMore
  },
  [types.INIT_DATA_FOR_REFUNDS] (state) {
    state.all = []
    state.hasMore = true
  }
}

const actions = {
  async fetchRefunds ({ state, commit }) {
    if (state.hasMore) {
      let payload = {
        offset: state.all.length,
        limit: state.limit
      }
      let res = await Api.fetchRefundsList(payload)
      if (res.status === 'SUCCESS') {
        commit(types.RECEIVE_REFUNDS_DATA, { refunds: res.results, hasMore: res.has_more })
      }
    }
  }
}

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions
}
