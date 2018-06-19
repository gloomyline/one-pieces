/**
 * @Date:   2018-01-03T10:10:23+08:00
 * @Last modified time: 2018-01-22T15:20:14+08:00
 */
import Api from '@/api'
import * as types from '@/store/mutation-types'

const state = {
  shop: {
    name: '',
    relation: '',
    telephone: '',
    address: ''
  },
  count: 0,
  msg: ''
}

const getters = {
  shop: state => state.shop,
  msg: state => state.msg
}

const actions = {
  async fetchShop ({ commit }) {
    // let res = await Api.fetchShop()
  },
  async submitShop ({ state, commit }) {
    const payload = {
      shop_name: state.shop.name,
      contacts_name: state.shop.relation,
      contacts_addr: state.shop.address,
      contacts_mobile: state.shop.telephone
    }
    let res = await Api.submitShop(payload)
    if (res.status === 'SUCCESS') {
      commit(types.SUBMIT_SHOP_SUCCESS, { msg: res.error_message })
    }
  }
}

const mutations = {
  [types.UPDATE_FOR_SHOP] (state, { key, val }) {
    if (state.shop[key] !== undefined) {
      state.shop[key] = val
    }
  },
  [types.SUBMIT_SHOP_SUCCESS] (state, { msg }) {
    state.msg = msg + (++state.count)
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
