/*
* @Author: AlanWang
* @Date:   2017-10-20 11:32:02
* @Last Modified by:   AlanWang
* @Last Modified time: 2017-12-05 09:42:47
*/
import Api from '@/api'

import * as types from '@/store/mutation-types'

// state
const state = {
  form: {
    diplomas: {
      acc: '',
      pwd: ''
    },
    taobao: {
      acc: '',
      pwd: '',
      phone: '',
      verification: ''
    },
    jingdong: {
      acc: '',
      pwd: ''
    },
    qq: {
      acc: '',
      pwd: ''
    },
    wechat: {
      acc: '',
      pwd: ''
    },
    bankcard: {
      acc: '',
      pwd: ''
    },
    netbank: {
      acc: '',
      pwd: ''
    },
    creditbill: {
      acc: '',
      pwd: ''
    }
  },
  count: 0,
  resMsg: '',
  isBankcardValid: false,
  bankcardInvalidMsg: '',
  jingdong: {
    authState: 0,
    isVerificationInputShow: false,
    verification: '',
    err: '',
    count: 0,
    isLoading: false
  },
  taobao: {
    authState: 0
  },
  diplomas: {
    authState: 0
  },
  bill: {
    authState: 0
  },
  ebank: {
    authState: 0
  },
  common: {
    qr: {
      img: '',
      isShow: ''
    },
    isAuthSuccess: false
  },
  authResultShowed: false
}

// getters
const getters = {
  form: state => state.form,
  resMsg: state => state.resMsg,
  isBankcardValid: state => state.isBankcardValid,
  bankcardInvalidMsg: state => state.bankcardInvalidMsg,
  jingdong: state => state.jingdong,
  taobao: state => state.taobao,
  diploma: state => state.diplomas,
  creditbill: state => state.bill,
  ebank: state => state.ebank,
  authResultShowed: state => state.authResultShowed,
  qr: state => state.common.qr,
  common: state => state.common
}

