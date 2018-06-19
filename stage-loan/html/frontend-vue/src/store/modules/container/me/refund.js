/**
 * @Date:   2018-01-04T16:45:10+08:00
 * @Last modified time: 2018-01-09T14:26:13+08:00
 */
import Api from '@/api'
import * as types from '@/store/mutation-types'

const state = {
  total_amount: 0,  // latest monthly needed repaying
  lends: [],        // repaying records
  start: 0,         // offset of records list
  limit: 10,        // limit of records list
  has_more: true,   // flag represent that is there more data or not
  lend: {
    detail: {},
    settle: {},
    settleData: {}, // llauth pay contructured preferences from server,
    imediateData: {}
  }
}

const getters = {
  amount: state => state.total_amount,
  lends: state => state.lends,
  lend: state => state.lend.detail,
  settle: state => state.lend.settle,
  settleData: state => JSON.stringify(state.lend.settleData),
  imediateData: state => JSON.stringify(state.lend.imediateData)
}

const actions = {
  async fetchLends ({ state, commit }) {
    let payload = {
      offset: state.start,
      limit: state.limit
    }
    let res = await Api.fetchLends(payload)
    if (res.status !== 'SUCCESS') return
    commit(types.RECEIVE_LEND_RECORDS, { records: res.results.detail })
    commit(types.RECEIVE_LATEST_MONTHLY_REPAYING, { amount: res.results.total_amount })
  },
  async fetchLend ({ commit }, payload) {
    let res = await Api.fetchLend(payload)
    if (res.status === 'SUCCESS') {
      commit(types.RECEIVE_LEND_RECORD_DETAIL, { detail: res.results, loanId: payload.loan_id })
    }
  },
  async confirmSettle ({ commit }, payload) {
    let res = await Api.confirmSettle(payload)
    if (res.status === 'SUCCESS') {
      commit(types.RECEIVE_LEND_RECORDS_SETTLE, { settle: res.results, loanId: payload.loan_id })
    }
  },
  async doSettle ({ commit }, payload) {
    let res = await Api.settle(payload)
    if (res.status === 'SUCCESS') {
      commit(types.RECEIVE_SETTLE_DATA, { settleData: res.results[0].req_data })
    }
  },
  async doImediate ({ commit }, payload) {
    let res = await Api.imediate(payload)
    if (res.status === 'SUCCESS') {
      commit(types.RECEIVE_IMDIATE_DATA, { imediateData: res.results[0].req_data })
    }
  }
}

const mutations = {
  [types.INIT_DATA_FOR_LENDS_IN_REFUND] (state) {
    state.lends = []
    // state.hasMore = true
  },
  [types.RECEIVE_LEND_RECORDS] (state, { records }) {
    state.lends = records
  },
  [types.RECEIVE_LATEST_MONTHLY_REPAYING] (state, { amount }) {
    state.total_amount = amount
  },
  [types.RECEIVE_LEND_RECORD_DETAIL] (state, { detail, loanId }) {
    let lend = state.lends.find(item => item.loan_id === loanId)
    state.lend.detail = Object.assign({}, lend, detail)
  },
  [types.RECEIVE_LEND_RECORDS_SETTLE] (state, { settle, loanId }) {
    // let lend = state.lends.find(item => item.loan_id === loanId)
    // state.lend.settle = Object.assign({}, lend, settle)
    state.lend.settle = settle
  },
  [types.RECEIVE_SETTLE_DATA] (state, { settleData }) {
    state.lend.settleData = settleData
  },
  [types.RECEIVE_IMDIATE_DATA] (state, { imediateData }) {
    state.lend.imediateData = imediateData
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
  mobules: {}
}
