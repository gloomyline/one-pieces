/*
* @Author: AlanWang
* @Date:   2017-10-13 09:26:37
* @Last Modified by:   AlanWang
* @Last Modified time: 2017-11-01 15:03:24
*/

import Api from '@/api'

import * as types from '@/store/mutation-types'

// state
const state = {
  form: {
    phone: '',
    verification: '',
    resetingPwd: ''
  },
  isVerificationFetched: false,
  maxCoolTime: 60, // max cool time
  coolingTime: 60, // cooling time for fetching verification
  resetCount: 0,
  confirmMsg: ''
}

// getters
const getters = {
  form: state => state.form,
  isVerificationFetched: state => state.isVerificationFetched,
  coolingTime: state => state.coolingTime,
  confirmMsg: state => state.confirmMsg
}

// actions
const actions = {
  fetchVerification ({commit, state}, payload) {
    Api.fetchVerifictionForForgetPwd(payload, res => {
      if (res.status === 'SUCCESS') {
        commit(types.FETCH_RESET_PWD_VERIFICATION_SUCCESS)

        // start a timer to change coolingTime for fetching verification
        state.intervalId = setInterval(() => {
          commit(types.RESET_PWD_VERIFICATION_COOLING)
        }, 1000)
      }
    })
  },
  resetPwd ({commit, state}) {
    let formData = state.form
    let putData = {
      mobile: formData.phone,
      code: formData.verification,
      password: formData.resetingPwd
    }
    Api.resetPwd(putData, res => {
      if (res.status === 'SUCCESS') {
        let msg = '密码重置成功'
        commit(types.RESET_LOGIN_PWD_SUCCESS, msg)
      }
    })
  }
}

// mutations
const mutations = {
  [types.UPDATE_PHONE] (state, {phone}) {
    state.form.phone = phone
  },
  [types.UPDATE_VERIFICATION] (state, {verification}) {
    state.form.verification = verification
  },
  [types.UPDATE_RESET_PWD] (state, {resetingPwd}) {
    state.form.resetingPwd = resetingPwd
  },
  [types.RESET_LOGIN_PWD_SUCCESS] (state, msg) {
    // count represent the times of reseting, if we would watch 'confirmMsg',
    // so we could trigger the show msg method
    let count = ++state.resetCount
    state.confirmMsg = msg + count
    console.log(11, msg + count)
  },
  [types.FETCH_RESET_PWD_VERIFICATION_SUCCESS] (state) {
    state.isVerificationFetched = true
  },
  [types.RESET_PWD_VERIFICATION_COOLING] (state) {
    // TODO: Has already record this question in Q&A.md
    --state.coolingTime
    if (state.coolingTime <= 0) {
      state.intervalId && clearInterval(state.intervalId)
      state.coolingTime = state.maxCoolTime
      state.isVerificationFetched = false
    }
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
