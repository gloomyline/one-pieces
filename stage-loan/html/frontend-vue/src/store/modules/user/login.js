/**
 * @Date:   2017-12-21T10:31:58+08:00
 * @Last modified time: 2018-01-05T16:38:32+08:00
 */
import { isPhoneNumber, isPassword, rmWhite } from '@/common/js/utils'
import Api from '@/api'
import * as types from '@/store/mutation-types'

const state = {
  mobile: '',
  password: '',
  validationErr: '',
  validateCount: 0
}

const getters = {
  mobile: state => state.mobile,
  password: state => state.password,
  validationErr: state => state.validationErr
}

const actions = {
  async login ({ state, commit }) {
    commit(types.VALIDATE_FOR_LOGIN)
    // local validation is not passed, do not send login request
    if (state.validationErr.replace(/[\s\d]+/g, '') !== '') {
      return
    }
    // send login request
    const postData = { mobile: state.mobile, password: state.password }
    let res = await Api.login(postData)
    if (res.status === 'SUCCESS') {
      // toggle the user login status,
      // we access the other mutations
      // in namespaced module by add '{ root: true }'
      commit('user/TOGGLE_USER_LOGIN_STATUS', { isLogined: true }, { root: true })
    } else { // ensure if login failed that the user login status is not logined
      commit('user/TOGGLE_USER_LOGIN_STATUS', { isLogined: false }, { root: true })
    }
  },
  async logout ({ state, commit }) {
    let res = await Api.logout()
    if (res.status === 'SUCCESS') {
      commit('user/TOGGLE_USER_LOGIN_STATUS', { isLogined: false }, { root: true })
      // TODO: init the tabbar zIndex
      commit('container/TOGGLE_TABBAR_SHOW', { isShow: true }, { root: true })
    } else {
      commit('user/TOGGLE_USER_LOGIN_STATUS', { isLogined: true }, { root: true })
    }
  }
}

const mutations = {
  [types.UPDATE_FOR_LOGIN] (state, { key, val }) {
    if (state[key] === undefined) return
    state[key] = val
  },
  [types.CHECKED_VALIDATION_FOR_LOGIN] (state, { errs }) {
    state.validationErrs = errs
  },
  [types.VALIDATE_FOR_LOGIN] (state) {
    let count = ++state.validateCount
    let err = ''
    // check the inputs is valid or not
    let mobile = state.mobile
    let password = state.password
    if (rmWhite(mobile) === '') { // mobile
      err = '账号为必填项'
    } else if (!isPhoneNumber(mobile)) {
      err = '账号必须为合法的手机号'
    } else if (rmWhite(password) === '') { // password
      err = '密码为必填项'
    } else if (!isPassword(password).isValid) {
      err = isPassword(password).errMsg
    } else {
      err = ''
    }

    state.validationErr = err + count
  },
  [types.CLEAR_LOGIN_INPUTS] (state) {
    state.mobile = ''
    state.password = ''
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
