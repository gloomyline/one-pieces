<template>
  <div class="wrapper">
    <main-header :username="username" :avatar="avatar"></main-header>
    <main-menu :menu="menu" :visited="visited" :username="username" :avatar="avatar"></main-menu>
    <div class="content-wrapper">
      <section class="content-header">
        <h1>{{ $route.meta.title }}</h1>
        <ol class="breadcrumb">
          <li>
            <a><i class="fa fa-dashboard"></i> {{title}}</a>
          </li>
          <li class="active">{{ $route.meta.title }}</li>
        </ol>
      </section>
      <div class="routerView"><router-view></router-view></div>
    </div>
  </div>
</template>
<script>
import apiBase from './apiBase';
import MainHeader from './components/MainHeader';
import MainMenu from './components/MainMenu';

export default {
  name: 'app',
  data() {
    return {
      menu: [],
      title: '',
      childTitle: '',
      visited: [],
      username: '',
      avatar: 'http://fenqi-shop.wkdk.cn/images/profile_small.jpg',
    };
  },
  components: {
    MainMenu,
    MainHeader,
  },
  watch: {
    $route: 'getNav',
  },
  mounted() {
    this.$http.get(`${apiBase}menu/mine`)
      .then(response => response.json())
      .then((json) => {
        if (json.status === 'NOLOGIN') {
          window.location.href = '/site/login';
        }
        if (json.status !== 'SUCCESS') {
          return Promise.reject(json.error_message);
        }
        this.menu = json.results;
        this.getNav();
        return {};
      }).catch((e) => {
        this.$message({
          showClose: true,
          message: e,
          type: 'error',
          duration: 0,
        });
      });
    this.$http.get(`${apiBase}shop-admin/basic`)
      .then(response => response.json())
      .then((json) => {
        if (json.status !== 'SUCCESS') {
          return Promise.reject(json.error_message);
        }
        this.username = json.results[0].name;
        return {};
      }).catch((e) => {
        this.$message({
          showClose: true,
          message: e,
          type: 'error',
          duration: 0,
        });
      });
  },
  created() {
    window.addEventListener('hashchange', () => {
      document.body.scrollTop = 0;
      document.documentElement.scrollTop = 0;
    });
  },
  methods: {
    getNav() {
      const path = this.$route.path;
      for (let i = 0; i < this.menu.length; i += 1) {
        for (let j = 0; j < this.menu[i].children.length; j += 1) {
          const url = this.menu[i].children[j].route.split('/#');
          if (String(url[1]) === String(path)) {
            this.visited = [i, j];
            this.title = this.menu[i].title;
            this.childTitle = this.menu[i].children[j].title;
            return;
          }
        }
      }
    },
  },
};
</script>

<style>
.content-wrapper {
  background-color: #fff;
}

.routerView {
  background-color: #fff;
  padding: 10px 15px 15px;
}

.content-header {
  height: 50px;
}

:before,
:after{
  -webkit-box-sizing: content-box;
  -moz-box-sizing: content-box;
  box-sizing: content-box;
}

.el-upload__inner input[type=file], 
.el-upload--text input[type=file],
.el-upload input[type=file] {
  display: none;
}

@media print
{
  .noPrint {
    display: none;
  }
}

@media screen
{
  .printOnly {
    display: none;
  }
}
</style>
