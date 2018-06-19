/**
 * @Date:   2017-12-29T16:17:41+08:00
 * @Last modified time: 2018-01-26T14:15:47+08:00
 */
import AuthCenter from '@/pages/Container/Children/Me/Children/AuthCenter'
import Identity from '@/pages/Container/Children/Me/Children/AuthCenter/Children/Identity'
import Personal from '@/pages/Container/Children/Me/Children/AuthCenter/Children/PersonalInfo'
import Job from '@/pages/Container/Children/Me/Children/AuthCenter/Children/PersonalInfo/Children/Job'
import RelationShip from '@/pages/Container/Children/Me/Children/AuthCenter/Children/PersonalInfo/Children/Relationship'
import Bankcard from '@/pages/Container/Children/Me/Children/AuthCenter/Children/Bankcard'
import BankcardDetail from '@/pages/Container/Children/Me/Children/AuthCenter/Children/Bankcard/Children/Detail'
import BankcardAdd from '@/pages/Container/Children/Me/Children/AuthCenter/Children/Bankcard/Children/Add'
import BankcardAddResults from '@/pages/Container/Children/Me/Children/AuthCenter/Children/Bankcard/Children/AddResults'
// import Sesame from '@/pages/Container/Children/Me/Children/AuthCenter/Children/Sesame'
import Telephone from '@/pages/Container/Children/Me/Children/AuthCenter/Children/Telephone'
import ForgetPwd from '@/pages/Container/Children/Me/Children/AuthCenter/Children/Telephone/ForgetPwd'
import UpperLimit from '@/pages/Container/Children/Me/Children/AuthCenter/Children/UpperLimit'
import UplimitAuthen from '@/pages/Container/Children/Me/Children/AuthCenter/Children/UpperLimit/Children/Authen.vue'

export default {
  path: 'auth',
  name: 'authCenter',
  component: AuthCenter,
  meta: { requiresAuth: true, requiresHideBar: true },
  children: [
    { // 身份认证
      path: 'identity',
      name: 'identity',
      component: Identity,
      meta: { requiresAuth: true }
    },
    { // 个人信息
      path: 'personal',
      name: 'personal',
      component: Personal,
      meta: { requiresAuth: true },
      children: [
        { // 工作信息
          path: 'job',
          name: 'job',
          component: Job,
          meta: { requiresAuth: true }
        },
        { // 人际关系
          path: 'relationship',
          name: 'relationship',
          component: RelationShip,
          meta: { requiresAuth: true }
        }
      ]
    },
    { // 银行卡
      path: 'bankcard',
      name: 'bankcard',
      component: Bankcard,
      meta: { requiresAuth: true },
      children: [
        { // 银行卡详情
          path: 'detail/:id',
          name: 'bankcardDetail',
          component: BankcardDetail,
          meta: { requiresAuth: true }
        },
        { // 银行卡添加结果
          path: 'addResults',
          name: 'bankcardAddResults',
          component: BankcardAddResults,
          meta: { requiresAuth: true }
        },
        { // 银行卡添加
          path: 'add',
          name: 'bankcardAdd',
          component: BankcardAdd,
          meta: { requiresAuth: true }
        }
      ]
    },
    { // 手机认证
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
    { // 提升额度
      path: 'upperLimit',
      name: 'upperLimit',
      component: UpperLimit,
      meta: { requiresAuth: true },
      children: [
        {
          path: ':typeId',
          name: 'uplimitAuthen',
          component: UplimitAuthen,
          meta: { requiresAuth: true }
        }
      ]
    }
  ]
}
