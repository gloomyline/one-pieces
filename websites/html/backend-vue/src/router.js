import Vue from 'vue';
import Router from 'vue-router';

import AdminList from './pages/admin/List';
import AdminForm from './pages/admin/Form';
import AssignForm from './pages/admin/AssignForm';
import RoleList from './pages/role/List';
import RoleForm from './pages/role/Form';
import AuthList from './pages/auth/List';
import AuthForm from './pages/auth/Form';
import NavList from './pages/nav/List';
import PartnerList from './pages/partner/List';
import BannerList from './pages/banner/List';
import ExampleList from './pages/example/List';
import ArticleList from './pages/article/List';
import ArticleForm from './pages/article/Form';

Vue.use(Router);

const router = new Router({
  routes: [
    {
      path: '/admins',
      component: AdminList,
      meta: { title: '管理员列表' },
    },
    {
      path: '/add-admin',
      component: AdminForm,
      meta: { title: '添加管理员' },
    },
    {
      path: '/update-admin/:id',
      component: AdminForm,
      meta: { title: '编辑管理员' },
    },
    {
      path: '/roles',
      component: RoleList,
      meta: { title: '角色列表' },
    },
    {
      path: '/add-role',
      component: RoleForm,
      meta: { title: '添加角色' },
    },
    {
      path: '/update-role/:name',
      component: RoleForm,
      meta: { title: '编辑角色' },
    },
    {
      path: '/assign-auth/:name',
      component: AssignForm,
      meta: { title: '权限分配' },
    },
    {
      path: '/auths',
      component: AuthList,
      meta: { title: '权限列表' },
    },
    {
      path: '/add-auth',
      component: AuthForm,
      meta: { title: '添加权限' },
    },
    {
      path: '/update-auth/:name',
      component: AuthForm,
      meta: { title: '编辑权限' },
    },
    {
      path: '/nav',
      component: NavList,
      meta: { title: '导航列表' },
    },
    {
      path: '/partner',
      component: PartnerList,
      meta: { title: '合作伙伴' },
    },
    {
      path: '/banner',
      component: BannerList,
      meta: { title: 'Banner' },
    },
    {
      path: '/example',
      component: ExampleList,
      meta: { title: '案例列表' },
    },
    {
      path: '/article',
      component: ArticleList,
      meta: { title: '文章列表' },
    },
    {
      path: '/article-add',
      component: ArticleForm,
      meta: { title: '添加文章' },
    },
    {
      path: '/article-update/:id',
      component: ArticleForm,
      meta: { title: '编辑文章' },
    },
  ],
});

module.exports = router;

