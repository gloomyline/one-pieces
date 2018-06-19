import Vue from 'vue';
import Router from 'vue-router';

import ProductList from './pages/product/ProductList';
import ProductForm from './pages/product/ProductForm';
import ProductUpdateForm from './pages/product/ProductUpdateForm';
import ProductAuditForm from './pages/product/ProductAuditForm';
import ProductDetail from './pages/product/ProductDetail';
import DecorateForm from './pages/shop/DecorateForm';
import OrderList from './pages/order/List';
import Index from './pages/shop/index';
import PwdForm from './pages/shop/PwdForm';

Vue.use(Router);

const router = new Router({
  routes: [
    {
      path: '/product-list',
      component: ProductList,
      meta: { title: '商品列表' },
    },
    {
      path: '/pro-add',
      component: ProductForm,
      meta: { title: '商品添加' },
    },
    {
      path: '/pro-update/:id',
      component: ProductUpdateForm,
      meta: { title: '商品编辑' },
    },
    {
      path: '/pro-audit/:id',
      component: ProductAuditForm,
      meta: { title: '商品审核' },
    },
    {
      path: '/pro-detail/:id',
      component: ProductDetail,
      meta: { title: '商品详情' },
    },
    {
      path: '/decorate',
      component: DecorateForm,
      meta: { title: '商户店铺管理' },
    },
    {
      path: '/order-list',
      component: OrderList,
      meta: { title: '订单列表' },
    },
    {
      path: '/index',
      component: Index,
      meta: { title: '商户概况' },
    },
    {
      path: '/pwd',
      component: PwdForm,
      meta: { title: '修改密码' },
    },
  ],
});

module.exports = router;

