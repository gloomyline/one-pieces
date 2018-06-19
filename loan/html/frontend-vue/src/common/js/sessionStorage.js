/*
* @Author: AlanWang
* @Date:   2017-09-08 13:58:34
* @Last Modified by:   AlanWang
* @Last Modified time: 2017-09-14 11:11:51
*/

let sessionStorage = window.sessionStorage

export default {
  saveLoginStatus (value) {
    let loginStatus = sessionStorage.__login__
    loginStatus = sessionStorage.__login__ ? JSON.parse(loginStatus) : {}
    loginStatus.token = value
    loginStatus.isLogin = true
    sessionStorage.__login__ = JSON.stringify(loginStatus)
  },
  loadLoginStatus (def) {
    let loginStatus = sessionStorage.__login__
    if (!loginStatus) return false
    else {
      loginStatus = JSON.parse(loginStatus)
      if (!loginStatus.isLogin) return def || false
      else return loginStatus
    }
  },
  clearLoginStatus () {
    let loginStatus = sessionStorage.__login__
    if (!loginStatus) return
    else {
      loginStatus = JSON.parse(loginStatus)
      loginStatus.token = null
      loginStatus.isLogin = false
      sessionStorage.__login__ = JSON.stringify(loginStatus)
    }
  }
}
