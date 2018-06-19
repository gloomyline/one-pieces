/*
* @Author: AlanWang
* @Date:   2017-10-09 13:56:46
* @Last Modified by:   AlanWang
* @Last Modified time: 2017-11-01 16:46:39
*/

import Api from '@/api'

import * as types from '../../../mutation-types'

// state
const state = {
  name: '',
  icNumber: '',
  btnConfirmShow: true,
  confirmMsg: '',
  confirmCount: 0
}

// getters
const getters = {
  name: state => state.name,
  icNumber: state => state.icNumber,
  btnConfirmShow: state => state.btnConfirmShow,
  confirmMsg: state => state.confirmMsg
}

// actions
const actions = {
  fetchIdentity ({commit}) {
    Api.fetchAutenticatedIdentity(identity => {
      if (identity.state === 'pass') {
        commit(types.DISABLE_IDENTITY_BTN_CONFIRM)
      }
      commit(types.UPDATE_AUTHENTICATION_IDENTITY, { identity })
    })
  },
  confirmIdentity ({ commit, rootState }, payload) {
    Api.authenticateIdentity(payload, res => {
      let msg
      if (res.status !== 'SUCCESS') return
      if (res.error_message !== 'pass') {
        msg = 'nopass'
      } else {
        msg = 'pass'
        let authenStatusName = 'identity'
        let status = 1
        // if commit mutation in global, will need to preference '{ root: true}' as the third one
        commit(`authenticationCenter/${types.UPDATE_AUTHENTICATION_CENTER_BY_NAME}`, { authenStatusName, status }, { root: true })
        commit(types.DISABLE_IDENTITY_BTN_CONFIRM)
      }
      commit(types.UPDATE_IDENTITY_CONFIRM_MSG, { msg })
    })
  }
}

// mutations
const mutations = {
  [types.UPDATE_AUTHENTICATION_IDENTITY] (state, { identity }) {
    state.name = identity.real_name
    state.icNumber = identity.identity_no
  },
  [types.DISABLE_IDENTITY_BTN_CONFIRM] (state) {
    state.btnConfirmShow = false
  },
  [types.UPDATE_IDENTITY_CONFIRM_MSG] (state, { msg }) {
    ++state.confirmCount
    state.confirmMsg = msg + state.confirmCount
  },
  [types.UPDATE_IDENTITY_NAME] (state, { name }) {
    state.name = name
  },
  [types.UPDATE_IDENTITY_ICNUMBER] (state, { icNumber }) {
    state.icNumber = icNumber
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
