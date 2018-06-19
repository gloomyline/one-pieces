<!--
@Date:   2018-01-02T14:30:29+08:00
@Last modified time: 2018-02-05T15:50:44+08:00
-->
<template>
  <div class="page-telephone">
    <t-header ref="header">
      <span slot="title-text" :not-go-back="cannotGoBack">手机认证</span>
      <span slot="btn-confirm"></span>
    </t-header>
    <group class="input-area" gutter="0">
      <cell class="phone-number" title="手机号" :value="phoneNumber" value-align="right"></cell>
      <x-input class="service-pwd" :type="pwdType" title="服务密码" v-model="servicePwd" placeholder="手机运营商服务密码">
        <div class="icon-eye-wrap" slot="right" @click="togglePwdShow"><span class="icon-eye" :class="{'icon-eye-hide': !isPwdHide, 'icon-eye-show': isPwdHide}"></span></div>
      </x-input>
      <cell class="forget-pwd" title="忘记密码？" @click.native="showPageForget"></cell>
    </group>
    <x-button class="btn-submit" type="primary" action-type="button" :show-loading="isSubmitPending" @click.native="submit">立即提交</x-button>
    <transition name="router-fade" mode="out-in">
      <keep-alive>
        <router-view class="router-view-container"></router-view>
      </keep-alive>
    </transition>
    <transition name="loading-fade">
      <div class="loading-wrap" v-show="isLoading">
        <loading :process-time="processTime" ref="loading"></loading>
      </div>
    </transition>
    <transition name="popup">
      <div class="popup-wrap" v-show="isPopup">
        <group class="popup-box" gutter="0">
          <h3 class="popup-title">请输入运营商发送的手机短信验证码</h3>
          <x-input class="sms-code-input" title="短信验证码" v-model="smsCode" ref="smsCodeInput"></x-input>
          <XButton class="btn-confirm-smscode btn-blue" type="primary" action-type="button" @click.native="confirm">确认</XButton>
        </group>
      </div>
    </transition>
    <transition name="fade">
      <page-submit-success v-show="pageSubmitSuccessShow"></page-submit-success>
    </transition>
  </div>
</template>

<script>
import THeader from '@/components/APageHeader/APageHeader'
import { Group, Cell, XInput, XButton } from 'vux'
import PageForgetPwd from './ForgetPwd'
import PageSubmitSuccess from './SubmitSuccess'
import Loading from '@/components/Loading/Loading'

import Api from '@/api'

import { mapState, createNamespacedHelpers } from 'vuex'
const namespace = 'user'
const { mapActions } = createNamespacedHelpers(namespace)

