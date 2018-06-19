<!--
@Date:   2017-12-19T11:43:08+08:00
@Last modified time: 2018-01-31T11:28:55+08:00
-->
<template lang="html">
  <div class="page-login">
    <header class="login-header">
      <div class="logo-wrap"><i class="icon-logo"></i></div>
      <p class="title">悟空分期</p>
    </header>
    <group class="login-inputs" gutter="0">
      <x-input class="account" ref="account" novalidate type="tel" placeholder="请输入您的账号" :show-clear="false" v-model="account">
        <i class="icon-book" slot="label"></i>
        <span class="icon-a-right" slot="right">
          <i class="icon-cross" v-show="!!account" @click="clear('account')"></i>
        </span>
      </x-input>
      <x-input class="pwd" ref="pwd" novalidate :type="pwdType" placeholder="请输入您的密码" :show-clear="false" v-model="pwd">
        <i class="icon-lock" slot="label"></i>
        <span class="icon-right" slot="right">
          <i class="icon-cross" v-show="!!pwd" @click="clear('pwd')"></i>
          <i class="icon-eye" :class="eyeClass" @click="togglePwdType"></i>
        </span>
      </x-input>
    </group>
    <footer class="login-footer">
      <div class="links">
        <router-link class="reset-pwd" to="/resetPwd">忘记密码？</router-link>
        <router-link class="register" to="/register">快速注册</router-link>
      </div>
      <x-button class="btn-login btn-yellow" type="primary" action-type="button" @click.native="login">登录</x-button>
    </footer>
  </div>
</template>

<script>
import { Group, XInput, XButton } from 'vux'

import { mapState, createNamespacedHelpers } from 'vuex'
const namespace = 'user/login'
const { mapGetters, mapActions } = createNamespacedHelpers(namespace)

import sessionStorage from '@/common/js/sessionStorage'

export default {
  data () {
    return {
      pwdType: 'password'
    }
  },
  created () {
  },
  destroyed () {
    this.$store.commit(`${namespace}/CLEAR_LOGIN_INPUTS`)
  },
  beforeRouteEnter (to, from, next) {
    if (sessionStorage.loadLoginStatus().isLogin) { // forbid user go to page login manually
      next(false)
    } else {
      next()
    }
  },
  watch: {
    'isLogined' (newVal, oldVal) {
      /* eslint-disable no-extra-boolean-cast */
      if (Boolean(newVal)) { // login sucees
        this._alertShow('登录成功', () => {
          this.$store.commit('user/LOGIN_SUCCESS', { mobile: this.mobile })
          this.$router.push('/home')
        })
      }
    },
    'validationErr' () {
      if (this.validationErr.replace(/[\d\s]+/g, '') !== '') {
        this._alertShow(this.validationErr.replace(/\d+$/, ''))
      }
    }
  },
  computed: {
    ...mapState({ isLogined: state => state.user.isLogined }),
    ...mapGetters([ 'mobile', 'password', 'validationErr' ]),
    account: {
      get () {
        return this.mobile
      },
      set (acc) {
        this.$store.commit(`${namespace}/UPDATE_FOR_LOGIN`, { key: 'mobile', val: acc })
      }
    },
    pwd: {
      get () {
        return this.password
      },
      set (pwd) {
        this.$store.commit(`${namespace}/UPDATE_FOR_LOGIN`, { key: 'password', val: pwd })
      }
    },
    eyeClass () {
      return this.pwdType === 'password' ? 'icon-eye-show' : 'icon-eye-hide'
    }
  },
  methods: {
    ...mapActions([ 'login' ]),
    clear (type) {
      if (type === 'pwd') {
        this.$refs.pwd.reset()
      } else {
        this.$refs.account.reset()
      }
    },
    togglePwdType () {
      this.pwdType = this.pwdType === 'password' ? 'text' : 'password'
    }
  },
  components: {
    Group,
    XInput,
    XButton
  }
}
</script>

<style lang="stylus">
@import '../../common/stylus/mixin.styl'
@import '../../common/stylus/variable.styl'

.page-login
  .login-header
    padding-top: 80px
    text-align: center
    .logo-wrap
      color: $color-text-yellow
      .icon-logo
        font-size: 70px
    .title
      margin-top: 10px
      font-size: 24px
      color: $color-text-black
  .login-inputs
    margin-top: 48px
    .weui-cells
      &:before, &:after
        display: none
      .weui-cell
        height: 60px !important
        padding-left: 40px
        padding-right: 40px
        .weui-cell__hd
          i[class^='icon']
            font-size 20px
            color $color-text-yellow
        .weui-cell__bd
          padding-left: 16px
          input
            font-size: 18px
        .weui-cell__ft
          .icon-a-right
            display: inline-block
            vertical-align: middle
            color: $color-text-yellow
            .icon-cross
              font-size: 20px
        &:before
          width: 300px
          left: 50%
          margin-left: -150px
      .pwd
        border-1px($color-border-grey-light, 'bottom')
        &:after
          width: 300px
          left: 50%
          margin-left: -150px
        .weui-cell__ft
          .icon-right
            display: inline-block
            vertical-align: middle
            font-size: 0
            color: $color-text-yellow
            .icon-eye, .icon-cross
              font-size: 20px
            .icon-cross
              margin-right: 12px
  .login-footer
    margin-top: 12px
    .links
      height: 32px
      padding: 0 40px 0 40px
      box-sizing: border-box
      font-size: 16px
      .reset-pwd, .register
        display: inline-block
        width: 49%
        color: $color-text-yellow
        text-decoration: none
      .register
        text-align: right
    .btn-login
      margin-top: 54px
      box-shadow: 6px 6px 8px $color-shadow-yellow-24
      &:active
        box-shadow: inset 6px 6px 8px $color-shadow-black-24
</style>
