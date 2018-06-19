/**
 * @Date:   2017-12-25T11:05:37+08:00
 * @Last modified time: 2018-01-05T15:13:37+08:00
 */
import * as types from '@/store/mutation-types'
import home from './home'
import mall from './mall'
import me from './me'

const state = {
  ui: {
    tabbarHide: false
  }
}

const getters = {
  isTabbarHide: state => state.ui.tabbarHide
}

const actions = {
}

const mutations = {
  [types.TOGGLE_TABBAR_SHOW] (state, { isShow }) {
    if (!isShow && isShow === undefined) {
      state.ui.tabbarHide = !state.ui.tabbarHide
    } else if (isShow !== undefined) {
      state.ui.tabbarHide = !isShow
    }
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
  modules: { home, mall, me }
}
