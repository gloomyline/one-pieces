<!--
@Date:   2017-12-19T11:43:27+08:00
@Last modified time: 2018-01-05T16:28:58+08:00
-->
<template lang="html">
  <div class="page-reset-pwd">
    <r-p-header class="header"><span class="title" slot="title-text">忘记密码</span><span slot="btn-confirm"></span></r-p-header>
    <group class="reset-inputs" gutter="0">
      <x-input class="mobile" placeholder="请输入手机号码" novalidate v-model="mobile">
        <i class="icon-book" slot="label"></i>
      </x-input>
      <x-input class="code" placeholder="请输入短信验证码" novalidate v-model="code">
        <i class="icon-msg" slot="label"></i>
        <x-button class="btn-fetch-code" slot="right" type="primary" mini plain @click.native="fetchCode">{{ labelFetchCode }}</x-button>
      </x-input>
      <x-input class="password" :type="pwdType" placeholder="请重置6~18位新密码" novalidate v-model="password">
        <i class="icon-lock" slot="label"></i>
        <i class="icon-eye" :class="eyeClass" slot="right" @click="togglePwdType"></i>
      </x-input>
      <x-input class="id-card" placeholder="请输入本人身份证号码后6位" novalidate v-model="idNumber">
        <i class="icon-id-card" slot="label"></i>
      </x-input>
    </group>
    <div class="footer">
      <x-button class="btn-reset" :class="{'btn-grey': !isBtnEnabled, 'btn-yellow': isBtnEnabled}"
        :disabled="!isBtnEnabled" type="primary" action-type="button" @click.native="resetPwd">重置密码</x-button>
    </div>
  </div>
</template>

<script>
import RPHeader from '@/components/APageHeader/APageHeader'
import { Group, XInput, XButton } from 'vux'

import { createNamespacedHelpers } from 'vuex'
const namespace = 'user/resetPwd'
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
    this.$store.commit(`${namespace}/CLEAR_FOR_RESET_PWD`)
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
          this.$router.push('/login')
        })
      }
    }
  },
  computed: {
    ...mapGetters([
      'form',
      'isCodeFetched',
      'isCoolingDown',
      'coolingTime',
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
      set (val) {
        this.updateInput({ key: 'password', val })
      }
    },
    idNumber: {
      get () {
        return this.form.idNumber
      },
      set (num) {
        this.updateInput({ key: 'card_mantissa', val: num })
      }
    },
    labelFetchCode () {
      return !this.isCodeFetched
        ? '获取验证码' : this.isCoolingDown
        ? '重新发送' : `${this.coolingTime}后重新发送`
    },
    eyeClass () {
      return this.pwdType === 'password' ? 'icon-eye-show' : 'icon-eye-hide'
    },
    isBtnEnabled () {
      for (let key in this.form) {
        if (key === 'card_mantissa') {
          continue
        } else {
          let val = this.form[key]
          if (val === '') {
            return false
          }
        }
      }
      return true
    }
  },
  methods: {
    ...mapActions([
      'fetchCode',
      'resetPwd'
    ]),
    updateInput ({ key, val }) {
      this.$store.commit(`${namespace}/UPDATE_FOR_RESET_PWD`, { key, val })
    },
    togglePwdType () {
      this.pwdType = this.pwdType === 'password' ? 'text' : 'password'
    }
  },
  components: {
    RPHeader,
    Group,
    XInput,
    XButton
  }
}
</script>

<style lang="stylus">
@import '../../common/stylus/mixin.styl'
@import '../../common/stylus/variable.styl'

.page-reset-pwd
  .reset-inputs
    .weui-cells
      .weui-cell
        height: 55px
        padding-left: 18px
        .weui-cell__hd
          margin-right: 20px
          i[class^='icon-']
            font-size: 20px
            color: $color-text-yellow
        .weui-cell__bd
          .weui-input
            font-size: 18px
        .weui-cell__ft
          .btn-fetch-code
            width: 100px
            height: 36px
            margin-right: 8px
            padding: 0
            font-size: 16px
            color: $color-text-grey
            border-radius: 8px 8px
            border-color: $color-text-grey-higher
          .icon-eye
            vertical-align: middle
            font-size: 20px
            color: $color-text-yellow
  .footer
    margin-top: 50px
    .btn-reset
      width: 300px
      height: 40px
      border-radius: 20px 20px
      &.btn-grey
        background-color: $color-grey-higher
</style>
