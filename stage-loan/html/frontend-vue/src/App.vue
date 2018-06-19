<!--
@Date:   2017-12-14T17:05:37+08:00
@Last modified time: 2017-12-19T15:13:31+08:00
-->
<template>
  <div id="app">
    <div v-transfer-dom>
      <loading :show="isLoading" :text="loadingText"></loading>
    </div>
    <transition name="router-fade" mode="out-in">
      <!-- <keep-alive> -->
      <router-view class="router-container"></router-view>
      <!-- </keep-alive> -->
    </transition>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import { Loading, TransferDomDirective as TransferDom, Tabbar, TabbarItem } from 'vux'

export default {
  name: 'app',
  data () {
    return {
      loadingText: 'loading'
    }
  },
  created () {
  },
  computed: {
    ...mapGetters([
      'isLoading'
    ])
  },
  methods: {
  },
  directives: {
    TransferDom
  },
  components: {
    Loading,
    Tabbar,
    TabbarItem
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus">
@import './common/stylus/mixin.styl'
@import './common/stylus/variable.styl'

#app
  font-family 'Avenir', Helvetica, Arial, sans-serif
  -webkit-font-smoothing antialiased
  -moz-osx-font-smoothing grayscale
  color #2c3e50
  .router-container
    position fixed
    left 0
    top 0
    bottom 0
    width 100%
    overflow hidden
    background-color $color-white-high
    &.router-fade-enter-active, &.router-fade-leave-active
      transition all .2s ease
    &.router-fade-enter, &.router-fade-leave-active
      opacity 0
</style>