// actions
const actions = {
  async authenticate ({commit, dispatch, state}, authenticateType) {
    if (authenticateType === 'bankcard') {
      let params = {
        card_no: state.form.bankcard.acc
      }
      let res = await Api.validateBankcard(params)
      if (res.status !== 'SUCCESS') {
        commit(types.VALIDTE_BANKCARD_NUMBER_ERROR)
        return
      } else {
        commit(types.VALIDTE_BANKCARD_NUMBER_SUCCESS)
      }
    }

    if (authenticateType === 'taobao') {
      dispatch('authentiacteTaobao')
      return
    }

    if (authenticateType === 'jingdong') {
      dispatch('authenticateJingdong')
      return
    }

    if (authenticateType === 'diplomas') {
      dispatch('authenticateDiploma')
      return
    }

    if (authenticateType === 'creditbill') {
      dispatch('authenticateCreditBill')
      return
    }

    if (authenticateType === 'netbank') {
      dispatch('authenticateEbank')
      return
    }

    let postData = {
      auth_type: authenticateType,
      accounts: state.form[authenticateType].acc
    }

    let res = await Api.authenticateForUpLimit(postData)
    commit(types.AUTHENTICATE_FOR_UP_LIMIT, {resMsg: res.error_message})
  },
  async fetchAuthenticated ({commit}, authenticateType) {
    let params = {
      auth_type: authenticateType
    }
    let res = await Api.fetchAuthenticateForUpLimit(params)
    commit(types.RECEIVE_AUTHENTICATED, { authenticateType, result: res.results[0][authenticateType] })
  },
  async upLimit ({ commit }) {
    let res = await Api.upperLimit()
    if (res.status === 'SUCCESS') {
      commit(types.AUTHENTICATE_FOR_UP_LIMIT, { resMsg: res.error_message })
    }
  },
  async authenticateDiploma ({ commit, state }) {
    let diplomas = state.form.diplomas
    let postData = {
      username: diplomas.acc,
      password: diplomas.pwd
    }
    let res = await Api.authenticateDiploma(postData)
    commit(types.TOGGLE_RESULT_SHOW, { isShow: true })
    if (res.status === 'SUCCESS') {
      commit(types.CHANGE_AUTHENTICATE_DIPLOMA_STATE, { isSuccess: true })
    } else {
      commit(types.CHANGE_AUTHENTICATE_DIPLOMA_STATE, { isSuccess: false })
      commit(types.AUTHENTICATE_FOR_UP_LIMIT, { resMsg: res.error_message })
    }
  },
  async authenticateJingdong ({ commit, state }) {
    let postData = {
      username: state.form.jingdong.acc,
      password: state.form.jingdong.pwd
    }
    let res = await Api.authenticateJingdong(postData)
    if (res.status === 'SUCCESS' && res.error_message !== '0006') {
      commit(types.SHOW_JINGDONG_AUTHENTICATE_SUCCESS)
    } else if (res.status === 'SUCCESS' && res.error_message === '0006') {
      commit(types.TOGGLE_JINGDONG_VERIFICATION_INPUT, { isInputShow: true })
    } else {
      commit(types.ALERT_JINGDONG_AUTHENTICATE_ERROR, { err: res.error_message })
      commit(types.CHANGE_JINGDONG_AUTHENTICATE, { isPending: false })
    }
  },
  async authenticateJingdongVerification ({ commit, state }) {
    commit(types.TOGGLE_JINGDONG_VERIFICATION_INPUT, { isInputShow: false })
    let postData = {
      smscode: state.jingdong.verification
    }
    let res = await Api.authenticateJingdongVerification(postData)
    if (res.status === 'SUCCESS' && res.error_message === '0009') {
      let _res = await Api.authenticateJingdongContinue()
      if (_res.status === 'SUCCESS' && _res.error_message === '0100') {
        commit(types.SHOW_JINGDONG_AUTHENTICATE_SUCCESS)
      } else {
        commit(types.ALERT_JINGDONG_AUTHENTICATE_ERROR, { err: res.error_message })
      }
      commit(types.CHANGE_JINGDONG_AUTHENTICATE, { isPending: false })
    } else {
      commit(types.ALERT_JINGDONG_AUTHENTICATE_ERROR, { err: res.error_message })
      commit(types.CHANGE_JINGDONG_AUTHENTICATE, { isPending: false })
    }
  },
  async authentiacteTaobao ({ commit }) {
    let res = await Api.authentiacteTaobao()
    if (res.status === 'SUCCESS' && res.results[0].input) {
      commit(types.RECEIVE_AUTHENTICATE_QR, { qrBase64String: res.results[0].input })
    }
  },
  async authentiacteTaobaoContinue ({ commit }) {
    let _res = await Api.authentiacteTaobaoContinue()
    if (_res.status === 'SUCCESS' && _res.error_message === '0100') {
      commit(types.CHANGE_AUTHENTICATE_TAOBAO_STATE, { isSuccess: true })
    } else {
      commit(types.CHANGE_AUTHENTICATE_TAOBAO_STATE, { isSuccess: false })
      commit(types.AUTHENTICATE_FOR_UP_LIMIT, { resMsg: _res.error_message })
    }
  },
  async authenticateCreditBill ({ commit, state }) {
    let postData = {
      username: state.form.creditbill.acc,
      password: state.form.creditbill.pwd
    }
    let res = await Api.authenticateCreditBill(postData)
    if (res.status === 'SUCCESS' && res.error_message === '0006') { // store the base64String of qr img
      commit(types.RECEIVE_AUTHENTICATE_QR, { qrBase64String: res.results[0].input })
    } else { // alert error_message include error from server or success tip
      commit(types.AUTHENTICATE_FOR_UP_LIMIT, { resMsg: res.error_message })
    }
  },
  async authenticateCreditBillContinue ({ commit }) {
    let res = await Api.authenticateCreditBillContinue()
    if (res.status === 'SUCCESS' && res.error_message === '0100') {
      commit(types.CHANGE_CREDIT_BILL_STATE, { isSuccess: true })
    } else {
      commit(types.CHANGE_CREDIT_BILL_STATE, { isSuccess: false })
    }
  },
  async authenticateEbank ({ commit, state }) {
    let postData = {
      username: state.form.netbank.acc,
      password: state.form.netbank.pwd,
      bank: '01040000'
    }
    let res = await Api.authenticateEbank(postData)
    commit(types.TOGGLE_RESULT_SHOW, { isShow: true })
    if (res.status === 'SUCCESS') {
      commit(types.CHANGE_AUTHENTICATE_EBANK_STATE, { isSuccess: true })
    } else {
      commit(types.CHANGE_AUTHENTICATE_EBANK_STATE, { isSuccess: false })
      commit(types.AUTHENTICATE_FOR_UP_LIMIT, { resMsg: res.error_message })
    }
  }
}

