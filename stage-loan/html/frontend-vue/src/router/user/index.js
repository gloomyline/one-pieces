/**
 * @Date:   2017-12-19T11:27:52+08:00
 * @Last modified time: 2017-12-19T11:45:08+08:00
 */
import Login from '@/pages/User/Login'
import Register from '@/pages/User/Register'
import ResetPwd from '@/pages/User/ResetPwd'

const login = {
  path: '/login',
  name: 'login',
  component: Login
}

const register = {
  path: '/register',
  name: 'register',
  component: Register
}

const resetPwd = {
  path: '/resetPwd',
  name: 'resetPwd',
  component: ResetPwd
}

export default [login, register, resetPwd]
