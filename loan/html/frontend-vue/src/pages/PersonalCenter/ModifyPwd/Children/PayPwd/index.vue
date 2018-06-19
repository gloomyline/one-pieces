<template>
  <div class="page-modify-pay-pwd">
    <m-p-header><span slot="title-text">修改支付密码</span><span slot="btn-confirm"></span></m-p-header>
    <group gutter="0">
      <x-input class="old" title="原支付密码" type="password" v-model="oldPwd" placeholder="请输入原支付密码"></x-input>
      <x-input class="new" title="新支付密码" type="password" v-model="newPwd" placeholder="请输入新支付密码"></x-input>
      <x-input class="confirm" title="确认密码" type="password" v-model="confirmPwd" placeholder="请再次确认密码"></x-input>
    </group>
    <div class="error-tip">
      <p class="tip-text">{{errMsg}}</p>
    </div>
    <x-button class="btn-blue btn-confirm-modify-pwd" @click.native="confirmModifyPwd">确认修改密码</x-button>
  </div>
</template>

<script>
import MPHeader from '@/components/APageHeader/APageHeader'
import { Group, XInput, XButton } from 'vux'

import { isPassword } from '@/common/js/utils'

export default {
  name: 'pageModifyPayPwd',
  data () {
    return {
      oldPwd: '',
      newPwd: '',
      confirmPwd: '',
      errMsg: ''
    }
  },
  mounted () {},
  computed: {
    localValidate () {
      if (this.oldPwd === '') {
        this.errMsg = '请输入原支付密码'
        return false
      }

      let res = isPassword(this.oldPwd)
      if (!res.isValid) {
        this.errMsg = res.errMsg
        return false
      }

      if (this.newPwd === '') {
        this.errMsg = '请输入新支付密码'
        return false
      }

      if (this.confirmPwd === '') {
        this.errMsg = '请再次输入新密码'
      }

      if (this.newPwd !== this.confirmPwd) {
        this.errMsg = '两次输入的密码不一致'
        return false
      }
    }
  },
  methods: {
    confirmModifyPwd () {
      if (!this.localValidate) return
      // send request to server
    }
  },
  components: {
    MPHeader,
    Group,
    XInput,
    XButton
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus">
@import '../../../../../common/stylus/variable.styl'

.page-modify-pay-pwd
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
</style>