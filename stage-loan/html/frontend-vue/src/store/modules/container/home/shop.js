/**
 * @Date:   2018-01-04T09:45:45+08:00
 * @Last modified time: 2018-02-08T17:06:56+08:00
 */
import Api from '@/api'
import * as types from '@/store/mutation-types'
// child module personal photo
import personalPhoto from './personalPhoto'

const state = {
  productCates: [],
  products: [
    {
      cateId: 0,
      list: [],
      hasMore: true
    }
    // ...
  ],
  currentCateId: 0,
  currentOffset: 0,
  limit: 10,
  hasMore: true,
  product: {
    selectedId: 0,
    detail: {
      banner: [],
      sku: []
    },
    currentPeriod: 0,
    currentSpec: 1,
    currentCount: 1,
    currentAmount: {
      total_amount: 0,
      monthly: 0
    }
  },
  cartProducts: [],
  isPhotoConfirmed: false,
  count: 0,
  msg: ''
}

const getters = {
  cates: state => state.productCates,
  currentCateId: state => state.currentCateId,
  all: state => state.products,
  hasMore: state => state.hasMore,
  productId: state => state.product.selectedId,
  productDetail: state => state.product.detail,
  currentPeriod: state => state.product.currentPeriod,
  currentSpec: state => state.product.currentSpec,
  currentCount: state => state.product.currentCount,
  currentAmount: state => state.product.currentAmount,
  cartProducts: state => state.cartProducts, // products in shopping cart
  isPhotoConfirmed: state => state.isPhotoConfirmed, // have confirmed photo yet or not
  msg: state => state.msg
}

const actions = {
  async fetchShopProductCate ({ state, commit, rootState }) {
    let payload = {
      shop_no: rootState.container.home.consume.no
    }
    let res = await Api.fetchShopProductCate(payload)
    commit(types.RECEIVE_SHOP_PRODUCT_CATE, { cates: res.results.category })
  },
  async fetchShopProducts ({ state, commit, rootState }) {
    let checkHasMoreOrNot = function (arr, curCateId) {
      for (let i = 0, l = arr.length; i < l; ++i) {
        let item = arr[i]
        if (item.cateId === curCateId) {
          return item.hasMore
        }
      }
      // if have loaded this cateId data before, init the hasMore status
      return true
    }
    if (checkHasMoreOrNot(state.products, state.currentCateId)) {
      let payload = {
        shop_id: rootState.container.home.consume.shopBaseInfo.shop_id,
        category_id: state.currentCateId,
        offset: state.currentOffset,
        limit: state.limit
      }
      let res = await Api.fetchShopProducts(payload)
      commit(types.RECEIVE_PRODUCTS_BY_DEFINED_CATE_ID, {
        cateId: state.currentCateId,
        products: res.results,
        hasMore: res.has_more
      })
    }
  },
  async fetchShopProduct ({ state, commit }) {
    if (state.product.selectedId === undefined) return
    let payload = {
      shop_product_id: state.product.selectedId
    }
    let res = await Api.fetchShopProduct(payload)
    commit(types.RECEIVE_PRODUCT_DETAIL, { detail: res.results })
  },
  async fetchAmount ({ commit }, payload) {
    payload = payload || { amount: 0, term: 1 }
    let res = await Api.fetchAmount(payload)
    commit(types.RECEIVE_PRODUCT_AMOUNT, { results: res.results })
  },
  async submitConsume ({ state, rootState, commit }, payload) {
    let period = state.product.detail.term[state.product.currentPeriod] * 1
    let shop_id = rootState.container.home.consume.shopBaseInfo.shop_id
    let photoUrl = rootState.container.home.shop.personalPhoto.photoUrl
    let sign_pic = photoUrl ? {
      pic_url: photoUrl
    } : {
      img_b64: rootState.container.home.shop.personalPhoto.file,
      suffix: rootState.container.home.shop.personalPhoto.type
    }
    let details = [{
      shop_product_id: state.product.selectedId,
      quantity: state.product.currentCount,
      spec_id: state.product.currentSpec
    }]
    payload = payload ? Object.assign({}, payload, { sign_pic }) : { period, shop_id, details, sign_pic }
    let res = await Api.submitConsume(payload)
    commit(types.SUBMIT_LOAN_SUCCESS, { msg: res.error_message })
  }
}

