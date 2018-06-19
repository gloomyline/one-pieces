import Vue from 'vue';
import VueResource from 'vue-resource';
import Element from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
import Cookies from 'js-cookie';
// 后台样式所需
import 'font-awesome/css/font-awesome.min.css';
import 'admin-lte/bootstrap/css/bootstrap.min.css';
import 'assets/css/AdminLTE.min.css';
import 'admin-lte/dist/css/skins/_all-skins.min.css';


import App from './App';
import router from './router';
import store from './store/index';
import apiBase, { devApiBase } from './apiBase';

Vue.use(Element);
Vue.use(VueResource);

Vue.http.options.emulateJSON = true;
Vue.http.interceptors.push((request, next) => {
  if (request.url.startsWith(devApiBase)) {
    request.credentials = true; // eslint-disable-line
  }
  if (request.url.startsWith(apiBase)) {
    request.headers.set('X-CSRF-Token', Cookies.get('admin_csrf'));
  }
  next();
});

/* eslint-disable no-new */
new Vue({
  router,
  store,
  render: h => h(App),
}).$mount('#app');

