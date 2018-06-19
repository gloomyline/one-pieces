/**
 * @Date:   2017-12-25T13:37:01+08:00
 * @Last modified time: 2018-01-02T10:04:32+08:00
 */
import Cash from '@/pages/Container/Children/Home/Children/Cash'
import Lend from '@/pages/Container/Children/Home/Children/Cash/lend.vue'

export default {
  path: 'cash',
  name: 'cash',
  component: Cash,
  meta: { requiresAuth: true, requiresHideBar: true },
  children: [
    {
      path: 'lend',
      name: 'cashLend',
      component: Lend
    }
  ]
}
