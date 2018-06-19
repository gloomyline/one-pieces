/**
 * @Date:   2017-12-19T13:59:45+08:00
 * @Last modified time: 2017-12-29T10:54:43+08:00
 */
import Container from '@/pages/Container'
import children from './children'

export default {
  path: '',
  redirect: '/home',
  component: Container,
  meta: { requiresAuth: true },
  children
}
