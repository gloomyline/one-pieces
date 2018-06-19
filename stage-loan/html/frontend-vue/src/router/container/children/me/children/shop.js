/**
 * @Date:   2018-01-03T09:54:35+08:00
 * @Last modified time: 2018-01-03T09:56:29+08:00
 */
import Shop from '@/pages/Container/Children/Me/Children/Shop'

export default {
  path: 'shop',
  name: 'shopEnter',
  component: Shop,
  meta: {
    requiresAuth: true,
    requiresHideBar: true
  }
}
