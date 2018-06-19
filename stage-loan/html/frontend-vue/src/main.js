/**
 * @Date:   2017-12-14T17:05:37+08:00
 * @Last modified time: 2018-01-30T16:52:24+08:00
 */

// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './router'
import store from './store'
import VueResource from 'vue-resource'
// import VueCookie from 'vue-cookie'
/* eslint-disable no-unused-vars */
import { getViewPortSize, getWindowAdaptRatio } from '@/common/js/polyfill'
import session from '@/common/js/sessionStorage'

import { AlertPlugin, WechatPlugin } from 'vux'

Vue.config.productionTip = false

// ////////////////////////
// import common css/js //
// ////////////////////////
// css
import './common/stylus/index.styl'

// js
Vue.use(AlertPlugin)
Vue.use(WechatPlugin)

Vue.use(VueResource)
// VueResource global configuration
Vue.http.options = {
  emulateJSON: true,
  emulateHTTP: true
}

// mount constants and static methods
const WINDOW_SIZE = {
  width: 375,
  height: 667
}
Vue.prototype.constants = {
  WINDOW_SIZE,
  WINDOW_ADAPT_RATIO: {
    width: getViewPortSize().width / WINDOW_SIZE.width,
    height: getViewPortSize().height / WINDOW_SIZE.height
  }
}
Vue.prototype.utils = {
  getViewPortSize
}

const wx = Vue.wechat
const http = Vue.http
// wx.ready(() => {
//   console.log('11', wx)
// })

// router global hook
router.beforeEach((to, from, next) => {
  if (to.matched.some(record => record.meta.requiresAuth)) {
    // this route requires auth, check if logged in
    // if not, redirect to login page.
    if (!session.loadLoginStatus().isLogin) {
      next({
        path: '/login'
      })
    } else {
      next()
    }
  } else {
    next() // 确保一定要调用 next()
  }
  // toggle the tabbar show or not
  if (to.matched.some(r => r.meta.requiresHideBar)) {
    store.commit('container/TOGGLE_TABBAR_SHOW', { isShow: false })
    next()
  } else if (from.matched.some(r => r.meta.requiresHideBar) &&
  to.matched.some(r => r.meta.requiresShowBar)) {
    store.commit('container/TOGGLE_TABBAR_SHOW', { isShow: true })
    next()
  } else {
    next()
  }
})

// mixin
let mixins = {
  methods: {
    _alertShow (content, cb, noAuto) {
      let timer
      this.$vux.alert.show({ // this => vm, invoked component context
        content,
        onHide () {
          timer && clearTimeout(timer)
          timer = null
          cb && cb()
        }
      })
      if (noAuto) {
        return
      } else {
        timer = setTimeout(() => {
          this.$vux.alert.hide()
          cb && cb()
        }, 3000)
      }
    }
  }
}
Vue.mixin(mixins)

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  store,
  template: '<App/>',
  components: { App }
})
