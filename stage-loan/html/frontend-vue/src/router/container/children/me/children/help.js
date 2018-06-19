/**
 * @Date:   2018-01-02T10:45:26+08:00
 * @Last modified time: 2018-01-02T17:11:01+08:00
 */
import Help from '@/pages/Container/Children/Me/Children/Help'
import Question from '@/pages/Container/Children/Me/Children/Help/Children/Question'

export default {
  path: 'help',
  name: 'help',
  component: Help,
  meta: { requiresAuth: true, requiresHideBar: true },
  children: [
    {
      path: 'question',
      name: 'question',
      component: Question,
      meta: { requiresAuth: true }
    }
  ]
}
