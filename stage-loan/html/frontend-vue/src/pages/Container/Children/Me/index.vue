<!--
@Date:   2017-12-15T16:50:38+08:00
@Last modified time: 2018-02-02T09:47:16+08:00
-->
<template lang="html">
  <div class="page-me">
    <div class="img-wrap"><img :src="bgImg" width="100%" height="100%"></div>
    <header class="header">
      <div class="settings" @click="gotoSettings"><i class="icon-settings"></i></div>
      <!-- <div class="message"><span class="icon" :class="{ 'icon-message': !hasNewMessage, 'icon-message-red': hasNewMessage }"><span class="path1"></span><span class="path2"></span><span class="path3"></span></span></div> -->
    </header>
    <div class="container">
      <p class="user-account">欢迎您，{{ mobileStr }}</p>
      <div class="loan-pannel">
        <h4 class="pannel-title">可用额度：</h4>
        <p class="available-quota">{{ availableQuota }}</p>
        <div class="loan-operation">
          <div class="btn-refund btn" @click="refund"><i class="icon-refund icon"></i><span class="label">我要还款</span></div>
          <div class="btn-lend btn" @click="lend"><i class="icon-loan icon"></i><span class="label">我的借款</span></div>
        </div>
      </div>
      <div class="list-wrap">
        <ul class="loan-list">
          <li class="loan-item">
            <router-link to="/me/auth">
              <p class="icon"><i class="icon-auth-center"></i></p>
              <p class="label">认证中心</p>
            </router-link>
          </li>
          <li class="loan-item">
            <router-link to="/me/help">
              <p class="icon"><i class="icon-light"></i></p>
              <p class="label">帮助中心</p>
            </router-link>
          </li>
          <li class="loan-item">
            <router-link to="/me/us">
              <p class="icon"><i class="icon-about-us"></i></p>
              <p class="label">关于我们</p>
            </router-link>
          </li>
          <li class="loan-item">
            <router-link to="/me/shop">
              <p class="icon"><i class="icon-shop"></i></p>
              <p class="label">商家入驻</p>
            </router-link>
          </li>
          <!-- <li class="loan-item">
            <router-link to="/me/invitation">
              <p class="icon"><i class="icon-invitation"></i></p>
              <p class="label">邀请好友</p>
            </router-link>
          </li> -->
        </ul>
      </div>
    </div>
    <transition name="me-children-fade">
      <router-view class="me-children-container"></router-view>
    </transition>
  </div>
</template>

<script type="text/javascript">
import { mapState, mapActions } from 'vuex'

export default {
  data () {
    return {
      bgImg: require('../../../../assets/imgs/bg-my-self.jpg')
    }
  },
  created () {
    if (!this.mobile) {
      this.fetchMobile()
    }
    this.fetchQuota()
  },
  destroyed () {
  },
  watch: {
  },
  computed: {
    ...mapState({
      mobile: state => state.user.mobile,
      availableQuota: state => state.user.quota.available_quota
    }),
    hasNewMessage () {
      return true
    },
    mobileStr () {
      let strArr = (this.mobile + '').split('')
      strArr.splice(3, 4, '****')
      return strArr.join('')
    }
  },
  methods: {
    ...mapActions({
      fetchMobile: 'user/fetchMobile',
      fetchQuota: 'user/fetchAvailableQuota'
    }),
    gotoSettings () {
      this.$router.push('/me/settings')
    },
    refund () {
      this.$router.push('/me/refund')
    },
    lend () {
      this.$router.push('/me/lend')
    }
  },
  components: {}
}
</script>

<style lang="stylus" scoped>
@import '../../../../common/stylus/mixin.styl'
@import '../../../../common/stylus/variable.styl'

.page-me
  .img-wrap
    absolute top left
    height 100%
    z-index: -1
  .header
    padding-top 8px
    font-size: 30px
    color $color-text-white
    .settings
      display: inline-block
      vertical-align: top
      padding 4px
    .message
      absolute: right top 8px
      padding: 4px
  .container
    margint-top 8px
    .user-account
      text-align: center
      font-size: 18px
      color $color-text-white
    .loan-pannel
      position: relative
      width: 300px
      height: 180px
      margin: 24px auto 0
      border-radius 8px
      border 1px solid $color-border-grey-light
      background-color: $color-white
      box-shadow: 8px 8px 16px $color-shadow-yellow-24
      &:before
        display: block
        content: ' '
        width: 100%
        height: 100%
        absolute: left top
        box-shadow: 8px 8px 16px $color-shadow-yellow-24
        transform: rotate(180deg)
      .pannel-title
        padding: 15px 0 0 15px
        font-size: 16px
        color: $color-text-yellow
      .available-quota
        margin-top: 26px
        text-align: center
        font-size: 39px
        color $color-text-yellow
      .loan-operation
        absolute: left bottom
        width: 100%
        height: 73px
        padding-top: 25px
        background-image: url('../../../../assets/imgs/bg-wave.png')
        background-size: 100% 100%
        background-repeat: no-repeat
        .btn-refund, .btn-lend
          display: inline-block
          width: 49%
          height: 48px
          line-height: 48px
          box-sizing: border-box
          text-align: center
          font-size: 0
          .icon
            vertical-align: middle
            font-size: 30px
            color: $color-text-yellow
          .label
            vertical-align: middle
            font-size: 16px
            color: $color-text-black
        .btn-refund
          border-1px(#f9e4c1, 'right')
    .list-wrap
      padding-top 2px
      .loan-list
        display: flex
        flex-wrap: wrap
        width: 100%
        .loan-item
          width: 125px
          height: 120px
          a
            display: block
            width: 100%
            height: 100%
            padding-top: 45px
            text-align: center
            text-decoration: none
            .icon
              font-size: 30px
              color: $color-text-yellow
            .label
              margin-top: 20px
              font-size: 16px
              color: $color-text-black
  .me-children-container
    position: fixed
    left: 0
    top: 0
    bottom: 0
    width: 100%
    overflow: hidden
    background-color: $color-white-high
    &.me-children-fade-enter-active, &.me-children-fade-leave-active
      transition: all .6s ease
    &.me-children-fade-enter, &.me-children-fade-leave-to
      opacity: 0
</style>
