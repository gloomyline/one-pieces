/**
 * @Date:   2017-12-25T16:18:24+08:00
 * @Last modified time: 2018-01-04T11:05:01+08:00
 */
// import Api from '@/api'
// import * as types from '@/store/mutation-types'
import cash from './cash'
import consume from './consume'
import shop from './shop'

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
    cash,
    consume,
    shop
  }
}
