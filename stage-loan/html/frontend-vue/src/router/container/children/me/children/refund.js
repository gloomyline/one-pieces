/**
 * @Date:   2018-01-04T16:40:16+08:00
 * @Last modified time: 2018-01-23T15:31:45+08:00
 */
import Refund from '@/pages/Container/Children/Me/Children/Refund'
import RefundDetail from '@/pages/Container/Children/Me/Children/Refund/RefundDetail'
import RefundSuccess from '@/pages/Container/Children/Me/Children/Refund/RefundSuccess'
import RefundFailure from '@/pages/Container/Children/Me/Children/Refund/RefundFail'

export default {
  path: 'refund',
  name: 'refund',
  component: Refund,
  meta: {
    requiresAuth: true,
    requiresHideBar: true
  },
  children: [
    { // detail
      path: 'detail/:loanId',
      name: 'refundDetail',
      component: RefundDetail,
      meta: {
        requiresAuth: true,
        requiresHideBar: true
      }
    },
    { // refund success
      path: 'success',
      name: 'refundSucceed',
      component: RefundSuccess,
      meta: {
        requiresHideBar: true
      }
    },
    { // refund failure
      path: 'failure',
      name: 'refundFailed',
      component: RefundFailure,
      meta: {
        requiresHideBar: true
      }
    }
  ]
}
