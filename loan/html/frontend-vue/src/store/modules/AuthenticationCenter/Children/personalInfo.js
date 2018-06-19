/*
* @Author: AlanWang
* @Date:   2017-10-09 13:59:40
* @Last Modified by:   AlanWang
* @Last Modified time: 2017-11-08 14:40:16
*/

import Api from '@/api'

import * as types from '@/store/mutation-types'

// state
const state = {
  userInfo: {
    live_area: '',
    live_addr: '',
    live_time: '',
    is_work_auth: 0,
    is_relation_auth: 0
  },
  jobInfo: {
    industry: '',
    position: '',
    company_name: '',
    company_area: '',
    company_addr: '',
    company_tel: ''
  },
  relationInfo: {
    linkman_relation_fir: '',
    linkman_name_fir: '',
    linkman_tel_fir: '',
    linkman_relation_sec: '',
    linkman_name_sec: '',
    linkman_tel_sec: ''
  },
  isUserInfoConfirmed: false,
  confirmPerMsg: '',
  confirmMsg: '',
  msgCount: 0
}

// getters
const getters = {
  userInfo: state => state.userInfo,
  jobInfo: state => state.jobInfo,
  relationInfo: state => state.relationInfo,
  isUserInfoConfirmed: state => state.isUserInfoConfirmed,
  confirmMsg: state => state.confirmMsg,
  confirmPerMsg: state => state.confirmPerMsg
}

// actions
const actions = {
  fetchUserInfo ({commit}) {
    Api.fetchPersonalInfo(userInfo => {
      commit(types.UPDATE_USER_INFO, {userInfo})
    })
  },
  saveUsreInfo ({commit}, payload) {
    Api.savePersonalInfo(payload, res => {
      if (res.status === 'SUCCESS') {
        let msg = '基本信息' + res.error_message
        commit(types.SAVE_USER_INFO_SUCCESS, {msg})
        let authenStatusName = 'personal'
        let status = 1
        // if commit mutation in global, will need to preference '{ root: true}' as the third one
        commit(`authenticationCenter/${types.UPDATE_AUTHENTICATION_CENTER_BY_NAME}`, { authenStatusName, status }, { root: true })
      }
    })
  },
  fetchJobInfo ({commit}) {
    Api.fetchWorkInfo(jobInfo => {
      commit(types.UPDATE_JOB_INFO, {jobInfo})
    })
  },
  saveJobInfo ({commit}, payload) {
    Api.saveWorkInfo(payload, res => {
      if (res.status === 'SUCCESS') {
        let msg = '工作信息' + res.error_message
        commit(types.SAVE_JOB_INFO_SUCCESS, {msg})
      }
    })
  },
  fetchRelationInfo ({commit}) {
    Api.fetchRelationInfo(relationInfo => {
      commit(types.UPDATE_RELATION_INFO, {relationInfo})
    })
  },
  saveRelationInfo ({commit}, payload) {
    Api.saveRelationInfo(payload, res => {
      if (res.status === 'SUCCESS') {
        let msg = '关系信息' + res.error_message
        commit(types.SAVE_RELATION_INFO_SUCCESS, {msg})
      }
    })
  }
}

// mutations
const mutations = {
  [types.UPDATE_USER_INFO] (state, {userInfo}) {
    state.userInfo = userInfo
  },
  [types.UPDATE_USER_INFO_PARTICULARADDRESS] (state, {address}) {
    state.userInfo.live_addr = address
  },
  [types.UPDATE_USER_INFO_ADDRESS] (state, {area}) {
    state.userInfo.live_area = area
  },
  [types.UPDATE_USER_INFO_TIME] (state, {time}) {
    state.userInfo.live_time = time
  },
  [types.SAVE_USER_INFO_SUCCESS] (state, {msg}) {
    let count = ++state.msgCount
    state.confirmPerMsg = msg + count
    state.isUserInfoConfirmed = true
  },
  [types.UPDATE_JOB_INFO] (state, {jobInfo}) {
    state.jobInfo = jobInfo
  },
  [types.UPDATE_JOB_INFO_INDUSTRY] (state, {industry}) {
    state.jobInfo.industry = industry
  },
  [types.UPDATE_JOB_INFO_POSITION] (state, {position}) {
    state.jobInfo.position = position
  },
  [types.UPDATE_JOB_INFO_COMPANY] (state, {company}) {
    state.jobInfo.company_name = company
  },
  [types.UPDATE_JOB_INFO_ADDRESS] (state, {address}) {
    state.jobInfo.company_area = address
  },
  [types.UPDATE_JOB_INFO_PARTICULARADDRESS] (state, {address}) {
    state.jobInfo.company_addr = address
  },
  [types.UPDATE_JOB_INFO_TELEPHONE] (state, {telephone}) {
    state.jobInfo.company_tel = telephone
  },
  [types.SAVE_JOB_INFO_SUCCESS] (state, {msg}) {
    let count = ++state.msgCount
    state.confirmMsg = msg + count
    state.userInfo.is_work_auth = 1
  },
  [types.UPDATE_RELATION_INFO] (state, {relationInfo}) {
    state.relationInfo = relationInfo
  },
  [types.UPDATE_RELATION_INFO_BY_KEY] (state, {key, value}) {
    state.relationInfo[key] = value
  },
  [types.SAVE_RELATION_INFO_SUCCESS] (state, {msg}) {
    let count = ++state.msgCount
    state.confirmMsg = msg + count
    state.userInfo.is_relation_auth = 1
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
