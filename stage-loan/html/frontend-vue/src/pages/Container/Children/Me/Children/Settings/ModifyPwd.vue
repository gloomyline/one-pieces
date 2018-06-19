<!--
@Date:   2017-12-29T11:32:38+08:00
@Last modified time: 2017-12-29T15:05:56+08:00
-->
<template lang="html">
  <div>
    <m-l-header :not-has-confirm="true" @go-back="goBack($event)" not-go-back>
      <span slot="title-text">修改登录密码</span>
    </m-l-header>
    <group gutter="0">
      <x-input class="old" title="原登录密码" :type="originPwdType" v-model="oldPwd" placeholder="请输入原登录密码">
        <div class="icon-eye-wrap" slot="right" @click="toggleShowOriginPwd"><span class="icon-eye" :class="{'icon-eye-hide': !isOriginPwdHide, 'icon-eye-show': isOriginPwdHide}"></span></div>
      </x-input>
      <x-input class="new" title="新登录密码" :type="newPwdType" v-model="newPwd" placeholder="请输入新登录密码">
        <div class="icon-eye-wrap" slot="right" @click="toggleShowNewPwd"><span class="icon-eye" :class="{'icon-eye-hide': !isNewPwdHide, 'icon-eye-show': isNewPwdHide}"></span></div>
      </x-input>
      <x-input class="confirm" title="确认密码" :type="confirmedPwdType"
        v-model="cofirmedPwd" placeholder="请再次确认密码">
        <div class="icon-eye-wrap" slot="right" @click="toggleShowConfirmedPwd">
          <span class="icon-eye" :class="{'icon-eye-hide': !isConfirmedPwdHide, 'icon-eye-show': isConfirmedPwdHide}"></span>
        </div>
      </x-input>
    </group>
    <div class="error-tip">
      <p class="tip-text">{{errMsg}}</p>
    </div>
    <x-button class="btn-confirm-modify-pwd" type="primary" @click.native="confirmModifyPwd">确认修改密码</x-button>
  </div>
</template>

<script>
import MLHeader from '@/components/APageHeader/APageHeader'
import { Group, XInput, XButton } from 'vux'
import { createNamespacedHelpers } from 'vuex'
const namespace = 'user'
const { mapGetters, mapActions } = createNamespacedHelpers(namespace)

export default {
  data () {
    return {
      originPwdType: 'password',
      newPwdType: 'password',
      confirmedPwdType: 'password',
      errMsg: ''
    }
  },
  watch: {
    'modifyPwd.invalidErr' () {
      this._alertShow(this.modifyPwd.invalidErr.replace(/\d+$/, ''))
    },
    'modifyPwd.msg' () {
      this._alertShow(this.modifyPwd.msg.replace(/\d+$/, ''), () => {
        this.$store.commit(`${namespace}/CLEAR_FOR_MODIFY_PWD`)
        // go back to route '/me/settings'
        this.$emit('close')
      })
    }
  },
  computed: {
    ...mapGetters([
      'modifyPwd'
    ]),
    oldPwd: {
      get () {
        return this.modifyPwd.old_password
      },
      set (val) {
        this.$store.commit(`${namespace}/UPDATE_FOR_MODIFY_PWD`, {
          key: 'old_password',
          val
        })
      }
    },
    newPwd: {
      get () {
        return this.modifyPwd.new_password
      },
      set (val) {
        this.$store.commit(`${namespace}/UPDATE_FOR_MODIFY_PWD`, {
          key: 'new_password',
          val
        })
      }
    },
    cofirmedPwd: {
      get () {
        return this.modifyPwd.repeat_password
      },
      set (val) {
        this.$store.commit(`${namespace}/UPDATE_FOR_MODIFY_PWD`, {
          key: 'repeat_password',
          val
        })
      }
    },
    isOriginPwdHide () {
      return this.originPwdType === 'password'
    },
    isNewPwdHide () {
      return this.newPwdType === 'password'
    },
    isConfirmedPwdHide () {
      return this.confirmedPwdType === 'password'
    }
  },
  methods: {
    ...mapActions([
      'confirmModifyPwd'
    ]),
    goBack (event) {
      this.$store.commit(`${namespace}/CLEAR_FOR_MODIFY_PWD`)
      // go back to route '/me/settings'
      this.$emit('close', event)
    },
    toggleShowOriginPwd () {
      this.originPwdType = this.originPwdType === 'password' ? 'text' : 'password'
    },
    toggleShowNewPwd () {
      this.newPwdType = this.newPwdType === 'password' ? 'text' : 'password'
    },
    toggleShowConfirmedPwd () {
      this.confirmedPwdType = this.confirmedPwdType === 'password' ? 'text' : 'password'
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

<style lang="stylus">
@import '../../../../../../common/stylus/mixin.styl'
@import '../../../../../../common/stylus/variable.styl'

.modify-pwd
  .weui-cells
    .weui-cell
      .weui-cell__ft
        .weui_icon_clear
          color: $color-text-yellow
        .icon-eye-wrap
          display: inline-block
          vertical-align: middle
          font-size: 20px
          color: $color-text-yellow
  .btn-confirm-modify-pwd
    width: 300px !important
    margin-top: 32px
</style>
