<!--
@Date:   2017-12-19T11:43:17+08:00
@Last modified time: 2018-01-22T10:25:38+08:00
-->
<template lang="html">
  <div class="page-register">
    <r-header class="register-header" :not-has-return="true" :not-has-confirm="true">
      <span class="title" slot="title-text">快速注册</span>
    </r-header>
    <group class="register-inputs" gutter="0">
      <x-input class="mobile" novalidate v-model="mobile" placeholder="请输入手机号码">
        <i class="icon-book" slot="label"></i>
      </x-input>
      <x-input class="code" novalidate v-model="code" placeholder="请输入验证码">
        <i class="icon-msg" slot="label"></i>
        <x-button class="btn-fetch-code" slot="right" type="primary" mini plain @click.native="fetchCode">{{ labelFetchCode }}</x-button>
      </x-input>
      <x-input class="password" :type="pwdType" novalidate v-model="password" placeholder="请输入您的登录密码">
        <i class="icon-lock" slot="label"></i>
        <i class="icon-eye" :class="pwdEyeClass" slot="right" @click="togglePwdType"></i>
      </x-input>
      <x-input class="pwdAagin" :type="pwdAaginType" novalidate v-model="pwdAagin" placeholder="再次输入您的登录密码">
        <i class="icon-lock" slot="label"></i>
        <i class="icon-eye" :class="pwdAaginEyeClass" slot="right" @click="toggleAgainPwdType"></i>
      </x-input>
    </group>
    <footer class="register-footer">
      <div class="link-protocol">
        <div class="icon-wrap" @click="checkProtocol"><i class="icon" :class="{'icon-box-checked': protocolChecked, 'icon-box-unchecked': !protocolChecked}"></i></div>
        <div class="protocol-link">
          <span class="tip">注册即代表同意</span>
          <span class="link" @click="openProtocol">《用户注册服务协议》</span>
        </div>
        <transition name="protocol-fade">
          <page-register-protocol class="page-protocol" ref="protocol" v-show="protocolShow" @close="closeProtocol"></page-register-protocol>
        </transition>
      </div>
      <x-button class="btn-register" :class="{'btn-yellow': isBtnEnabled}" type="primary" action-type="button" @click.native="register">注册</x-button>
      <p class="link-to-login"><router-link to="/login">已有账号，去登陆</router-link></p>
    </footer>
  </div>
</template>

<script>
import RHeader from '@/components/APageHeader/APageHeader'
import { Group, XInput, XButton } from 'vux'
import PageRegisterProtocol from '@/pages/Protocol'

import { createNamespacedHelpers } from 'vuex'
const namespace = 'user/register'
const { mapGetters, mapActions } = createNamespacedHelpers(namespace)
import sessionStorage from '@/common/js/sessionStorage'

