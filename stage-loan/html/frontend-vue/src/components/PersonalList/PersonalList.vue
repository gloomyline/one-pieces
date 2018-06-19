<template>
  <div class="personal-list">
    <div class="personal-list-wrap">
      <header class="personal-list-header">
        <div class="bg-wrap">
          <img src="./bg_personal_header.png" width="280" height="175" class="bg">
        </div>
        <div class="logo-wrap">
          <span class="icon-logo"></span>
        </div>
        <p class="user-telephone">{{ mobile | telNumFormatter }}</p>
      </header>
      <group class="personal-list-content" gutter="0">
        <cell class="record" title="借还记录" is-link link="/personal/record/lend">
          <i class="icon icon-list" slot="icon"></i>
        </cell>
        <cell class="help" title="帮助中心" is-link link="/personal/help">
          <i class="icon icon-help-book" slot="icon"></i>
        </cell>
        <cell class="msg-center" title="消息中心" is-link link="/personal/msgCenter">
          <i class="icon icon-msg-center" slot="icon"></i>
        </cell>
        <cell class="invitation" title="邀请好友" is-link link="/personal/invitation">
          <i class="icon icon-invitation" slot="icon"></i>
        </cell>
        <cell class="voucher" title="代金券" is-link link="/personal/voucherList">
          <span class="icon icon-voucher" slot="icon"><span class="path1"></span><span class="path2"></span></span>
        </cell>
        <cell class="modify-pwd" title="修改密码" is-link link="/personal/modifyPwd">
          <i class="icon icon-lock-1" slot="icon"></i>
        </cell>
        <!-- <cell class="wechat" title="微信公众号" is-link link="/personal/wechat"> -->
        <cell class="wechat" title="微信公众号" is-link>
          <i class="icon icon-wechat-pub" slot="icon"></i>
          <span class="wechat-id">wukongdai</span>
        </cell>
        <cell class="service" title="联系客服" is-link @click.native="openService">
          <i class="icon icon-customer-service" slot="icon"></i>
        </cell>
        <cell class="us" title="关于我们" is-link link="/personal/us">
          <i class="icon icon-exclamation-point" slot="icon"></i>
        </cell>
        <transition name="router-fade">
          <keep-alive>
            <router-view class="personal-list-container"></router-view>
          </keep-alive>
        </transition>
      </group>
      <footer class="personal-list-footer">
        <x-button class="btn-logout" plain type="primary" action-type="button" @click.native="logout">退出登录</x-button>
      </footer>
    </div>
  </div>
</template>

<script>
import { Group, Cell, XButton } from 'vux'

import Api from '@/api'

import { createNamespacedHelpers } from 'vuex'
const namespace = 'home'
const { mapGetters } = createNamespacedHelpers(namespace)

export default {
  name: 'personalList',
  data () {
    return {
    }
  },
  created () {
  },
  mounted () {},
  computed: {
    ...mapGetters([
      'mobile'
    ])
  },
  methods: {
    async logout () {
      let res = await Api.logout()
      if (res.status === 'SUCCESS') {
        this.$parent.personalListShow = false
        setTimeout(() => {
          this.$router.push('/login') // 强制跳转到登陆界面
        }, 600)
      }
    },
    openService (event) {
      this.$emit('open-service', event)
    }
  },
  filters: {
    telNumFormatter (str) {
      if (!str || str === '') return
      return `${str.substring(0, 3)}*****${str.substring(str.length - 3)}`
    }
  },
  components: {
    Group,
    Cell,
    XButton
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus">
@import '../../common/stylus/mixin.styl'
@import '../../common/stylus/variable.styl'

.personal-list
  .personal-list-wrap
    .personal-list-header
      position relative
      .bg-wrap
        width 100%
        height 100px
        .bg
          width 100%
          height 100%
      .logo-wrap
        position absolute
        top 5px
        left 50%
        width 45px
        height 45px
        margin-left -22.5px
        .icon-logo
          inline-icon(45px, 45px)
          bg-img('./icon_personal_logo')
      .user-telephone
        position absolute
        left 0
        top 52.5px
        width 100%
        height 24px
        line-height 24px
        text-align center
        font-size 15px
        font-weight 200
        color $color-white
    .personal-list-content
      font-size 14px
      color $color-text-black
      .weui-cells
        &:before
          display none
      .weui-cell
        height 45px !important
      .weui-cell__hd
        margin-right 12px
        padding-top 2px
        .icon
          font-size 20px
        .wechat-id
          font-size 14px
          font-weight 200
      .vux-label
        font-size 14px
        color $color-text-black
    .personal-list-footer
      .btn-logout
        width 200px
        height 35px
        margin-top 21px
        font-size 14px
        color $color-text-blue
        border-color $color-blue
        border-radius 17px 17px
        &:active
          color $color-text-blue-lightter
          border-color $color-blue
</style>