/**
 * @Date:   2017-12-27T14:49:02+08:00
 * @Last modified time: 2018-01-26T11:40:32+08:00
 */
import Api from '@/api'
import * as types from '@/store/mutation-types'
// import cates from '@/assets/static-configs/category_config'

const limit = 10

const state = {
  shops: [
  ],
  selectedCateId: 0, // default select "0", prefer to all cates
  selectedCityId: '0', // default select "0", prefer to all areas
  shopName: '',
  hasMore: true,
  limit,
  shopDetail: {
    banner: []
  }
}

const getters = {
  shops: state => state.shops,
  cateId: state => state.selectedCateId,
  cityId: state => state.selectedCityId,
  shopName: state => state.shopName,
  hasMore: state => state.hasMore,
  limit: state => state.limit,
  shop: state => state.shopDetail
}

const actions = {
  async fetchShops ({ commit }, payload) {
    payload = payload || { limit }
    let res = await Api.fetchShops(payload)
    if (payload.loadingMore) {
      commit(types.LOAD_MORE_FOR_SHOPS, { shops: res.results, hasMore: res.has_more })
    } else {
      commit(types.RECEIVE_SHOPS_LIST, { shops: res.results, hasMore: res.has_more })
    }
  },
  async fetchShopsByChanging ({ state, dispatch, commit }) {
    let payload = state.shopName ? {
      category_id: state.selectedCateId,
      city_id: state.selectedCityId,
      shop_name: state.shopName,
      offset: 0,
      limit
    } : {
      category_id: state.selectedCateId,
      city_id: state.selectedCityId,
      offset: 0,
      limit
    }
    dispatch('fetchShops', payload)
  },
  async fetchShopsByLoadingMore ({ state, dispatch, commit }) {
    if (!state.hasMore) {
      return
    }
    dispatch('fetchShops', {
      category_id: state.selectedCateId,
      city_id: state.selectedCityId,
      offset: state.shops.length,
      limit,
      loadingMore: true
    })
  },
  async fetchShopDetail ({ commit }, payload) {
    payload = payload || {}
    let res = await Api.fetchShopDetail(payload)
    commit(types.RECEIVE_SHOP_DETAIL_IN_MALL, { shop: res.results })
  }
}

const mutations = {
  [types.RECEIVE_SHOPS_LIST] (state, { shops, hasMore }) {
    state.shops = shops
    state.hasMore = hasMore
  },
  [types.LOAD_MORE_FOR_SHOPS] (state, { shops, hasMore }) {
    state.shops = Array.prototype.concat.call(state.shops, shops)
    state.hasMore = hasMore
  },
  [types.UPDATE_FOR_SHOPS_IN_MALL] (state, { key, val }) {
    if (state[key] !== undefined) {
      state[key] = val
    }
  },
  [types.RECEIVE_SHOP_DETAIL_IN_MALL] (state, { shop }) {
    state.shopDetail = shop
  },
  [types.CLEAR_SEARCH] (state) {
    state.shopName = ''
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
