/*
* @Author: AlanWang
* @Date:   2017-10-31 14:06:35
* @Last Modified by:   AlanWang
* @Last Modified time: 2017-11-24 17:59:27
*/

import Api from '@/api'
import * as types from '@/store/mutation-types'

import { format } from 'date-fns'

export const OrderStateMap = {
  submit_success: '申请提交成功',
  auditing: '审核中',
  audit_failure: '审核失败',
  audit_success: '审核通过',
  granting: '放款中',
  grant_failure: '放款失败',
  repaying: '还款中',
  paying: '支付中',
  repay_success: '还款成功',
  repay_failure: '还款失败',
  overdue: '逾期中'
}

const cardMap = [
  {
    name: '绿卡',
    maxVal: 1000
  },
  {
    name: '蓝卡',
    maxVal: 2000
  },
  {
    name: '银卡',
    maxVal: 3000
  },
  {
    name: '金卡'
  }
]

// state
const state = {
  user: {
    quota: 0,
    minQuota: 0,
    maxQuota: 0,
    successCount: 0,
    mobile: '',
    overdue_rate: 0
  },
  id: '',
  orderState: '',
  detail: [],
  refundData: '',
  card: ''
}

// getters
const getters = {
  user: state => state.user,
  quota: state => state.user.quota,
  successCount: state => state.user.successCount,
  mobile: state => state.user.mobile,
  loanId: state => state.id,
  orderState: state => state.orderState,
  orderDetail: state => state.detail,
  refundData: state => JSON.stringify(state.refundData),
  card: state => state.card
}

// actions
const actions = {
  async fetchData ({ commit, dispatch }) {
    let homeData = await Api.fetchHomeData()
    commit(types.RECEIVE_HOME_DATA, { homeData })
  },
  async refund ({ commit, state }) {
    let res = await Api.refundLoan({ loan_id: state.id })
    commit(types.RECEIVE_REFUND_DATA, { refundData: res.req_data })
  }
}

// mutations
const mutations = {
  [types.RECEIVE_HOME_DATA] (state, { homeData }) {
    state.user.quota = homeData.quota
    state.user.minQuota = homeData.min_quota
    state.user.maxQuota = homeData.max_quota
    state.user.successCount = homeData.success_count
    state.user.mobile = homeData.mobile
    state.user.overdue_rate = homeData.overdue_rate || 0
    state.id = homeData.id
    state.orderState = homeData.state

    let orderDetail = []
    homeData.detail.forEach((item, index) => {
      let newItem = item
      newItem.title = OrderStateMap[item.title]
      newItem.time = item.time ? format(item.time * 1000, 'YYYY-MM-DD') : ''
      orderDetail.push(newItem)
    })
    state.detail = orderDetail

    let cardItem = cardMap.find((item) => {
      return item.maxVal >= homeData.quota
    })
    state.card = (cardItem) ? cardItem.name : cardMap[cardMap.length - 1].name
  },
  [types.RECEIVE_REFUND_DATA] (state, { refundData }) {
    state.refundData = refundData
  },
  [types.APPLY_AGAIN] (state) {
    state.orderState = null
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
