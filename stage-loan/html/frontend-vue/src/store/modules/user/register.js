/**
 * @Date:   2017-12-21T10:32:06+08:00
 * @Last modified time: 2018-01-22T10:18:49+08:00
 */
import { rmWhite, isPhoneNumber, isPassword } from '@/common/js/utils'
import * as types from '@/store/mutation-types'
import Api from '@/api'

const state = {
  form: {
    mobile: '',
    code: '',
    password: '',
    pwdAgin: '',
    isProtocolChecked: true
  },
  isCodeFetched: false,
  isCoolingDown: true,
  maxCoolTime: 60, // max cool time
  coolingTime: 60, // cooling time for fetching verification
  validationErr: '',
  validateCount: 0,
  confirmMsg: '',
  registerCount: 0
}

const getters = {
  form: state => state.form,
  isCodeFetched: state => state.isCodeFetched,
  isCoolingDown: state => state.isCoolingDown,
  coolingTime: state => state.coolingTime,
  validationErr: state => state.validationErr,
  confirmMsg: state => state.confirmMsg
}

const actions = {
  async fetchCode ({ state, commit }) {
    commit(types.VALIDATE_REGISTER, true)
    if (state.mobileErr) return
    let payload = { mobile: state.form.mobile }
    let res = await Api.fetchVerification(payload)
    if (res.status === 'SUCCESS') {
      commit(types.FETCH_REGISTER_VERIFICATION_SUCCESS)
      // start a timer to change coolingTime for fetching verification
      state.intervalId = setInterval(() => {
        commit(types.REGISTER_VERIFICATION_COOLING)
      }, 1000)
    }
  },
  async register ({ state, commit }) {
    commit(types.VALIDATE_REGISTER)
    if (state.validationErr.replace(/\d+$/, '') !== '') return
    let formData = state.form
    let postData = {
      mobile: formData.mobile,
      code: formData.code,
      password: formData.password
    }
    let res = await Api.signUp(postData)
    if (res.status === 'SUCCESS') {
      commit(types.REGISTER_SUCCESS)
    }
  }
}

const mutations = {
  [types.UPDATE_INPUT_FOR_REGISTER] (state, { key, val }) {
    let el = state.form[key]
    if (el !== undefined) {
      state.form[key] = val
    }
  },
  [types.VALIDATE_REGISTER] (state, flag) {
    let mobile = state.form.mobile
    let code = state.form.code
    let password = state.form.password
    let passwordAagin = state.form.passwordAagin
    let isProtocolChecked = state.form.isProtocolChecked
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
        err = '请输入密码'
      } else if (!isPassword(password).valid) {
        err = isPassword(password).errMsg
      } else if (rmWhite(passwordAagin) === '') {
        err = '请再次输入密码'
      } else if (!isPassword(passwordAagin).valid) {
        err = isPassword(password).errMsg
      } else if (password !== passwordAagin) {
        err = '两次输入的密码不一致'
      } else if (!isProtocolChecked) {
        err = '请认真阅读注册服务协议后勾选'
      } else {
        err = ''
      }
    }
    ++state.validateCount
    state.validationErr = err + state.validateCount
  },
  [types.REGISTER_SUCCESS] (state, msg) {
    // count represent the times of reseting, if we would watch 'confirmMsg',
    // so we could trigger the show msg method
    let count = ++state.registerCount
    state.confirmMsg = '注册成功，自动跳转到首页' + count
  },
  [types.FETCH_REGISTER_VERIFICATION_SUCCESS] (state) {
    state.isCodeFetched = true
    state.isCoolingDown = false
  },
  [types.REGISTER_VERIFICATION_COOLING] (state) {
    // TODO: Has already record this question in Q&A.md
    --state.coolingTime
    if (state.coolingTime <= 0) {
      state.intervalId && clearInterval(state.intervalId)
      state.coolingTime = state.maxCoolTime
      state.isCoolingDown = true
    }
  },
  [types.CHECK_REGISTER_PROTOCOL] (state) {
    state.form.isProtocolChecked = !state.form.isProtocolChecked
  },
  [types.CLEAR_TIME_ID_FOR_REGISTER] (state) {
    state.intervalId && clearInterval(state.intervalId)
    state.coolingTime = state.maxCoolTime
    state.isCoolingDown = true
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
