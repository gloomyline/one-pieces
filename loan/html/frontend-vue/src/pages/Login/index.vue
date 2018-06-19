<template>
  <div class="login-page" @click="_loseFocus">
    <div class="login-header">
      <div class="logo-wrap">
        <span class="logo"></span>
      </div>
    </div>
    <div class="login-info">
      <w-input ref="account" is-type="account" placeholder="11位手机号" v-model="account" @click.native.stop="focusOnAcc"></w-input>
      <w-input ref="password" w-type="password" is-type="password" v-model="password" placeholder="6-15位密码" @click.native.stop="focusOnPwd"></w-input>
      <div class="extra">
        <div class="extra-wrap clearfix">
          <span class="register" @click="register">注册</span>
          <span class="password-reset" @click="forgetLoginPwd">忘记密码？</span>  
        </div>
      </div>
      <div class="login-btn-wrap">
        <button class="login-btn" @click.stop="login">登录</button>
      </div>
    </div>
  </div>
</template>

<script>
import WInput from '@/components/WInput/WInput'

import api from '@/api'

export default {
  name: 'loginPage',
  data () {
    return {
      account: '',
      password: ''
    }
  },
  mounted () {},
  computed: {},
  methods: {
    _loseFocus () {
      this.$refs.account && this.$refs.account.loseFocus()
      this.$refs.password && this.$refs.password.loseFocus()
    },
    focusOnAcc () {
      this._loseFocus()
      this.$refs.account.onFocus()
    },
    focusOnPwd () {
      this._loseFocus()
      this.$refs.password.onFocus()
    },
    register () {
      this.$router.push('/register')
    },
    forgetLoginPwd () {
      this.$router.push('/forgetLoginPwd')
    },
    alertShow (content, loginFlag) {
      this.$vux.alert.show({
        content,
        onHide: function () {
          if (loginFlag) {
            this.$router.push('/')
          }
          if (this.timer) {
            clearTimeout(this.timer)
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
    async login () {
      let postData = {
        mobile: this.account,
        password: this.password
      }
      let res = await api.login(postData)
      if (res.status === 'SUCCESS') {
        // this.alertShow('正在为您跳转中...', true)
        this.$router.push('/')
      }
    }
  },
  components: {
    WInput
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus" scoped>
@import '../../common/stylus/mixin.styl'
@import '../../common/stylus/variable.styl'

.login-page
  position fixed
  left 0
  top 0
  bottom 0
  width 100%
  overflow hidden
  .login-header
    position relative
    width 100%
    height 280px
    background url('./header_bg_1.0.0.png')
    background-size 100% 100%
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
        bg-img('./logo')
        background-size 71px 71px
        background-repeat no-repeat
  .login-info
    // margin-top -10px
    .extra
      height 36px
      text-align center  
      margin-top -10px
      .extra-wrap
        width 80%
        line-height 36px
        font-size 0
        .register
          float right
          margin-left 16px
          display inline-block
          font-size 15px
          color $color-text-error
        .password-reset
          float left
          display inline-block
          font-size 15px
          color $color-text-blue
    .login-btn-wrap
      // width 250px
      width 80%
      height 40px
      margin 20px auto 0
      .login-btn
        width 100%
        height 100%
        font-size 16px
        color $color-text-white
        border-radius 17px 17px
        background $color-blue
        box-shadow 3px 5px 5px $color-blue-light
        &:active
          color $color-text-blue-lightter
          background $color-blue-light
</style>