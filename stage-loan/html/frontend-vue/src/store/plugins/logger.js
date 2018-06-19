/*
* @Author: AlanWang
* @Date:   2017-10-09 09:23:28
* @Last Modified by:   AlanWang
* @Last Modified time: 2017-11-01 17:14:09
*/

import createLogger from 'vuex/dist/logger'
import * as types from '../mutation-types'

const logger = createLogger({
  collapsed: false, // 自动展开记录的 mutation
  filter (mutation, stateBefore, stateAfter) {
    // 若 mutation 需要被记录，就让它返回 true 即可
    // 顺便，`mutation` 是个 { type, payload } 对象
    const blackListedMutations = [
      types.SEND_HTTP_REQUEST,
      types.RESPONSE_SUCCESS,
      types.RESPONSE_FAIL,
      `authenticationCenter/identity/${types.UPDATE_IDENTITY_NAME}`,
      `authenticationCenter/identity/${types.UPDATE_IDENTITY_ICNUMBER}`,
      `authenticationCenter/personalInfo/${types.UPDATE_USER_INFO_PARTICULARADDRESS}`
    ]

    // TODO: use regexp to filter mutation logs
    // for (let i = 0; i < blackListedMutations.length; ++i) {
    //   let item = blackListedMutations[i]
    //   if (/item/)
    // }

    return blackListedMutations.indexOf(mutation.type) === -1

    // const whiteListedMutations = [
    //   `authenticationCenter/personalInfo/${types.UPDATE_JOB_INFO}`,
    //   // `authenticationCenter/personalInfo/${types.UPDATE_JOB_INFO_INDUSTRY}`,
    //   // `authenticationCenter/personalInfo/${types.UPDATE_JOB_INFO_ADDRESS}`,
    //   `authenticationCenter/personalInfo/${types.SAVE_JOB_INFO_SUCCESS}`,
    //   `authenticationCenter/personalInfo/${types.UPDATE_RELATION_INFO}`
    // ]

    // return whiteListedMutations.indexOf(mutation.type) !== -1
  },
  transformer (state) {
    // transform state before logs it
    // just return state of authenticationCenter
    // return state.authenticationCenter

    // just return state of personalCenter
    // return state.personalCenter

    // just return state of forgetLoginPwd
    // return state.forgetLoginPwd

    // just return state of home
    // return state.home
    return state
  }
  // mutationTransformer (mutation) {
  //   // we could format the mutation logger by any form
  //   // logs mutation into { type, payload} format
  //   return { type, payload }
  // }
})

export default logger
