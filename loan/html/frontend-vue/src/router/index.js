import Vue from 'vue'
import Router from 'vue-router'

// pages
import Login from '@/pages/Login'
import Register from '@/pages/Register'
import ForgetLoginPwd from '@/pages/ForgetLoginPwd'

import Home from '@/pages/Home'
import RefundSuccess from '@/pages/Home/Children/RefundSuccess'
import RefundFail from '@/pages/Home/Children/RefundFail'

// personal center
import Record from '@/pages/PersonalCenter/Record'
import Lend from '@/components/Record/Lend'
import Refund from '@/components/Record/Refund'

import Help from '@/pages/PersonalCenter/Help'

import Msg from '@/pages/PersonalCenter/Msg'

// invitation
import Invitation from '@/pages/PersonalCenter/Invitation'
import InvitationRecord from '@/pages/PersonalCenter/Invitation/Children/Record'
import Withdraw from '@/pages/PersonalCenter/Invitation/Children/Withdraw'

import VoucherList from '@/pages/PersonalCenter/VoucherList'

// import ModifyPwd from '@/pages/PersonalCenter/ModifyPwd'
import ModifyLoginPwd from '@/pages/PersonalCenter/ModifyPwd/Children/LoginPwd'
// import ModifyPayPwd from '@/pages/PersonalCenter/ModifyPwd/Children/PayPwd'

import Wechat from '@/pages/PersonalCenter/Wechat'
import Us from '@/pages/PersonalCenter/Us'
import Feedback from '@/pages/PersonalCenter/Feedback'

// authentication center
import Authentication from '@/pages/AuthenticationCenter'
import Identity from '@/pages/AuthenticationCenter/Children/Identity'

import PersonalInfo from '@/pages/AuthenticationCenter/Children/PersonalInfo'
import Job from '@/pages/AuthenticationCenter/Children/PersonalInfo/Children/Job'
import Relationship from '@/pages/AuthenticationCenter/Children/PersonalInfo/Children/Relationship'

import Telephone from '@/pages/AuthenticationCenter/Children/Telephone'
import ForgetPwd from '@/pages/AuthenticationCenter/Children/Telephone/Children/ForgetPwd'

import Bankcard from '@/pages/AuthenticationCenter/Children/Bankcard'
import BankcardDetail from '@/pages/AuthenticationCenter/Children/Bankcard/Children/Detail'
import BankcardAdd from '@/pages/AuthenticationCenter/Children/Bankcard/Children/Add'
import BankcardAddResults from '@/pages/AuthenticationCenter/Children/Bankcard/Children/AddResults'

import Sesame from '@/pages/AuthenticationCenter/Children/Sesame'

import UpperLimit from '@/pages/AuthenticationCenter/Children/UpperLimit'
import Authen from '@/pages/AuthenticationCenter/Children/UpperLimit/Children/Authen'

Vue.use(Router)

