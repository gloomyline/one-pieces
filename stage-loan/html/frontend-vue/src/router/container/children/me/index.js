/**
 * @Date:   2017-12-19T10:56:35+08:00
 * @Last modified time: 2018-01-02T09:50:54+08:00
 */
import Me from '@/pages/Container/Children/Me'
import children from './children'

export default {
  path: 'me',
  name: 'me',
  component: Me,
  meta: { requiresAuth: true, requiresShowBar: true },
  children
}
