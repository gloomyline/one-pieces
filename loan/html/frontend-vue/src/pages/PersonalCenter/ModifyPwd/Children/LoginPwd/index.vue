<template>
  <div class="page-modify-login-pwd">
    <m-l-header><span slot="title-text">修改登录密码</span><span slot="btn-confirm"></span></m-l-header>
    <group gutter="0">
      <x-input class="old" title="原登录密码" :type="originPwdType" v-model="oldPwd" placeholder="请输入原登录密码">
        <div class="icon-eye-wrap" slot="right" @click="toggleShowOriginPwd"><span class="icon-eye" :class="{'icon-eye-hide': !isOriginPwdHide, 'icon-eye-show': isOriginPwdHide}"></span></div>
      </x-input>
      <x-input class="new" title="新登录密码" :type="newPwdType" v-model="newPwd" placeholder="请输入新登录密码">
        <div class="icon-eye-wrap" slot="right" @click="toggleShowNewPwd"><span class="icon-eye" :class="{'icon-eye-hide': !isNewPwdHide, 'icon-eye-show': isNewPwdHide}"></span></div>
      </x-input>
      <x-input class="confirm" title="确认密码" :type="confirmedPwdType" v-model="confirmPwd" placeholder="请再次确认密码">
        <div class="icon-eye-wrap" slot="right" @click="toggleShowConfirmedPwd"><span class="icon-eye" :class="{'icon-eye-hide': !isConfirmedPwdHide, 'icon-eye-show': isConfirmedPwdHide}"></span></div>
      </x-input>
    </group>
    <div class="error-tip">
      <p class="tip-text">{{errMsg}}</p>
    </div>
    <x-button class="btn-blue btn-confirm-modify-pwd" @click.native="confirmModifyPwd">确认修改密码</x-button>
  </div>
</template>

<script>
import MLHeader from '@/components/APageHeader/APageHeader'
import { Group, XInput, XButton } from 'vux'

import { isPassword } from '@/common/js/utils'

import Api from '@/api'

export default {
  name: 'pageModifyLoginPwd',
  data () {
    return {
      oldPwd: '',
      newPwd: '',
      confirmPwd: '',
      errMsg: '',
      originPwdType: 'password',
      isOriginPwdHide: true,
      newPwdType: 'password',
      isNewPwdHide: true,
      confirmedPwdType: 'password',
      isConfirmedPwdHide: true
    }
  },
  mounted () {},
  computed: {
    localValidate () {
      if (this.oldPwd === '') {
        this.errMsg = '请输入原登录密码'
        return false
      }

      let res = isPassword(this.oldPwd)
      if (!res.isValid) {
        this.errMsg = res.errMsg
        return false
      }

      if (this.newPwd === '') {
        this.errMsg = '请输入新登录密码'
        return false
      }

      if (this.confirmPwd === '') {
        this.errMsg = '请再次输入新密码'
      }

      if (this.newPwd !== this.confirmPwd) {
        this.errMsg = '两次输入的密码不一致'
        return false
      }

      return true
    }
  },
  methods: {
    toggleShowOriginPwd () {    // switch origin pwd show or not
      this.originPwdType = (this.originPwdType === 'password') ? 'text' : 'password'
      this.isOriginPwdHide = !this.isOriginPwdHide
    },
    toggleShowNewPwd () {       // switch new pwd show or not
      this.newPwdType = (this.newPwdType === 'password') ? 'text' : 'password'
      this.isNewPwdHide = !this.isNewPwdHide
    },
    toggleShowConfirmedPwd () { // switch confirmed pwd show or not
      this.confirmedPwdType = (this.confirmedPwdType === 'password') ? 'text' : 'password'
      this.isConfirmedPwdHide = !this.isConfirmedPwdHide
    },
    async confirmModifyPwd () {
      if (!this.localValidate) return
      // send request to server
      let payload = {
        old_password: this.oldPwd,
        new_password: this.newPwd,
        repeat_password: this.confirmPwd
      }
      let res = await Api.modifyLoginPwd(payload)
      if (res.status === 'SUCCESS') {
        this._alertShow(res.error_message, () => {
          this.$router.push('/')
        })
      }
    }
  },
  components: {
    MLHeader,
    Group,
    XInput,
    XButton
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus">
@import '../../../../../common/stylus/variable.styl'
@import '../../../../../common/stylus/mixin.styl'

.page-modify-login-pwd
  .confirm
    .weui-cell__bd
      margin-left 16px
  .error-tip
    width 100%
    height 42px
    line-height 42px
    padding 0 15px
    font-size 12px
    color $color-text-error
  .btn-confirm-modify-pwd
    margin-top 36px
  .icon-eye-wrap
    display inline-block
    vertical-align middle
    padding 8px
    .icon-eye
      font-size 24px
      color $color-text-blue
</style>