/*
* @Author: AlanWang
* @Date:   2017-10-11 13:41:58
* @Last Modified by:   AlanWang
* @Last Modified time: 2017-10-30 16:02:29
*/

/**
 *  问题反馈 module
 */

import Api from '@/api'

import * as types from '@/store/mutation-types'

// state
const state = {
  questionType: 0,
  questionTypes: {
    credit: 0,        // 信用不足
    info: 1,         // 资料填写
    loan: 2,        // 借款
    repayment: 3,  // 还款
    func: 4,      // 功能建议
    other: 5     // 其他
  },
  content: '',
  resMsg: '',
  count: 0
}

// getters
const getters = {
  questionType: state => state.questionType,
  content: state => state.content,
  resMsg: state => state.resMsg
}

// actions
const actions = {
  async feedback ({commit, state}) {
    let postData = {
      type: Object.keys(state.questionTypes)[state.questionType],
      content: state.content
    }
    let res = await Api.feedback(postData)
    if (res.status === 'SUCCESS') {
      commit(types.SUBMIT_FEEDBACK_SUCCESS)
    }
  }
}

// mutations
const mutations = {
  [types.UPDATE_QUESTION_TYPE] (state, { questionType }) {
    state.questionType = questionType
  },
  [types.UPDATE_QUESTION_CONTENT] (state, { content }) {
    state.content = content
  },
  [types.SUBMIT_FEEDBACK_SUCCESS] (state) {
    ++state.count
    state.resMsg = `${state.count}问题提交成功，我们的工作人员会及时处理`
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}