export default {
  data () {
    return {
      pwdType: 'password',
      pwdAaginType: 'password',
      protocolShow: false
    }
  },
  destroyed () {
    this.$store.commit(`${namespace}/CLEAR_TIME_ID_FOR_REGISTER`)
  },
  beforeRouteEnter (to, from, next) {
    if (sessionStorage.loadLoginStatus().isLogin) { // forbid user go to page login manually
      next(false)
    } else {
      next()
    }
  },
  watch: {
    'validationErr' () {
      let err = this.validationErr.replace(/\d+$/, '')
      if (err !== '') {
        this._alertShow(err)
      }
    },
    'confirmMsg' () {
      let msg = this.confirmMsg.replace(/\d+$/, '')
      if (msg !== '') {
        this._alertShow(msg, () => {
          this.$router.push('/home')
        })
      }
    }
  },
  computed: {
    ...mapGetters([
      'form',
      'isCodeFetched',
      'coolingTime',
      'isCoolingDown',
      'validationErr',
      'confirmMsg'
    ]),
    mobile: {
      get () {
        return this.form.mobile
      },
      set (num) {
        this.updateInput({ key: 'mobile', val: num })
      }
    },
    code: {
      get () {
        return this.form.code
      },
      set (num) {
        this.updateInput({ key: 'code', val: num })
      }
    },
    password: {
      get () {
        return this.form.password
      },
      set (num) {
        this.updateInput({ key: 'password', val: num })
      }
    },
    pwdAagin: {
      get () {
        return this.form.passwordAagin
      },
      set (num) {
        this.updateInput({ key: 'pwdAgin', val: num })
      }
    },
    labelFetchCode () {
      return !this.isCodeFetched
        ? '获取验证码' : this.isCoolingDown
        ? '重新发送' : `${this.coolingTime}后重新发送`
    },
    pwdEyeClass () {
      return this.pwdType === 'password' ? 'icon-eye-show' : 'icon-eye-hide'
    },
    pwdAaginEyeClass () {
      return this.pwdAaginType === 'password' ? 'icon-eye-show' : 'icon-eye-hide'
    },
    protocolChecked () {
      return this.form.isProtocolChecked
    },
    isBtnEnabled () {
      for (let k in this.form) {
        let v = this.form[k]
        if (v === '') {
          return false
        }
      }
      return true
    }
  },
  methods: {
    ...mapActions([
      'fetchCode',
      'register'
    ]),
    updateInput ({key, val}) {
      this.$store.commit(`${namespace}/UPDATE_INPUT_FOR_REGISTER`, { key, val })
    },
    togglePwdType () {
      this.pwdType = this.pwdType === 'password' ? 'text' : 'password'
    },
    toggleAgainPwdType () {
      this.pwdAaginType = this.pwdAaginType === 'password' ? 'text' : 'password'
    },
    checkProtocol () {
      this.$store.commit(`${namespace}/CHECK_REGISTER_PROTOCOL`)
    },
    openProtocol () {
      this.protocolShow = true
      this.$refs.protocol.show()
    },
    closeProtocol () {
      this.protocolShow = false
    }
  },
  components: {
    Group,
    XInput,
    XButton,
    RHeader,
    PageRegisterProtocol
  }
}
</script>

<style lang="stylus">
@import '../../common/stylus/mixin.styl'
@import '../../common/stylus/variable.styl'

  .page-register
    background-color: $color-white-high
    .register-inputs
      .weui-cells
        .weui-cell
          padding-left: 20px
          .weui-cell__hd
            margin-right: 20px
            i[class^="icon-"]
              font-size: 20px
              color: $color-text-yellow
          .weui-cell__bd
            input
              font-size: 16px
              color: $color-text-black
          .weui-cell__ft
            .btn-fetch-code
              width: 100px
              height: 36px
              padding: 0
              font-size: 16px
              color: $color-text-grey-higher
              border-radius: 8px 8px
              border-color: $color-border-grey
            .icon-eye
              vertical-align: middle
              font-size: 20px
              color: $color-text-yellow
    .register-footer
      margin-top: 10px
      .link-protocol
        padding-top 20px
        .icon-wrap
          display: inline-block
          vertical-align: top
          padding-left: 20px
          box-sizing: border-box
          font-size: 20px
          color: $color-text-yellow
        .protocol-link
          display: inline-block
          vertical-align: top
          font-size: 0
          .tip
            font-size: 16px
            color: $color-text-black
          .link
            font-size: 16px
            color: $color-text-yellow
        .page-protocol
          fixed: top left
          width: 100%
          overflow: hidden
          background-color: $color-white-high
          z-index: 10
          &.protocol-fade-enter-active, &.protocol-fade-leave-active
            transition: all 1.2s ease
          &.protocol-fade-enter, &.protocol-fade-leave-to
            opacity: 0
      .btn-register
        width: 300px
        height: 40px
        margin-top: 30px
        font-size: 18px
        color: $color-text-white
        border-radius: 20px 20px
        background-color: $color-grey-higher
        border-color: $color-border-grey
      .link-to-login
        margin-top: 24px
        text-align: center
        a
          display: inline-block
          width: 160px
          height: 32px
          line-height: 32px
          font-size: 16px
          color: $color-text-yellow
</style>
