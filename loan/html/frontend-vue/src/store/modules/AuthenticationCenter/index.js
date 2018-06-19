/*
* @Author: AlanWang
* @Date: 2017-10-09 13:49:30
* @Last Modified by:   AlanWang
* @Last Modified time: 2017-11-02 11:46:57
*/

import Api from '@/api'

import * as types from '../../mutation-types'

import modules from './Children'

// state
const state = {
  authenticatedStatus: {
    identity: 0,
    personal: 0,
    bankcard: 0,
    sesame: 0,
    telephone: 0
  }
}

// getters
const getters = {
  authenStatus: state => state.authenticatedStatus
}

// actions
const actions = {
  fetchAuthenStatus ({commit}) {
    Api.fetchUserAuthenStatus(res => {
      let resAuthenStatus = res.results[0]
      commit(types.UPDATE_AUTHENTICATION_CENTER, { resAuthenStatus })
    })
  }
}

// mutations
const mutations = {
  [types.UPDATE_AUTHENTICATION_CENTER] (state, { resAuthenStatus }) {
    let authenStatus = state.authenticatedStatus
    authenStatus.identity = resAuthenStatus.is_identity_auth
    authenStatus.personal = resAuthenStatus.is_profile_auth
    authenStatus.bankcard = resAuthenStatus.is_bankcard_auth
    authenStatus.telephone = resAuthenStatus.is_phone_auth
    // authenStatus.sesame  = resAuthenStatus.is
  },
  [types.UPDATE_AUTHENTICATION_CENTER_BY_NAME] (state, { authenStatusName, status }) {
    state.authenticatedStatus[authenStatusName] = status
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
  modules
}
