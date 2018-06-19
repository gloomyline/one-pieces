// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './router'
import store from './store'
import VueResource from 'vue-resource'
// import VueCookie from 'vue-cookie'
/* eslint-disable no-unused-vars */
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
        // query: { redirect: to.fullPath }
      })
    } else {
      next()
    }
  } else {
    next() // 确保一定要调用 next()
  }
})

// mixin
let mixins = {
  methods: {
    _alertShow (content, cb) {
      let timer
      this.$vux.alert.show({
        content,
        onHide () {
          timer && clearTimeout(timer)
          timer = null
          cb && cb()
        }
      })
      timer = setTimeout(() => {
        this.$vux.alert.hide()
        cb && cb()
      }, 3000)
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
