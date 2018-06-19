<template>
  <div class="register-page" @click="_loseFocus">
    <!-- 头部 -->
    <div class="register-header">
      <div class="logo-wrap">
        <span class="logo"></span>
      </div>
    </div>
    <!-- 注册信息 -->
    <div class="register-info">
      <!-- 输入区域 -->
      <div class="register-input-area">
        <!-- 手机号 -->
        <w-input ref="account" is-type="account" placeholder="11位手机号" 
          v-model="registerInfo.account" @click.stop.native="_focusOnAcc"></w-input>
        <!-- 短信验证码 -->
        <div class="verication-wrap" @click.stop="_focusOnVer">
          <w-input class="w-input-verification" ref="verification" w-type="verification" is-type="verification" 
            placeholder="短信验证码" v-model="registerInfo.verification"></w-input>
          <div class="btn-fetch-verification" :class="{'verificating': isVerificationFetched}">
            <span class="btn-default" v-show="!isVerificationFetched" @click="fetchVerification">获取验证码</span>
            <span class="btn-cooling" v-show="isVerificationFetched">{{coolingTime}}s</span>
          </div>                
        </div>
        <!-- 密码 -->
        <w-input ref="password" w-type="password" is-type="password" placeholder="6-15位密码" 
         v-model="registerInfo.password" @click.stop.native="_focusOnPwd"></w-input>
        <!-- 再次密码 -->
        <w-input ref="passwordAgain" w-type="password" is-type="password" placeholder="再次输入密码" 
         v-model="passwordAgain" @click.stop.native="_focusOnPwdAgain"></w-input>
      </div>
      <!-- 用户协议 -->
      <div class="user-protocol">
        <div class="user-protocol-link-wrap">
          <div class="icon-check-wrap" @click="checkProtocol">
            <span class="icon-checker" :class="{'icon-check': protocolCheck, 'icon-not-check': !protocolCheck}"></span>
          </div>
          <div class="user-protocol-link" @click="openPageProtocol">同意<span class="color-text-blue">《 用户注册及服务协议 》</span></div>  
        </div>
        <transition name="fade">
          <div class="user-protocol-content" v-show="protocolShow">
            <page-protocol ref="pageProtocol" @close="closePageProtocol"></page-protocol>
          </div>
        </transition>
      </div>
    </div>
    <!-- 底部 -->
    <div class="register-footer">
      <x-button class="btn-register" plain type="primary" action-type="button" @click.native="register">注册</x-button>
      <x-button class="btn-goto-login" plain type="primary" action-type="button" @click.native="gotoLogin">已注册，去登陆</x-button>
    </div>
  </div>
</template>

<script>
import Api from '@/api'
import { isPhoneNumber } from '@/common/js/utils'
import WInput from '@/components/WInput/WInput'
import PageProtocol from '@/pages/Protocol'
import { XButton } from 'vux'

