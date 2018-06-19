/**
 * @Date:   2017-12-25T16:18:59+08:00
 * @Last modified time: 2018-01-22T16:42:47+08:00
 */
import Api from '@/api'
import * as types from '@/store/mutation-types'

const state = {
  no: '',
  isShopNoValid: false,
  shopBaseInfo: {},
  isShopAvailable: false,
  productCates: [],
  errMsg: '',
  count: 0
}

const getters = {
  no: state => state.no,
  shopBaseInfo: state => state.shopBaseInfo,
  isShopAvailable: state => state.isShopAvailable,
  errMsg: state => state.errMsg
}

const actions = {
  async search ({ state, commit }) {
    commit(types.CHECK_SHOP_NO)
    let oldShopId = state.shopBaseInfo.shop_id
    // if shop no is not valid, do nothing just return
    if (!state.isShopNoValid) return
    let payload = {
      shop_no: state.no
    }
    let res = await Api.searchShop(payload)
    if (res.status === 'SUCCESS') {
      commit(types.RECEIVE_SHOP_BASE_INFO, {
        info: res.results,
        isShopAvailable: true,
        errMsg: '成功找到商户，点击进入'
      })
      let currentShopId = res.results.shop_id
      if (currentShopId !== oldShopId) { // change shop empty cart
        commit('container/home/shop/EMPTY_CART', null, { root: true })
      }
    } else {
      commit(types.RECEIVE_SHOP_BASE_INFO, {
        info: {},
        isShopAvailable: false,
        errMsg: res.error_message
      })
    }
  }
}

const mutations = {
  [types.UPDATE_SHOP_NO] (state, { no }) {
    state.no = no
  },
  [types.CHECK_SHOP_NO] (state) {
    if (!state.no) {
      state.errMsg = `请输入商家编号后点击搜索！${++state.count}`
      state.isShopNoValid = false
      return
    }
    state.isShopNoValid = true
  },
  [types.RECEIVE_SHOP_BASE_INFO] (state, { info, isShopAvailable, errMsg }) {
    ++state.count
    state.shopBaseInfo = info
    state.isShopAvailable = isShopAvailable
    state.errMsg = errMsg + state.count
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
  modules: {}
}
