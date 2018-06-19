/*
* @Author: AlanWang
* @Date:   2017-10-13 09:26:37
 * @Last modified by:
 * @Last modified time: 2017-12-22T15:38:19+08:00
*/
import {
  clearObjectRecursively,
  rmWhite,
  isPhoneNumber,
  isPassword
} from '@/common/js/utils'
import Api from '@/api'
import * as types from '@/store/mutation-types'

// state
const state = {
  form: {
    mobile: '',
    code: '',
    password: '',
    card_mantissa: ''
  },
  isCodeFetched: false,
  isCoolingDown: true,
  maxCoolTime: 60, // max cool time
  coolingTime: 60, // cooling time for fetching verification
  resetCount: 0,
  validationErr: '',
  validateCount: 0,
  confirmMsg: ''
}

// getters
const getters = {
  form: state => state.form,
  isCodeFetched: state => state.isCodeFetched,
  isCoolingDown: state => state.isCoolingDown,
  coolingTime: state => state.coolingTime,
  validationErr: state => state.validationErr,
  confirmMsg: state => state.confirmMsg
}

// actions
const actions = {
  async fetchCode ({commit, state}) {
    commit(types.VALIDATE_RESET_PWD, true)
    if (state.mobileErr) return
    let payload = { mobile: state.form.mobile }
    let res = await Api.fetchVerifictionForForgetPwd(payload)
    if (res.status === 'SUCCESS') {
      commit(types.FETCH_RESET_PWD_VERIFICATION_SUCCESS)
      // start a timer to change coolingTime for fetching verification
      state.intervalId = setInterval(() => {
        commit(types.RESET_PWD_VERIFICATION_COOLING)
      }, 1000)
    }
  },
  resetPwd ({commit, state}) {
    commit(types.VALIDATE_RESET_PWD)
    if (state.validationErr.replace(/\d+$/, '') !== '') return
    let formData = state.form
    let putData = {
      mobile: formData.mobile,
      code: formData.code,
      password: formData.password,
      card_mantissa: formData.card_mantissa
    }
    Api.resetPwd(putData, res => {
      if (res.status === 'SUCCESS') {
        commit(types.RESET_LOGIN_PWD_SUCCESS, res.error_message)
      }
    })
  }
}

// mutations
const mutations = {
  [types.UPDATE_FOR_RESET_PWD] (state, { key, val }) {
    let el = state.form[key]
    if (el !== undefined) {
      state.form[key] = val
    }
  },
  [types.VALIDATE_RESET_PWD] (state, flag) {
    let mobile = state.form.mobile
    let code = state.form.code
    let password = state.form.password
    let err = ''

    if (rmWhite(mobile) === '') {
      err = '请输入手机号码'
      state.mobileErr = true
    } else if (!isPhoneNumber(mobile)) {
      err = '请输入合法的手机号'
      state.mobileErr = true
    } else {
      err = ''
      state.mobileErr = false
      if (flag) return
      if (rmWhite(code) === '') {
        err = '请输入短信验证码'
      } else if (!/\d{6}/.test(code)) {
        err = '验证码为6个数字'
      } else if (rmWhite(password) === '') {
        err = '请输入新密码'
      } else if (!isPassword(password).valid) {
        err = isPassword(password).errMsg
      } else {
        err = ''
      }
    }
    ++state.validateCount
    state.validationErr = err + state.validateCount
  },
  [types.RESET_LOGIN_PWD_SUCCESS] (state, msg) {
    // count represent the times of reseting, if we would watch 'confirmMsg',
    // so we could trigger the show msg method
    let count = ++state.resetCount
    msg = msg || '密码重置成功'
    state.confirmMsg = msg + count
  },
  [types.FETCH_RESET_PWD_VERIFICATION_SUCCESS] (state) {
    state.isCodeFetched = true
    state.isCoolingDown = false
  },
  [types.RESET_PWD_VERIFICATION_COOLING] (state) {
    // TODO: Has already record this question in Q&A.md
    --state.coolingTime
    if (state.coolingTime <= 0) {
      state.intervalId && clearInterval(state.intervalId)
      state.coolingTime = state.maxCoolTime
      state.isCoolingDown = true
    }
  },
  [types.CLEAR_FOR_RESET_PWD] (state) {
    clearObjectRecursively(state.form)
    state.intervalId && clearInterval(state.intervalId)
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
