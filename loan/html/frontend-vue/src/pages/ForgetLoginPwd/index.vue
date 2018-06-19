<template>
  <div class="page-forget-login-pwd">
    <f-p-header><span slot="title-text">忘记登录密码</span><span slot="btn-confirm"></span></f-p-header>
    <group class="form-section" gutter="0">
      <x-input class="phone" v-model="phone" placeholder="手机号码">
        <i class="icon-mobile icon" slot="label"></i>
      </x-input>
      <x-input class="verification" v-model="verification" :show-clear="false" placeholder="短信验证码">
        <i class="icon-msg-verification icon" slot="label"></i>
        <div class="vercatFetchTimer" slot="right">
          <x-button class="btn-fetch-verification" plain type="mini" action-type="button" @click.native="fetchVerificationHanlder">{{labelFetchVerification}}</x-button>
        </div>
      </x-input>
      <x-input class="reset-pwd" :type="pwdType" v-model="resetingPwd" placeholder="重置6~15位新密码">
        <i class="icon-lock icon" slot="label"></i>
        <div class="icon-eye-wrap" slot="right" @click="togglePwdShow"><span class="icon" :class="{'icon-eye-hide': !isPwdHide, 'icon-eye-show': isPwdHide}"></span></div>
      </x-input>
    </group>
    <x-button class="btn-confirm-reset btn-blue" @click.native="confirmResetPwd">确认重置密码</x-button>  
  </div>
</template>

<script>
import FPHeader from '@/components/APageHeader/APageHeader'
import { Group, XInput, XButton } from 'vux'

import { createNamespacedHelpers } from 'vuex'
const namespace = 'forgetLoginPwd'
const { mapGetters, mapActions } = createNamespacedHelpers(namespace)

/* eslint-disable no-unused-vars */
import { isPhoneNumber, isPassword } from '@/common/js/utils'

export default {
  name: 'pageForgetLoginPwd',
  data () {
    return {
      pwdType: 'password',
      isPwdHide: true
    }
  },
  created () {
  },
  mounted () {},
  computed: {
    ...mapGetters([
      'form', 'isVerificationFetched', 'coolingTime', 'confirmMsg'
    ]),
    phone: {
      get () {
        return this.form.phone
      },
      set (phone) {
        this.$store.commit(`${namespace}/UPDATE_PHONE`, {phone})
      }
    },
    verification: {
      get () {
        return this.form.verification
      },
      set (verification) {
        this.$store.commit(`${namespace}/UPDATE_VERIFICATION`, {verification})
      }
    },
    resetingPwd: {
      get () {
        return this.form.resetingPwd
      },
      set (resetingPwd) {
        this.$store.commit(`${namespace}/UPDATE_RESET_PWD`, {resetingPwd})
      }
    },
    labelFetchVerification () {
      return this.isVerificationFetched ? `${this.coolingTime}s` : '发送验证码'
    }
  },
  watch: {
    'confirmMsg' () {
      this._alertShow(this.confirmMsg.replace(/(\d+)/g, ''), () => {
        this.$router.push('/login')
      })
    }
  },
  methods: {
    ...mapActions([
      'fetchVerification', 'resetPwd'
    ]),
    togglePwdShow () {
      this.pwdType = (this.pwdType === 'password') ? 'text' : 'password'
      this.isPwdHide = !this.isPwdHide
    },
    localValidate () {
      if (this.verification.trim() === '') {
        this._alertShow('请输入手机短信验证码！')
        return false
      }

      if (this.resetingPwd.trim() === '') {
        this._alertShow('请输入重置密码！')
        return false
      }

      let result = isPassword(this.resetingPwd)
      if (!result.isValid) {
        this._alertShow(result.errMsg)
        return false
      }

      return true
    },
    confirmResetPwd () {
      if (!this.localValidate()) return
      this.resetPwd()
    },
    fetchVerificationHanlder () {
      if (this.isVerificationFetched) return
      if (this.phone.trim() === '') {
        this._alertShow('请输入手机号获取验证码！')
        return
      }
      if (!isPhoneNumber(this.phone)) {
        this._alertShow('请输入合法的手机号！')
      }

      this.fetchVerification({mobile: this.phone})
    }
  },
  components: {
    FPHeader,
    Group,
    XInput,
    XButton
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus">
@import '../../common/stylus/mixin.styl'
@import '../../common/stylus/variable.styl'

.page-forget-login-pwd
  .form-section
    .phone
      .weui-cell__hd
        margin-left -4px
      .icon-mobile
        font-size 24px
    .weui-cell__hd
      margin-right 30px
    .icon
      font-size 20px
    .verification
      .btn-fetch-verification
        width 100px
        height 35px
        font-size 12px
        color $color-text-blue
        border 1px solid $color-blue
        border-radius 18px
        background-color transparent
        &:active
          color $color-text-blue-lightter
          background-color transparent
    .reset-pwd
      .icon-eye-wrap
        display inline-block
        vertical-align middle
        padding 6px
        color $color-text-blue
  .btn-confirm-reset
    margin-top 40px
</style>