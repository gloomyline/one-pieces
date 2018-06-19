<!--
@Date:   2017-12-19T13:48:08+08:00
@Last modified time: 2018-01-05T16:42:10+08:00
-->
<template lang="html">
  <div class="page-container">
    <transition name="container-fade" mode="out-in">
      <!-- <keep-alive> -->
      <router-view class="container-router-container"></router-view>
      <!-- </keep-alive> -->
    </transition>
    <tabbar ref="tabbar" v-model="selectIndex" class="tabbar" icon-class="icon">
      <tabbar-item link="/home">
        <span slot="icon"><i class="icon-home"></i></span>
        <span slot="label">首页</span>
      </tabbar-item>
      <tabbar-item link="/mall">
        <span slot="icon"><i class="icon-shopping-cart"></i></span>
        <span slot="label">商城</span>
      </tabbar-item>
      <tabbar-item link="/me">
        <span slot="icon"><i class="icon-my-self"></i></span>
        <span slot="label">我的</span>
      </tabbar-item>
    </tabbar>
  </div>
</template>

<script>
import { Tabbar, TabbarItem } from 'vux'
import containerRoute from '@/router/container/children'

import { createNamespacedHelpers } from 'vuex'
const namespace = 'container'
const { mapGetters } = createNamespacedHelpers(namespace)

export default {
  data () {
    return {
      selectIndex: 0
    }
  },
  created () {
    let routeName = this.$route.name
    let routeArr = containerRoute.slice() // ensure this is an Array
    for (let i = 0; i < routeArr.length; ++i) {
      let item = routeArr[i]
      if (item.name === routeName) {
        this.selectIndex = i
        break
      }
    }
  },
  mounted () {
  },
  watch: {
    'isTabbarHide' (newVal, oldVal) {
      let tabbarStyle = this.$refs.tabbar.$el.style
      tabbarStyle.zIndex = newVal ? '-1' : '1'
    }
  },
  computed: {
    ...mapGetters(['isTabbarHide'])
  },
  components: {
    Tabbar,
    TabbarItem
  }
}
</script>

<style lang="stylus">
@import '../../common/stylus/mixin.styl'
@import '../../common/stylus/variable.styl'

.page-container
  .container-router-container
    position fixed
    left 0
    top 0
    bottom 0
    width 100%
    overflow hidden
    &.container-fade-enter-active, &.container-fade-leave-active
      transition all .2s ease
    &.container-fade-enter, &.container-fade-leave-to
      opacity 0
  .tabbar
    position: fixed
    z-index: 10
    .weui-tabbar__item
      text-decoration none
      color $color-text-black-light
      .icon
        font-size 30px
      .weui-tabbar__label
        font-size 14px
        color $color-text-black-light
      &.weui-bar__item_on
        .icon, .weui-tabbar__label
          color $color-text-yellow
</style>