export default {
  name: 'registerPage',
  data () {
    return {
      registerInfo: { // 注册信息
        account: '',
        verification: '',
        password: ''
      },
      passwordAgain: '',            // 再次输入的密码
      isVerificationFetched: false, // 验证码获取冷却时间内flag
      cooling: 60,                  // 获取验证码冷却时间
      coolingTime: 60,              // 冷却时间显示
      protocolCheck: false,
      protocolShow: false
    }
  },
  created () {
  },
  mounted () {
    this.wInputsArray = [
      this.$refs.account,
      this.$refs.verification,
      this.$refs.password,
      this.$refs.passwordAgain
    ]
    this.$refs.verification.$el.querySelector('.w-input-area').style.width = '50%'
    this.$refs.verification.$el.querySelector('.validation-error').style.paddingLeft = '10px'
  },
  destroyed () {
    this.timer && clearTimeout(this.timer)
    this.coolingTimer && clearTimeout(this.coolingTimer)
  },
  watch: {
    'passwordAgain' () {
      let _vm = this.$refs.passwordAgain
      if (this.passwordAgain !== this.registerInfo.password) {
        _vm.$nextTick(() => {
          _vm.validationError = '两次输入的密码不一致'
          _vm.$el.querySelector('.validation-error').style.display = 'block'
        })
      } else {
        _vm.$nextTick(() => {
          _vm.validationError = ''
          _vm.$el.querySelector('.validation-error').style.display = 'none'
        })
      }
    }
  },
  computed: {
    _localValidate () { // 本地校验用户的注册信息是否合法
      let _result = {ok: true, msg: ''}
      for (let i = 0; i < this.wInputsArray.length; ++i) {
        let el = this.wInputsArray[i]
        // 如果用户没有输入任何信息或者任何输入信息校验不合法，返回 false
        if (el.isInitValid || !el.isValid) {
          _result = {ok: false, msg: '请正确填写注册信息'}
          break
        }
      }
      if (this.passwordAgain !== this.registerInfo.password) {
        _result = {ok: false, msg: '两次输入的密码不一致'}
        return _result
      }
      if (!this.protocolCheck) {
        _result = {ok: false, msg: '请认真阅读用户协议后勾选'}
        return _result
      }
      return _result
    }
  },
  methods: {
    _loseFocus () {
      this.wInputsArray.forEach((el, i) => {
        el.loseFocus()
      })
    },
    _focusOnAcc () {
      this._loseFocus()
      this.wInputsArray[0].onFocus()
    },
    _focusOnVer () {
      this._loseFocus()
      this.wInputsArray[1].onFocus()
    },
    _focusOnPwd () {
      this._loseFocus()
      this.wInputsArray[2].onFocus()
    },
    _focusOnPwdAgain () {
      this._loseFocus()
      this.wInputsArray[3].onFocus()
    },
    _alertShow (content, loginFlag) {
      this.$vux.alert.show({
        content,
        onHide: function () {
          if (this.timer) clearTimeout(this.timer)
          if (loginFlag) {
            this.$router.push('/')
          }
        }.bind(this)
      })
      this.timer = setTimeout(() => {
        this.$vux.alert.hide()
        if (loginFlag) {
          this.$router.push('/')
        }
      }, 3000)
    },
    startTimerDecrease () {
      let _this = this

      // 清除定时器
      let clearTimer = function () {
        if (_this.coolingTimer) {
          clearTimeout(_this.coolingTimer)
        }
      }
      clearTimer()

      // 定时器回调loop
      function timerLoop () {
        _this.coolingTimer = setTimeout(() => {
          --_this.coolingTime
          if (_this.coolingTime <= 0) {
            _this.isVerificationFetched = false
            // 重置请求冷却
            clearTimer()
            _this.coolingTime = _this.cooling
          }
          timerLoop()
        }, 1000)
      }
      timerLoop()
    },
    async fetchVerification () {
      // 若所填写手机号不正确，直接返回
      if (!isPhoneNumber(this.registerInfo.account)) return

      // 若在冷却时间内已获取过验证码，直接返回
      if (this.isVerificationFetched) return

      // 请求服务端获取短信验证码
      let postData = {
        mobile: this.registerInfo.account
      }
      let res = await Api.fetchVerification(postData)
      this._alertShow(res.error_message)

      // 获取到验证码
      this.isVerificationFetched = true

      // 开启定时计时器
      this.startTimerDecrease()
    },
    async sendSignUpReq () {
      let postData = {
        mobile: this.registerInfo.account,
        code: this.registerInfo.verification,
        password: this.registerInfo.password
      }
      let res = await Api.signUp(postData)
      if (res.status === 'SUCCESS') {
        this._alertShow('自动登陆中...3s后若无响应，请自行登陆', true)
      } else {
        this._alertShow(res.error_message)
      }
    },
    checkProtocol () {
      this.protocolCheck = !this.protocolCheck
    },
    openPageProtocol () {
      this.protocolShow = true
      this.$refs.pageProtocol.show()
    },
    closePageProtocol () {
      this.protocolShow = false
    },
    register () {
      if (!this._localValidate.ok) {    // 若本地校验不通过
        this._alertShow(this._localValidate.msg)
      } else {                          // 请求服务端注册
        this.sendSignUpReq()
      }
    },
    gotoLogin () {
      this.$router.push('/login')
    }
  },
  components: {
    WInput,
    PageProtocol,
    XButton
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus" scoped>
@import '../../common/stylus/mixin.styl'
@import '../../common/stylus/variable.styl'

.register-page
  position fixed
  left 0
  top 0
  width 100%
  .register-header
    position relative
    width 100%
    height 160px
    background url('../../assets/imgs/header_bg.png')
    background-size 100% 400px
    background-position 0 80%
    background-repeat no-repeat
    .logo-wrap
      position absolute
      left 50%
      top 50%
      margin -56px 0 0 -36px
      .logo
        display inline-block
        width 71px
        height 71px
        bg-img('../../assets/imgs/logo')
        background-size 71px 71px
        background-repeat no-repeat
  .register-info
    margin-top 20px
    .register-input-area
      .verication-wrap
        // width 250px
        width 80%
        margin 0 auto
        &:after
          display block
          height 0
          line-height 0
          content ''
          visibility hidden
          clear both
        .w-input-verification
          float left
        .btn-fetch-verification
          float right
          width 90px
          height 40px
          line-height 40px
          text-align center
          font-size 14px
          border-radius 17px 17px
          border 1px solid $color-blue
          &.verificating
            border none
            background $color-grey-higher
          .btn-default
            width 100%
            height 100%
            color $color-text-blue
          .btn-cooling
            color $color-text-black
    .user-protocol
      margin-top 12px
      .user-protocol-link-wrap
        // width 250px
        width 80%
        margin -20px auto 0
        font-size 0
        .icon-check-wrap
          display inline-block
          vertical-align middle
          padding 6px
          .icon-checker
            font-size 20px
            color $color-text-blue
        .user-protocol-link
          display inline-block
          vertical-align middle
          font-size 14px
          color $color-text-black
      .user-protocol-content
        position fixed
        top 0
        left 0
        right 0
        bottom 0
        background-color $color-white
        z-index 99
        &.fade-enter-active, &.fade-leave-active 
          transition all .3s ease
        &.fade-enter, &.fade-leave-active
          opacity 0
  .register-footer
    margin-top 20px
    .btn-register
      // width 250px
      width 80%
      height 40px
      line-height 35px
      font-size 16px
      color $color-text-white
      letter-spacing 2px
      border-radius 20px 20px
      background-color $color-blue
      &:active
        border-color $color-blue-light
        color $color-text-blue-lightter
    .btn-goto-login
      margin-top 10px
      font-size 14px
      color $color-text-yellow
      border none
      &:active
        border none
        color $color-text-yellow
</style>