const mutations = {
  [types.RECEIVE_SHOP_PRODUCT_CATE] (state, { cates }) {
    state.productCates = cates
  },
  [types.CHANGE_CURRENT_PRODUCT_CATE] (state, { newCate }) {
    state.currentCateId = newCate.id
    let selectedCate = state.products.find(item => (item.cateId === newCate.id))
    state.currentOffset = selectedCate ? selectedCate.list.length : 0
    state.hasMore = selectedCate ? selectedCate.hasMore : true
  },
  [types.RECEIVE_PRODUCTS_BY_DEFINED_CATE_ID] (state, { cateId, products, hasMore }) {
    // get all products
    let all = state.products
    let el = all.find(item => item.cateId === cateId)
    if (el) {
      let index = all.indexOf(el)
      let oldProductList = all[index]['list']
      let newProductList = Array.prototype.concat.call(oldProductList, products)
      state.products[index]['list'] = newProductList
      // current product cate corresponding to list offset
      state.currentOffset = newProductList.length
      state.products[index]['hasMore'] = hasMore
    } else {
      let newEl = {
        cateId: cateId,
        list: products,
        hasMore: hasMore
      }
      state.products.push(newEl)
    }
    state.hasMore = hasMore
  },
  [types.UPDATE_PRODUCT_ID] (state, { productId }) {
    state.product.selectedId = productId
  },
  [types.RECEIVE_PRODUCT_DETAIL] (state, { detail }) {
    state.product.detail = detail
    // init the data for new selectedId product
    state.product.currentPeriod = 0
    state.product.currentSpec = 1
    state.product.currentCount = 0
  },
  [types.RECEIVE_PRODUCT_AMOUNT] (state, { results }) {
    state.product.currentAmount = results
  },
  [types.UPDATE_FOR_PRODUCT_IN_CONSUME] (state, { key, val }) {
    if (state.product[key] !== undefined) {
      state.product[key] = val
    }
  },
  [types.ADD_PRODUCT_TO_CART] (state, { product }) {
    product = product || {}
    ++state.count
    let products = state.cartProducts
    let el = products.find((item, index) => item.spec_id === product.spec_id)
    if (el) { // there is them same spec product in the cart, just add the quan
      let curQuantity = products[products.indexOf(el)].quantity + product.quantity
      if (curQuantity >= product.stock) {
        state.cartProducts[products.indexOf(el)].quantity = product.stock
        state.msg = '已达商品库存上限！' + state.count + 'cart'
      } else {
        state.cartProducts[products.indexOf(el)].quantity = curQuantity
        state.msg = '加入成功，可在购物车中查看' + state.count + 'cart'
      }
    } else {
      state.cartProducts.push(product)
      state.msg = '加入成功，可在购物车中查看' + state.count + 'cart'
    }
  },
  [types.EMPTY_CART] (state) {
    if (state.cartProducts.length > 0) {
      state.cartProducts = []
    }
  },
  [types.CHANGE_PRODUCT_COUNT_IN_SHOP_CART] (state, { index, count }) {
    if (count > 0) {
      state.cartProducts[index].quantity = count
    } else { // del defined product from cart
      state.cartProducts.splice(index, 1)
    }
  },
  [types.SUBMIT_LOAN_SUCCESS] (state, { msg }) {
    ++state.count
    state.msg = `${msg}${state.count}`
  },
  [types.CLEAR_SHOP_DATA_FOR_CONSUME] (state) {
    state.products = [
      {
        cateId: 0,
        list: [],
        hasMore: true
      }
      // ...
    ]
    state.currentOffset = 0
    state.currentCateId = 0
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
  modules: {
    personalPhoto
  }
}