export default {
  name: 'pageTelephone',
  data () {
    return {
      servicePwd: '',
      isLoading: false,
      cannotGoBack: false,
      processTime: 200,
      isPopup: false,
      smsCode: '',
      pageSubmitSuccessShow: false,
      isPwdHide: true,
      pwdType: 'password',
      isBtnSubmitEnabled: true,
      isSubmitPending: false
    }
  },
  created () {
  },
  mounted () {
    if (this.authenStatus.telephone === 1) {
      this.pageSubmitSuccessShow = true
    }
    this.btnReturn = this.$refs.header.$el.querySelector('.btn-return')
  },
  activated () {
    this.fetchMobile()
  },
  watch: {
    'cannotGoBack' (newVal, oldVal) {
      if (newVal) {
        this.btnReturn.style.display = 'none'
      } else {
        this.btnReturn.style.display = 'block'
      }
    }
  },
  computed: {
    ...mapState({
      'authenStatus': state => state.authenticationCenter.authenticatedStatus,
      'phoneNumber': state => state.user.mobile
    })
  },
  methods: {
    ...mapActions([
      'fetchMobile'
    ]),
    togglePwdShow () {
      this.pwdType = this.isPwdHide ? 'text' : 'password'
      this.isPwdHide = !this.isPwdHide
    },
    startLoading () {
      // make the return btn disabled
      this.cannotGoBack = true
      this.isLoading = true
      this.$refs.loading.start()
    },
    stopLoading () {
      // make the return btn enabled
      this.cannotGoBack = false
      this.$refs.loading.stop()
      this.isLoading = false
      this.pageSubmitSuccessShow = true
    },
    async confirm () {
      let res = await Api.authenticatePhoneVerification({smscode: this.smsCode})
      if (res.status === 'SUCCESS') {
        this.isPopup = false
        let _res = await Api.authenticatePhoneContinue()
        if (_res.status === 'SUCCESS') {      // authen success
          this.stopLoading()
          this.$store.commit('authenticationCenter/UPDATE_AUTHENTICATION_CENTER_BY_NAME', {
            authenStatusName: 'telephone',
            status: 1
          })
        } else {                              // authen fail, popup error message
          if (res.error_message === '0006') {
            this.isPopup = true
          } else {
            this._alertShow(_res.error_message, () => {
              this.cannotGoBack = false
              this.$refs.loading.stop()
              this.isLoading = false
            })
          }
        }
        this.isBtnSubmitEnabled = true
      }
    },
    async submit () {
      if (!this.servicePwd.trim()) {
        this._alertShow('请输入服务密码')
        return
      }

      if (!this.isBtnSubmitEnabled) {
        this._alertShow('请求正在处理中，请耐心等待！')
        return
      }

      // disable the submit btn and show tip to users when the last request is not be responded
      this.isBtnSubmitEnabled = false

      // enable show loading in submit button
      this.isSubmitPending = true

      // send request to server
      let res = await Api.authenticatePhone({servicecode: this.servicePwd})
      if (res.status === 'SUCCESS') {
        // start to query phone data, start animation for query in web at the moment
        this.startLoading()
        // if 'error_message' equal '0006', popup smscode input box
        if (res.error_message === '0006') {
          this.isPopup = true
          this.$nextTick(() => {
            this.$refs.smsCodeInput.$el.querySelector('.weui-input').focus()
          })
        }
      } else {
        this._alertShow(res.error_message)
        this.isBtnSubmitEnabled = true
      }
      this.isSubmitPending = false
    },
    showPageForget () {
      this.$router.push('/authentication/telephone/forgetPwd')
    }
  },
  components: {
    THeader,
    Group,
    Cell,
    XInput,
    XButton,
    PageForgetPwd,
    PageSubmitSuccess,
    Loading
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus">
@import '../../../../../../../../common/stylus/variable.styl'
@import '../../../../../../../../common/stylus/mixin.styl'

.page-telephone
  .input-area
    .weui-cells
      &:after
        display none
    .phone-number
      .vux-label
        font-size 15px
        color $color-text-black
    .service-pwd
      .weui-label
        font-size 15px
        color $color-text-black
      .weui-cell__bd > input
        text-align right
        font-size 15px
        color $color-text-black
      .icon-eye-wrap
        display inline-block
        vertical-align middle
        padding 8px
        font-size 24px
        color $color-text-yellow
    .forget-pwd
      height 44px !important
      &:after
        display none
      .vux-cell-bd
        text-align right
        font-size 13px
        color $color-text-yellow
  .btn-submit
    width 300px
    height 35px
    margin 57px auto 0
    font-size 14px
    color $color-text-white
    letter-spacing 2px
  .router-view-container
    &.router-fade-active, &.router-fade-leave-active
      transition all .6s ease
    &.router-fade-enter, &.router-fade-leave-to
      opacity 0
  .page-submit-success
    &.fade-enter-active, &.fade-leave-active
      transition all .6s ease
    &.fade-enter, &.fade-leave-to
      opacity 0
  .loading-wrap
    position fixed
    left 0
    top 55px
    bottom 0
    width 100%
    background-color $color-white
    z-index 33
    overflow hidden
    &.loading-fade-enter-active, &.loading-fade-leave-active
      transition all .6s ease
    &.loading-fade-enter, &.loading-fade-leave-to
      opacity 0
    .loading
      margin 90px auto 0
  .popup-wrap
    position fixed
    left 0
    top 0
    bottom 0
    width 100%
    background transparent
    z-index 44
    overflow hidden
    &.popup-enter-acitve, &.popup-leave-active
      transition all .6s ease
    &.popup-enter, &.popup-leave-to
      opacity 0
    .popup-box
      width 90%
      margin 160px auto 0
      box-shadow 3px 5px 5px rgba(0, 0, 0, .4)
      .weui-cells
        padding 15px 0
        .sms-code-input
          width 90%
          margin 8px auto 8px
          border 1px solid $color-grey-higher
          border-radius 16px 16px
        .popup-title
          line-height 32px
          text-align center
          font-size 18px
          color $color-text-black
        .btn-confirm-smscode
          width 120px !important
          font-size 16px !important
          letter-spacing 2px !important
</style>
