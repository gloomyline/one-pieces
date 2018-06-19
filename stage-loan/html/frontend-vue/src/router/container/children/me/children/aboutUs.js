/**
 * @Date:   2017-12-29T16:55:17+08:00
 * @Last modified time: 2018-01-02T09:50:56+08:00
 */
import AboutUs from '@/pages/Container/Children/Me/Children/AboutUs'

export default {
  path: 'us',
  name: 'aboutUs',
  component: AboutUs,
  meta: { requiresAuth: true, requiresHideBar: true },
  children: []
}
