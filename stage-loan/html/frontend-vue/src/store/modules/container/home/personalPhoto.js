/**
 * @Date:   2018-01-25T11:47:19+08:00
 * @Last modified time: 2018-02-02T15:08:25+08:00
 */
import Api from '@/api'
import * as types from '@/store/mutation-types'

const state = {
  photo: null,
  photoUrl: '',
  file: '',
  type: '',
  isPhotoConfirmed: false,
  msg: '',
  count: 0
}

const getters = {
  photo: state => state.photo,
  photoUrl: state => state.photoUrl,
  file: state => state.file,
  type: state => state.type,
  isPhotoConfirmed: state => state.isPhotoConfirmed,
  msg: state => state.msg
}

const actions = {
  async fetchPhoto ({ commit, rootState }) {
    let res = await Api.fetchAuthenticateForUpLimit({
      auth_type: 'sign_pic',
      shop_id: rootState.container.home.consume.shopBaseInfo.shop_id
    })
    commit(types.RECEIVE_PERSONAL_PHOTO, { photoUrl: res.results[0].sign_pic })
  },
  async submitPhoto ({ state, commit }, payload) {
    let res = await Api.submitPhoto(payload)
    if (res.status === 'SUCCESS') {
      commit(types.OPERATE_PERSONAL_PHOTO_MSG, { msg: res.error_message })
      commit('authenticationCenter/UPDATE_AUTHENTICATION_CENTER_BY_NAME', {
        authenStatusName: 'personalPhoto',
        status: 1
      }, { root: true })
    }
  }
}

const mutations = {
  [types.UPDATE_FOR_PERSONAL_PHOTO] (state, { key, val }) {
    if (state[key] !== undefined) {
      state[key] = val
    }
    // clear the photoUrl in order to enable btn upload img
    state.photoUrl = ''
  },
  [types.RECEIVE_PERSONAL_PHOTO] (state, { photoUrl }) {
    state.photoUrl = photoUrl
  },
  [types.OPERATE_PERSONAL_PHOTO_MSG] (state, { msg }) {
    ++state.count
    state.msg = msg + state.count
  },
  [types.SELECTED_PERSONAL_PHOTO] (state, { file, type }) {
    state.file = file
    state.type = type
  },
  [types.TOGGLE_PHOTO_CONFIRMED] (state) {
    state.isPhotoConfirmed = !state.isPhotoConfirmed
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
