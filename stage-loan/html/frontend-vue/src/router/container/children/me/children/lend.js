/**
 * @Date:   2018-01-09T14:53:37+08:00
 * @Last modified time: 2018-01-10T17:24:59+08:00
 */
import Lend from '@/pages/Container/Children/Me/Children/Lend'
import Lends from '@/pages/Container/Children/Me/Children/Lend/Lends'
import LendDetail from '@/pages/Container/Children/Me/Children/Lend/LendDetail'
import Refunds from '@/pages/Container/Children/Me/Children/Lend/Refunds'

export default {
  path: 'lend',
  redirect: 'lend/lends',
  name: 'lend',
  meta: {
    requiresAuth: true,
    requiresHideBar: true
  },
  component: Lend,
  children: [
    {
      path: 'lends',
      name: 'lends',
      component: Lends,
      children: [
        {
          path: 'detail/:loanId',
          name: 'lendsDetail',
          component: LendDetail,
          meta: {
            requiresAuth: true
          }
        }
      ]
    },
    {
      path: 'refunds',
      name: 'refunds',
      component: Refunds
    }
  ]
}
