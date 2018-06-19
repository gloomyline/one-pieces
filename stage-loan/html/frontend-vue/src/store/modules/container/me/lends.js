/**
 * @Date:   2018-01-09T17:43:17+08:00
 * @Last modified time: 2018-01-19T11:00:06+08:00
 */
import Api from '@/api'
import * as types from '@/store/mutation-types'

const state = {
  all: [],
  hasMore: true,
  loanId: 0,
  detail: {},
  plans: [],
  repayment: 0,
  surplus: 0,
  logs: [],
  offset: 0,
  limit: 10,
  loadCount: 0,
  msg: '',
  count: 0
}

const getters = {
  all: state => state.all,
  hasMore: state => state.hasMore,
  loanId: state => state.loanId,
  lend: state => state.detail,
  plans: state => state.plans,
  repayment: state => state.repayment,
  surplus: state => state.surplus,
  logs: state => state.logs
}

const mutations = {
  [types.RECEIVE_LENDS_RECORD] (state, { lends, hasMore }) {
    state.all = Array.prototype.concat.call(state.all, lends)
    state.hasMore = hasMore
    ++state.loadCount
  },
  [types.UPDATE_MSG_FOR_LENDS] (state, { msg }) {
    ++state.count
    state.msg = msg + state.count
  },
  [types.INIT_DATA_FOR_LENDS] (state) {
    state.all = []
    state.hasMore = true
  },
  [types.UPDATE_LOAN_ID_FOR_LENDS] (state, { loanId }) {
    state.loanId = loanId
  },
  [types.RECEIVE_LENDS_DETAIL] (state, { detail }) {
    state.detail = detail
  },
  [types.RECEIVE_LENDS_PLAN] (state, { plan, repayment, surplus }) {
    state.plans = plan
    state.repayment = repayment
    state.surplus = surplus
  },
  [types.RECEIVE_LENDS_LOGS] (state, { logs }) {
    state.logs = logs
  }
}

const actions = {
  async fetchLends ({ state, commit }) {
    let hasMore = state.hasMore
    if (hasMore) {
      let payload = {
        offset: state.all.length, // current lends list data length
        limit: state.limit
      }
      let res = await Api.fetchLendsList(payload)
      if (res.status === 'SUCCESS') {
        commit(types.RECEIVE_LENDS_RECORD, { lends: res.results, hasMore: res.has_more })
      }
    }
  },
  async fetchLendDetail ({ state, commit }) {
    let payload = {
      loan_id: state.loanId
    }
    let res = await Api.fetchLendsDetail(payload)
    if (res.status === 'SUCCESS') {
      commit(types.RECEIVE_LENDS_DETAIL, { detail: res.results[0].detail })
      commit(types.RECEIVE_LENDS_PLAN, {
        plan: res.results[0].plan,
        repayment: res.results[0].repayed_amount,
        surplus: res.results[0].surplus_amount
      })
      commit(types.RECEIVE_LENDS_LOGS, { logs: res.results[0].log })
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