// mutations
const mutations = {
  [types.UPDATE_AUTHENTICATE_ACC] (state, { key, acc }) {
    state.form[key].acc = acc
  },
  [types.UPDATE_AUTHENTICATE_PWD] (state, { key, pwd }) {
    state.form[key].pwd = pwd
  },
  [types.UPDATE_AUTHENTICATE_TAOBAO_PHONE] (state, { phone }) {
    state.form['taobao'].phone = phone
  },
  [types.UPDATE_AUTHENTICATE_TAOBAO_VERIFICATION] (state, { verification }) {
    state.form['taobao'].verification = verification
  },
  [types.UPDATE_AUTHENTICATE_JINDONG_VERIFICATION] (state, { verification }) {
    state.jingdong.verification = verification
  },
  [types.VALIDTE_BANKCARD_NUMBER_ERROR] (state, { errMsg }) {
    let count = ++state.count
    state.isBankcardValid = false
    state.bankcardInvalidMsg = errMsg + count
  },
  [types.VALIDTE_BANKCARD_NUMBER_SUCCESS] (state) {
    state.isBankcardValid = true
  },
  [types.AUTHENTICATE_FOR_UP_LIMIT] (state, { resMsg }) {
    let count = ++state.count
    state.resMsg = resMsg + count
  },
  [types.RECEIVE_AUTHENTICATED] (state, { authenticateType, result }) {
    authenticateType = authenticateType === 'jd' ? 'jingdong' : authenticateType
    authenticateType = authenticateType === 'education' ? 'diplomas' : authenticateType
    if (authenticateType === 'bankcard' || authenticateType === 'wechat' || authenticateType === 'qq') {
      state.form[authenticateType].acc = result
      if (result) {
        state.common.isAuthSuccess = true
      } else {
        state.common.isAuthSuccess = false
      }
    } else {
      state[authenticateType].authState = result
    }
    if (result) {
      state.authResultShowed = true
    } else {
      state.authResultShowed = false
    }
  },
  [types.TOGGLE_JINGDONG_VERIFICATION_INPUT] (state, { isInputShow }) {
    state.jingdong.isVerificationInputShow = isInputShow
    state.jingdong.isLoading = !isInputShow
  },
  [types.ALERT_JINGDONG_AUTHENTICATE_ERROR] (state, { err }) {
    ++state.jingdong.count
    state.jingdong.err = err + state.jingdong.count
  },
  [types.SHOW_JINGDONG_AUTHENTICATE_SUCCESS] (state) {
    state.jingdong.authState = 1
    state.authResultShowed = true
  },
  [types.CHANGE_JINGDONG_AUTHENTICATE] (state, { isPending }) {
    state.jingdong.isLoading = isPending
  },
  [types.RECEIVE_AUTHENTICATE_QR] (state, { qrBase64String }) {
    let qr = state.common.qr
    qr.img = qrBase64String
    qr.isShow = true
  },
  [types.CLOSE_AUTHENTICATE_POPUP_QR] (state) {
    state.common.qr.isShow = false
  },
  [types.CHANGE_AUTHENTICATE_TAOBAO_STATE] (state, { isSuccess }) {
    state.taobao.authState = isSuccess ? 1 : 0
    state.authResultShowed = isSuccess
  },
  [types.CHANGE_CREDIT_BILL_STATE] (state, { isSuccess }) {
    state.bill.authState = isSuccess ? 1 : 0
    state.authResultShowed = isSuccess
  },
  [types.CHANGE_AUTHENTICATE_EBANK_STATE] (state, { isSuccess }) {
    state.ebank.authState = isSuccess ? 1 : 0
    state.authResultShowed = isSuccess
  },
  [types.TOGGLE_RESULT_SHOW] (state, { isShow }) {
    if (isShow === undefined) {
      state.authResultShowed = !state.authResultShowed
    } else {
      state.authResultShowed = isShow
    }
  },
  [types.AUTHEN_DIPLOMA_SUCCESS] (state, { msg }) {
    let count = ++state.count
    state.resMsg = msg + count
  },
  [types.CHANGE_AUTHENTICATE_DIPLOMA_STATE] (state, { isSuccess }) {
    state.diplomas.authState = isSuccess ? 1 : 0
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
