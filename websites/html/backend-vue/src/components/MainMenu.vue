<template>
  <aside class="main-sidebar">
    <section class="sidebar">
        <div class="user-panel">
          <div class="pull-left image">
            <img :src="avatar" class="img-circle" alt="User Image">
          </div>
          <div class="pull-left info">
            <p>{{username}}</p>
            <a href="javascript:;"><i class="fa fa-circle text-success"></i> 在线</a>
          </div>
        </div>
        <ul class="sidebar-menu" id="sidebar-menu">
          <li class="header">厦门万匹思网络科技有限公司欢迎你！</li>
          <li class="treeview" :class="{active: index === visited[0]}" v-for="item, index in menu">
            <a href="javascript:;" @click="tab(index)">
              <i class="fa" :class="item.icon"></i> <span>{{item.title}}</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li v-for="items, n in item.children" :class="{active: n === visited[1]}">
                <a @click="jumpUrl(items.route)" href="javascript:;"><i class="fa fa-circle-o"></i>{{items.title}}</a>
              </li>
            </ul>
          </li>
        </ul>
      </section>
  </aside>
</template>

<script>
import $ from 'jquery';
import 'jquery-slimscroll';

export default {
  data() {
    return {};
  },
  props: {
    menu: {
      type: Array,
      default: [],
    },
    visited: {
      type: Array,
      default: [],
    },
    username: {
      type: String,
      default: 'onepieces',
    },
    avatar: {
      type: String,
    },
  },
  mounted() {
    $(() => {
      this.sidebar();
      $(window).resize(() => { this.sidebar(); });
    });
  },
  methods: {
    sidebar() {
      $('.sidebar').slimscroll({
        height: `${$(window).height() - $('.main-header').height()}px`,
        size: '3px',
        color: 'rgba(0,0,0,0.2)',
      });
    },
    tab(index) {
      const $this = $('.sidebar .treeview').eq(index).find('a');
      const checkElement = $this.next('ul');
      const animationSpeed = 500;
      if ((checkElement.is(':visible')) && (!$('body').hasClass('sidebar-collapse'))) {
        checkElement.slideUp(animationSpeed, () => {
          checkElement.removeClass('menu-open');
        });
        checkElement.parent('li').removeClass('active');
      } else if (!checkElement.is(':visible')) {
        const parent = $('.sidebar-menu');
        const ul = parent.find('ul:visible').slideUp(animationSpeed);
        ul.removeClass('menu-open');
        checkElement.slideDown(animationSpeed, () => {
          checkElement.addClass('menu-open');
          parent.find('li.active').removeClass('active');
          $this.parents('.treeview').addClass('active');
        });
      }
    },
    jumpUrl(url) {
      const path = url.split('/#');
      if (path.length === 1) {
        window.location.href = url;
      } else {
        this.$router.push({
          path: path[1],
          query: { page: 1 }, // 一开始会发送2次请求。。。临时解决
        });
      }
    },
  },
};
</script>

<style scoped>
.fa-circle-o {
  font-size: 12px;
}
</style>
