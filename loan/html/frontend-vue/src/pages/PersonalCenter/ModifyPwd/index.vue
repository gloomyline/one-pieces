<template>
  <div class="page-modify-pwd">
    <m-header><span slot="title-text">修改密码</span><span slot="btn-confirm"></span></m-header>
    <group gutter="0">
      <cell class="login-pwd" title="登录密码" is-link link="/personal/modifyPwd/login"></cell>
      <!-- <cell class="pay-pwd" title="支付密码" is-link link="/personal/modifyPwd/pay"></cell> -->
    </group>
    <transition name="router-fade">
      <keep-alive>
        <router-view class="modify-pwd-container"></router-view>
      </keep-alive>
    </transition>
  </div>
</template>

<script>
import MHeader from '@/components/APageHeader/APageHeader'
import { Group, Cell } from 'vux'

export default {
  name: 'modifyPwd',
  data () {
    return {}
  },
  mounted () {},
  computed: {},
  methods: {},
  components: {
    MHeader,
    Group,
    Cell
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus" scoped>
@import '../../../common/stylus/variable.styl'

  .page-modify-pwd
    .login-pwd
      border-bottom 1px solid $color-grey-higher1
    .modify-pwd-container
      position fixed
      left 0
      top 0
      bottom 0
      width 100%
      background $color-white
      overflow hidden
      &.router-fade-enter-active, &.router-fade-leave-active
        transition all .6s ease
      &.router-fade-enter, &.router-fade-leave-to
        opacity 0
</style>