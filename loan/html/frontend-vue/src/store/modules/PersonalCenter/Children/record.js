/*
* @Author: AlanWang
* @Date:   2017-10-11 13:41:58
* @Last Modified by:   AlanWang
* @Last Modified time: 2017-11-03 14:22:43
*/

/**
 * 借还记录 module
 */

import Api from '@/api'

import * as types from '@/store/mutation-types'

// state
const state = {
  loans: [],
  repayments: [],
  loan: {},
  loanDetailShow: false,
  isLoansLoadedFull: false,
  isLoadedRepaymentsFull: false
}

// getters
const getters = {
  loans: state => state.loans,
  repayments: state => state.repayments,
  loan: state => state.loan,
  loanDetailShow: state => state.loanDetailShow,
  isLoansLoadedFull: state => state.isLoansLoadedFull,
  isLoadedRepaymentsFull: state => state.isLoadedRepaymentsFull
}

// actions
const actions = {
  fetchLoanRecords ({commit, state}, payload) {
    payload = payload || { offset: state.loans.length }
    Api.fetchLoanRecords(payload, res => { // payload: {offset: int}
      if (res.has_more) {
        commit(types.UNLOADED_RECORD_LOAN_FULL)
      } else {
        commit(types.LOADED_RECORD_LOAN_FULL)
      }

      if (res.results.length !== 0) {
        let loans = res.results
        commit(types.RECIEVE_RECORD_LOAN_LIST, {loans})
      }
    })
  },
  fetchLoanRecordById ({commit}, payload) { // payload: {loan_id: ''}
    Api.fetchLoanRecordById(payload, loan => {
      commit(types.UPDATE_RECORD_LOAN, {loan})
      commit(types.SHOW_LOAN_DETAIL)
    })
  },
  fetchRepaymentRecords ({commit, state}, payload) { // payload {offset: int}
    payload = payload || { offset: state.repayments.length }
    Api.fetchRepaymentRecords(payload, res => {
      if (res.has_more) {
        commit(types.UNLOADED_RECORD_REPAYMENT_FULL)
      } else {
        commit(types.LOADED_RECORD_REPAYMENT_FULL)
      }

      if (res.results.length !== 0) {
        let repayments = res.results
        commit(types.RECIEVE_RECORD_REPAYMENT_LIST, {repayments})
      }
    })
  }
}

// mutations
const mutations = {
  [types.RECIEVE_RECORD_LOAN_LIST] (state, {loans}) {
    state.loans = Array.prototype.concat.call(state.loans, loans)
  },
  [types.CLEAR_LOANS] (state) {
    state.loans = []
  },
  [types.LOADED_RECORD_LOAN_FULL] (state) {
    state.isLoansLoadedFull = true
  },
  [types.UNLOADED_RECORD_LOAN_FULL] (state) {
    state.isLoansLoadedFull = false
  },
  [types.UPDATE_RECORD_LOAN] (state, {loan}) {
    state.loan = loan
  },
  [types.RECIEVE_RECORD_REPAYMENT_LIST] (state, {repayments}) {
    state.repayments = Array.prototype.concat.call(state.repayments, repayments)
  },
  [types.CLEAR_REPAYMENTS] (state) {
    state.repayments = []
  },
  [types.LOADED_RECORD_REPAYMENT_FULL] (state) {
    state.isLoadedRepaymentsFull = true
  },
  [types.UNLOADED_RECORD_REPAYMENT_FULL] (state) {
    state.isLoadedRepaymentsFull = false
  },
  [types.SHOW_LOAN_DETAIL] (state) {
    state.loanDetailShow = true
  },
  [types.HIDE_LOAN_DETAIL] (state) {
    state.loanDetailShow = false
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
