/**
 * @Date:   2018-01-09T17:41:22+08:00
 * @Last modified time: 2018-01-09T18:08:30+08:00
 */
import lends from './lends'
import refunds from './refunds'

const state = {}

const getters = {}

const actions = {}

const mutations = {}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
  modules: {
    lends,
    refunds
  }
}
