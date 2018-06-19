/**
 * @Date:   2017-12-21T10:28:55+08:00
 * @Last modified time: 2018-02-02T09:45:25+08:00
 */
import * as types from '@/store/mutation-types'
import Api from '@/api'
import { isPassword, rmWhite } from '@/common/js/utils'

import login from './login'
import register from './register'
import resetPwd from './resetPwd'

const state = {
  isLogined: false,
  mobile: '',
  quota: {
    available_quota: 0,
    min_quota: 0,
    max_quota: 0
  },
  authState: {
    is_auth_pass: 0
  },
  modifyPwd: {
    old_password: '',
    new_password: '',
    repeat_password: '',
    validated: false,
    invalidErr: '',
    msg: '' // Success Msg
  }
}

const getters = {
  mobile: state => state.mobile, // login success user account
  isAuthed: state => state.authState.is_auth_pass,
  availableQuota: state => state.quota.available_quota,
  minQuota: state => state.quota.min_quota,
  maxQuota: state => state.quota.max_quota,
  modifyPwd: state => state.modifyPwd
}

const actions = {
  async fetchUserAuth ({ commit, dispatch }, isOnlyQuota) {
    const { is_auth_pass } = await Api.getBaseAuthState()
    commit(types.RECEIVE_USER_IS_AUTH_PASS, { is_auth_pass })
    if (is_auth_pass) {
      dispatch('fetchAvailableQuota')
    } else {
      dispatch('fetchMaxQuota')
    }
  },
  async fetchAvailableQuota ({ commit }) {
    const { available_quota } = await Api.getAvailableQuota()
    commit(types.RECEIVE_USER_AVAILABLE_QUOTA, { available_quota })
  },
  async fetchMaxQuota ({ commit }) {
    const { max_quota } = await Api.getMaxQuota()
    commit(types.RECEIVE_USER_MAX_QUOTA, { max_quota })
  },
  async confirmModifyPwd ({ state, commit }) {
    const confirmedPwd = state.modifyPwd
    commit(types.VALIDATE_FOR_MODIFY_PWD)
    // validation is not passed, do not send request to modify pwd
    if (!confirmedPwd.validated) return
    const payload = {
      old_password: confirmedPwd.old_password,
      new_password: confirmedPwd.new_password,
      repeat_password: confirmedPwd.repeat_password
    }
    let res = await Api.modifyLoginPwd(payload)
    if (res.status === 'SUCCESS') {
      commit(types.MODIFY_PWD_SUCCESS, {
        msg: res.error_message
      })
    }
  },
  async fetchMobile ({ state, commit }) {
    let res = await Api.fetchMobile()
    commit(types.RECEIVE_USER_MOBILE, { mobile: res.results.mobile })
  }
}
// global count of validation for modify pwd
let count = 0

const mutations = {
  [types.TOGGLE_USER_LOGIN_STATUS] (state, { isLogined }) {
    state.isLogined = Boolean(isLogined)
  },
  [types.RECEIVE_USER_IS_AUTH_PASS] (state, { is_auth_pass }) {
    state.authState.is_auth_pass = is_auth_pass
  },
  [types.RECEIVE_USER_AVAILABLE_QUOTA] (state, { available_quota }) {
    state.quota.available_quota = available_quota
  },
  [types.RECEIVE_USER_MAX_QUOTA] (state, { max_quota }) {
    state.quota.max_quota = max_quota
  },
  [types.RECEIVE_USER_MOBILE] (state, { mobile }) {
    state.mobile = mobile
  },
  [types.UPDATE_USER_QUOTA_RANGE] (state, { max_quota, min_quota }) {
    let quota = state.quota
    quota.min_quota = min_quota
    quota.max_quota = max_quota
  },
  [types.UPDATE_FOR_MODIFY_PWD] (state, { key, val }) {
    let el = state.modifyPwd[key]
    if (el !== undefined) {
      state.modifyPwd[key] = val
    }
  },
  [types.VALIDATE_FOR_MODIFY_PWD] (state) {
    ++count
    const modifyPwd = state.modifyPwd
    let oldPwd = modifyPwd.old_password
    let newPwd = modifyPwd.new_password
    let repeatPwd = modifyPwd.repeat_password
    if (rmWhite(oldPwd) === '') {
      modifyPwd.validated = false
      modifyPwd.invalidErr = '旧密码不可为空' + count
    } else if (!isPassword(oldPwd).isValid) {
      modifyPwd.validated = false
      modifyPwd.invalidErr = isPassword(oldPwd).errMsg + count
    } else if (rmWhite(newPwd) === '') {
      modifyPwd.validated = false
      modifyPwd.invalidErr = '新密码不可为空' + count
    } else if (!isPassword(newPwd).isValid) {
      modifyPwd.validated = false
      modifyPwd.invalidErr = isPassword(newPwd).errMsg + count
    } else if (rmWhite(repeatPwd) === '') {
      modifyPwd.validated = false
      modifyPwd.invalidErr = isPassword(repeatPwd).errMsg + count
    } else {
      modifyPwd.validated = true
    }
  },
  [types.MODIFY_PWD_SUCCESS] (state, { msg }) {
    ++count
    state.modifyPwd.msg = msg + count
  },
  [types.LOGIN_SUCCESS] (state, { mobile }) {
    state.mobile = mobile
  },
  [types.CLEAR_FOR_MODIFY_PWD] (state) {
    let modifyPwd = state.modifyPwd
    for (let k in modifyPwd) {
      modifyPwd[k] = ''
    }
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
  modules: { login, register, resetPwd }
}
