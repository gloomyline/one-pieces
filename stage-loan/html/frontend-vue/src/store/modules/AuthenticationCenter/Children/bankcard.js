/*
* @Author: AlanWang
* @Date:   2017-10-09 14:00:16
* @Last Modified by:   AlanWang
* @Last Modified time: 2017-11-08 10:32:31
*/

import Api from '@/api'

import * as types from '@/store/mutation-types'

import bankData from '@/assets/datas/bankcard'

// state
const state = {
  formData: {
    acct_name: '',
    card_no: '',
    id_no: ''
  },
  bankcards: [],           // list of authenticated bankcard
  count: 0,               // record the count of errMsg include validate and responseErr
  reqData: '',           // type Object
  validateErr: '',      // cardbin validate error
  responseErrMsg: ''   // response error
}

// getters
const getters = {
  bankcards: state => state.bankcards,
  bankcardsHasMore: state => state.bankcardsHasMore,
  formData: state => state.formData,
  reqData: state => state.reqData,
  validateErr: state => state.validateErr,
  responseErrMsg: state => state.responseErrMsg
}

// actions
const actions = {
  async fetchBankcards ({ commit }) {
    let res = await Api.fetchBankcards()
    commit(types.RECEIVE_AUTHENTICATED_BANKCARDS, { bankcards: res.results })
  },
  async setDefaultBankcard ({ commit }, payload) {
    payload = payload || {id: 1}
    let res = await Api.setDefaultBankcard(payload)
    if (res.status === 'SUCCESS') {
      commit(types.SET_DEFAULT_BANKCARD, { id: payload.id, resMsg: res.error_message })
    }
  },
  async validateBankcard ({ commit, state }) {
    let params = {
      card_no: state.formData.card_no
    }
    let res = await Api.validateBankcard(params)
    if (res.status !== 'SUCCESS') {
      commit(types.VALIDTE_BANKCARD_NUMBER_ERROR)
    }
  },
  async addBankcard ({ commit, state }, cb) {
    let params = {
      // acct_name: state.formData.acct_name,
      card_no: state.formData.card_no.replace(/\s+/g, '')
      // id_no: state.formData.id_no
    }
    let res = await Api.addBankcard(params)
    if (res.status === 'SUCCESS') {
      let reqData = res.results[0].req_data
      commit('RECEIVE_ADD_BANKCARD_REQ_DATA', { reqData })
      cb && cb()
      return reqData
    } else {
      let errMsg = res.err_msg
      commit('UPDATE_ADD_BANKCARD_RESPONSE_ERR_MSG', { errMsg })
    }
  },
  async authBankcard ({ commit, dispatch }) {
    let reqData = await dispatch('addBankcard')
    let res = await Api.authenticateBankcard({ req_data: JSON.stringify(reqData) })
    return res
  }
}

// mutations
const mutations = {
  [types.RECEIVE_AUTHENTICATED_BANKCARDS] (state, { bankcards }) {
    let bankcardList = []
    bankcards.forEach((item, index) => {
      // new bankcard obj
      let bankcard = item
      // add key 'bankId'
      let bank = bankData.find((el) => {
        return el.code === item.bank_code
      })
      bankcard.bankId = bank ? bank.id : 1
      // move the default card to the first position in the bankcard list
      if (bankcard.is_default === 1) {
        bankcardList.unshift(bankcard)
      } else {
        bankcardList.push(bankcard)
      }
    })
    state.bankcards = bankcardList
  },
  [types.SET_DEFAULT_BANKCARD] (state, { id, resMsg }) {
    // let bankcardList = state.bankcards
    let bankcardList = []
    state.bankcards.forEach((item, index) => {
      if (item.id === id) {
        item.is_default = 1
        bankcardList.unshift(item)
      } else {
        item.is_default = 0
        bankcardList.push(item)
      }
    })
    let count = ++state.count
    state.responseErrMsg = resMsg + count
    state.bankcards = bankcardList
  },
  [types.UPDATE_ACCOUNT_NAME] (state, { name }) {
    state.formData.acct_name = name
  },
  [types.UPDATE_BANKCARD_NUM] (state, { num }) {
    state.formData.card_no = num
  },
  [types.CLEAR_BANKCARD_NUM] (state) {
    state.formData.card_no = ''
  },
  [types.UPDATE_IDCARD_NUM] (state, { num }) {
    state.formData.id_no = num
  },
  [types.VALIDTE_BANKCARD_NUMBER_ERROR] (state) {
    let count = ++state.count
    state.formData.validateErr = '请输入合法的银行卡号码' + count
  },
  [types.RECEIVE_ADD_BANKCARD_REQ_DATA] (state, { reqData }) {
    state.reqData = reqData
  },
  [types.UPDATE_ADD_BANKCARD_RESPONSE_ERR_MSG] (state, { errMsg }) {
    state.responseErrMsg = errMsg
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
