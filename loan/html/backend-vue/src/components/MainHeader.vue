<template>
  <header class="main-header">
    <a class="logo">
      <span class="logo-mini"><b>W</b>kd</span>
      <span class="logo-lg"><b>悟空贷</b></span>
    </a>
    <nav class="navbar navbar-static-top">
      <a href="javascript:;" @click="sidebarToggle" class="sidebar-toggle"> </a>
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <li class="dropdown user user-menu">
            <a  class="dropdown-toggle">
              <img :src="avatar" class="user-image" alt="User Image">
              <span class="hidden-xs">{{username}}</span>
            </a>
          </li>
          <li>
            <a @click="loginOut" href="javascript:;"><i class="fa fa-power-off"></i></a>
          </li>
        </ul>
      </div>
    </nav>
  </header>
</template>

<script>
export default {
  data() {
    return {};
  },
  props: {
    username: {
      type: String,
      default: 'wkdk',
    },
    avatar: {
      type: String,
    },
  },
  methods: {
    loginOut() {
      this.$confirm('确定退出?', '提示', {
        type: 'warning',
      }).then(() => {
        window.location.href = '/site/logout';
      }).catch(() => {});
    },
    sidebarToggle() {
      const obj = document.getElementsByTagName('body')[0];
      const clsArr = obj.className.split(' ');
      obj.className = clsArr.length !== 4 ? `${obj.className} sidebar-collapse` : `${clsArr[0]} ${clsArr[1]} ${clsArr[2]}`;
    },
  },
};
</script>
<style></style>
