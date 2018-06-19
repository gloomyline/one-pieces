/**
 * @Date:   2017-12-19T10:52:51+08:00
 * @Last modified time: 2018-01-02T10:04:38+08:00
 */
import Home from '@/pages/Container/Children/Home'
import children from './children'

export default {
  path: 'home',
  name: 'home',
  component: Home,
  meta: { requiresShowBar: true },
  children
}
