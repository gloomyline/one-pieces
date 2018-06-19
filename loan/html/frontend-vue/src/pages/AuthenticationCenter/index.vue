<template>
  <div class="page-authentication">
    <a-header @go-back="goBack" @confirm="confirm" not-go-back>
      <span slot="title-text">认证中心</span>
      <span slot="btn-confirm"></span>
    </a-header>
    <group class="authentication-list" gutter="0">
      <cell class="authen-item identity" link="/authentication/identity" is-link>
        <span class="icon icon-identity" slot="icon"></span>
        <span class="title" slot="title">身份认证</span>
        <span class="icon-required" slot="after-title"></span>
        <span class="value" :class="{'text-authen': !!authenStatus.identity}">{{authenStatus.identity | authenStatusFormat}}</span>
      </cell>  
      <cell class="authen-item personal-info" link="/authentication/personalInfo" is-link>
        <span class="icon icon-card" slot="icon"></span>
        <span class="title" slot="title">个人信息</span>
        <span class="icon-required" slot="after-title"></span>
        <span class="value" :class="{'text-authen': !!authenStatus.personal}">{{authenStatus.personal |authenStatusFormat}}</span>
      </cell>
      <cell class="authen-item bank-card" link="/authentication/bankcard" is-link>
        <span class="icon icon-bankcard" slot="icon"></span>
        <span class="title" slot="title">银行卡</span>
        <span class="icon-required" slot="after-title"></span>
        <span class="value" :class="{'text-authen': !!authenStatus.bankcard}">{{authenStatus.bankcard |authenStatusFormat}}</span>
      </cell>
      <cell class="authen-item sesame" link="/authentication/sesame" is-link>
        <span class="icon icon-sesame" slot="icon"></span>
        <span class="title" slot="title">芝麻信用</span>
        <span class="icon-required" slot="after-title"></span>
        <span class="value" :class="{'text-authen': !!authenStatus.sesame}">{{authenStatus.sesame |authenStatusFormat}}</span>
      </cell>
      <cell class="authen-item telephone" link="/authentication/telephone" is-link>
        <span class="icon icon-mobile" slot="icon"></span>
        <span class="title" slot="title">手机认证</span>
        <span class="icon-required" slot="after-title"></span>
        <span class="value" :class="{'text-authen': !!authenStatus.telephone}">{{authenStatus.telephone |authenStatusFormat}}</span>
      </cell>
      <cell class="authen-item upper-limit" link="/authentication/upperLimit" is-link>
        <span class="icon icon-get-rmb" slot="icon"></span>
        <span class="title" slot="title">提升额度</span>
        <span class="icon-not-required" slot="after-title"></span>
      </cell>
    </group>
    <transition name="authen-router-fade" mode="out-in">
      <keep-alive>
        <router-view class="authentication-container"></router-view>
      </keep-alive>
    </transition>
  </div>
</template>

<script>
import AHeader from '@/components/APageHeader/APageHeader'
import { Group, Cell } from 'vux'

import { mapGetters, mapActions } from 'vuex'
import { getSesameAuth } from '@/common/js/localStorage'

export default {
  name: 'pageAuthentication',
  data () {
    return {
      sesameAuth: 0
    }
  },
  created () {
    this.fetchAuthenStatus()
  },
  mounted () {
    if (getSesameAuth(this.user)) {
      this.$store.commit('authenticationCenter/UPDATE_AUTHENTICATION_CENTER_BY_NAME', {
        authenStatusName: 'sesame',
        status: 1
      })
    } else {
      this.$store.commit('authenticationCenter/UPDATE_AUTHENTICATION_CENTER_BY_NAME', {
        authenStatusName: 'sesame',
        status: 0
      })
    }
  },
  computed: {
    ...mapGetters({
      user: 'home/mobile',
      authenStatus: 'authenticationCenter/authenStatus'
    })
  },
  methods: {
    ...mapActions({
      fetchAuthenStatus: 'authenticationCenter/fetchAuthenStatus'
    }),
    goBack () {
      this.$router.push('/')
    },
    confirm () {
      console.log('open page deposit')
    }
  },
  filters: {
    authenStatusFormat (val) {
      return val === 0 ? '未填写' : '已认证'
    }
  },
  components: {
    AHeader,
    Group,
    Cell
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus">
@import '../../common/stylus/mixin.styl'
@import '../../common/stylus/variable.styl'

.page-authentication
  position fixed
  top 0
  left 0
  bottom 0
  width 100%
  .authentication-list
    .authen-item
      height 55px
      .weui-cell__hd 
        margin-right 8px /* icon 与 title 间距 */
        padding-top 6px
        .icon
          font-size 26px
          color $color-text-blue
      .vux-cell-bd
        .vux-label
          display inline-block
          vertical-align top
          .title
            font-size 15px
            color $color-text-black
        .icon-required /* 标题右边必填图标 */
          margin 3px 0 0 10px
          inline-icon(25px, 15px)
          bg-img('./icon-required')
        .icon-not-required /* 标题右边选填图标 */
          margin 5px 0 0 10px
          inline-icon(25px, 15px)
          bg-img('./icon-not-required')
      .weui-cell__ft
        margin-right 8px    
        .value
          font-size 12px
          color $color-text-grey-higher
          &.text-authen
            color $color-text-blue            
  .authentication-container
    position fixed
    top 0
    left 0
    bottom 0
    width 100%
    background #fff
    &.authen-router-fade-enter-active, &.authen-router-fade-leave-active
      transition all .6s ease
    &.authen-router-fade-enter, &.authen-router-fade-leave-to
      opacity 0
</style>