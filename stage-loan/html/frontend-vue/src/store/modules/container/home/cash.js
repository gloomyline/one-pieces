/**
 * @Date:   2017-12-25T16:18:53+08:00
 * @Last modified time: 2017-12-27T12:06:43+08:00
 */

import Api from '@/api'
import * as types from '@/store/mutation-types'

const state = {
  lendCount: 2000,
  uses: [],
  use: ['1'], // default '学习进修'
  periods: [],
  period: ['4'], // default '12月'
  rate: {},
  bankcardName: '',
  bankcardNo: ''
}

const getters = {
  lendCount: state => state.lendCount,
  uses: state => state.uses,
  use: state => state.use,
  periods: state => state.periods,
  period: state => state.period,
  rate: state => state.rate,
  bankcardName: state => state.bankcardName,
  bankcardNo: state => state.bankcardNo
}

const actions = {
  async fetchCashLendData ({ commit }) {
    let data = await Api.cashTimesLoan()
    const {
      max_quota,
      min_quota,
      use,
      period,
      annualized_interest_rate,
      trial_rate,
      service_rate,
      overdue_rate,
      poundage,
      bank_name,
      end_bank_no
    } = data.results
    commit(`user/${types.UPDATE_USER_QUOTA_RANGE}`, { max_quota, min_quota }, { root: true })
    commit(types.RECEIVE_CASH_USE_AND_PERIOD, { use, period })
    commit(types.RECEIVE_RATE, {
      annualized_interest_rate,
      trial_rate,
      service_rate,
      overdue_rate,
      poundage
    })
    commit(types.RECEIVE_USER_USE_BANKCARD, { bank_name, end_bank_no })
  },
  async confirmCashLend ({ commit }, payload) {
    let res = await Api.cashLend(payload)
    if (res.status === 'SUCCESS') {
      return res.error_message
    }
  }
}

const mutations = {
  [types.UPDATE_FOR_CASH] (state, { key, val }) {
    let el = state[key]
    if (el !== undefined) {
      state[key] = val
    }
  },
  [types.RECEIVE_CASH_USE_AND_PERIOD] (state, { use, period }) {
    state.uses = use
    state.periods = period
  },
  [types.RECEIVE_RATE] (state, { annualized_interest_rate, trial_rate, service_rate, overdue_rate, poundage }) {
    state.rate = { annualized_interest_rate, trial_rate, service_rate, overdue_rate, poundage }
  },
  [types.RECEIVE_USER_USE_BANKCARD] (state, { bank_name, end_bank_no }) {
    state.bankcardName = bank_name
    state.bankcardNo = end_bank_no
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
