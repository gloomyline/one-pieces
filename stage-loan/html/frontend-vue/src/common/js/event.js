/*
* @Author: AlanWang
* @Date:   2017-09-14 16:26:55
* @Last Modified by:   AlanWang
* @Last Modified time: 2017-09-29 16:37:08
*/

'use strict'

import Vue from 'vue'

// 事件总线
const eventBus = new Vue()

// 事件类型
const eventType = {
  EVENT_LOAN_CONFIRMED: 'loan-confirmed',
  EVENT_RELATION_ADD_CONFIRMED: 'relation-confirmed',
  EVENT_IDENTITY_AUTHENED: 'identity-authened'
}

export default { eventBus, eventType }
