/**
 * @Date:   2018-01-03T16:09:57+08:00
 * @Last modified time: 2018-01-03T17:18:55+08:00
 */
import Comsume from '@/pages/Container/Children/Home/Children/Consume'

export default {
  path: 'consume',
  name: 'consume',
  component: Comsume,
  meta: {
    requiresAuth: true,
    requiresHideBar: true
  },
  children: []
}
