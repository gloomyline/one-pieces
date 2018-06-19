/**
 * @Date:   2017-12-19T11:50:54+08:00
 * @Last modified time: 2018-01-09T14:55:43+08:00
 */
// pages
import Settings from '@/pages/Container/Children/Me/Children/Settings'
// routes
import refund from './refund'
import lend from './lend'
import authCenter from './authCenter'
import aboutUs from './aboutUs'
import help from './help'
import shop from './shop'

export default [
  {
    path: 'settings',
    name: 'settings',
    component: Settings,
    meta: { requiresAuth: true, requiresHideBar: true }
  },
  refund,
  lend,
  authCenter,
  aboutUs,
  help,
  shop
]