export default new Router({
  // mode: 'history',
  linkActiveClass: 'active',
  routes: [
    {
      path: '',
      redirect: '/home'
    },
    {
      path: '/home',
      name: 'home',
      component: Home,
      // meta: { requiresAuth: true }
      children: [
        {
          path: 'refund/success',
          name: 'refundSuccess',
          component: RefundSuccess,
          meta: { requiresAuth: false }
        },
        {
          path: 'refund/fail',
          name: 'refundFail',
          component: RefundFail,
          meta: { requiresAuth: false }
        }
      ]
    },
    {
      path: '/login',
      name: 'login',
      component: Login
    },
    {
      path: '/register',
      name: 'register',
      component: Register
    },
    {
      path: '/forgetLoginPwd',
      name: 'forgetLoginPwd',
      component: ForgetLoginPwd
    },
    { // 借还记录
      path: '/personal/record',
      component: Record,
      meta: { requiresAuth: true },
      children: [
        {
          path: '',
          name: 'recordDefault',
          component: Lend,
          meta: { requiresAuth: true }
        },
        {
          path: 'lend',
          name: 'lend',
          component: Lend
          // meta: { requiresAuth: true }
        },
        {
          path: 'refund',
          name: 'refund',
          component: Refund
          // meta: { requiresAuth: true }
        }
      ]
    },
    { // 帮助中心
      path: '/personal/help',
      name: 'help',
      component: Help,
      meta: { requiresAuth: true }
    },
    { // 消息中心
      path: '/personal/msgCenter',
      name: 'msgCenter',
      component: Msg,
      meta: { requiresAuth: true }
    },
    { // 问题反馈
      path: '/personal/feedback',
      name: 'feedback',
      component: Feedback,
      meta: { requiresAuth: true }
    },
    { // 邀请好友
      path: '/personal/invitation',
      name: 'invitation',
      component: Invitation,
      meta: { requiresAuth: true },
      children: [
        {
          path: 'record',
          name: 'invitationRecord',
          component: InvitationRecord,
          meta: { requiresAuth: true }
        },
        {
          path: 'withdraw',
          name: 'withdraw',
          component: Withdraw,
          meta: { requiresAuth: true }
        }
      ]
    },
    { // 代金券
      path: '/personal/voucherList',
      name: 'voucherList',
      component: VoucherList,
      meta: { requiresAuth: true }
    },
    { // 修改登录密码
      path: '/personal/modifyPwd',
      name: 'modifyLoginPwd',
      component: ModifyLoginPwd,
      meta: { requiresAuth: true }
    },
    // { // 修改密码

    //   path: '/personal/modifyPwd',
    //   name: 'modifyPwd',
    //   component: ModifyPwd,
    //   meta: { requiresAuth: true },
    //   children: [
    //     {
    //       path: 'login',
    //       name: 'modifyLoginPwd',
    //       component: ModifyLoginPwd,
    //       meta: { requiresAuth: true }
    //     },
    //     {
    //       path: 'pay',
    //       name: 'modifyPayPwd',
    //       component: ModifyPayPwd,
    //       meta: { requiresAuth: true }
    //     }
    //   ]
    // },
    { // wechat 公众号
      path: '/personal/wechat',
      name: 'wechat',
      component: Wechat,
      meta: { requiresAuth: true }
    },
    { // 关于我们
      path: '/personal/us',
      name: 'us',
      component: Us,
      meta: { requiresAuth: true }
    },
    { // 认证中心
      path: '/authentication',
      name: 'authentication',
      component: Authentication,
      meta: { requiresAuth: true },
      children: [
        {
          path: 'identity',
          name: 'identity',
          component: Identity,
          meta: { requiresAuth: true }
        },
        {
          path: 'personalInfo',
          name: 'personalInfo',
          component: PersonalInfo,
          meta: { requiresAuth: true },
          children: [
            {
              path: 'job',
              name: 'job',
              component: Job,
              meta: { requiresAuth: true }
            },
            {
              path: 'relationship',
              name: 'relationship',
              component: Relationship,
              meta: { requiresAuth: true }
            }
          ]
        },
        {
          path: 'bankcard',
          name: 'bankcard',
          component: Bankcard,
          meta: { requiresAuth: true },
          children: [
            {
              path: 'detail/:id',
              name: 'bankcardDetail',
              component: BankcardDetail,
              meta: { requiresAuth: true }
            },
            {
              path: 'addResults',
              name: 'bankcardAddResults',
              component: BankcardAddResults,
              meta: { requiresAuth: true }
            },
            {
              path: 'add',
              name: 'bankcardAdd',
              component: BankcardAdd,
              meta: { requiresAuth: true }
            }
          ]
        },
        {
          path: 'sesame',
          name: 'sesame',
          component: Sesame,
          meta: { requiresAuth: true }
        },
        {
          path: 'telephone',
          name: 'telephone',
          component: Telephone,
          meta: { requiresAuth: true },
          children: [
            {
              path: 'forgetPwd',
              name: 'forgetPwd',
              component: ForgetPwd,
              meta: { requiresAuth: true }
            }
          ]
        },
        {
          path: 'upperLimit',
          name: 'upperLimit',
          component: UpperLimit,
          meta: { requiresAuth: true },
          children: [
            {
              path: ':type',
              name: 'authen',
              component: Authen,
              meta: { requiresAuth: true }
            }
          ]
        }
      ]
    }
  ]
})
