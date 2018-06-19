/**
 * @Date:   2018-01-03T10:10:33+08:00
 * @Last modified time: 2018-01-25T11:51:09+08:00
 */
import auth from './auth'
import shop from './shop'
import refund from './refund'
import lend from './lend'

const state = {
}

const getters = {

}

const actions = {

}

const mutations = {}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
  modules: {
    auth,
    shop,
    refund,
    lend
  }
}
