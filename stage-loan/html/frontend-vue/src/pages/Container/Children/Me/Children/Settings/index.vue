<!--
@Date:   2017-12-29T10:04:07+08:00
@Last modified time: 2018-01-18T17:53:10+08:00
-->
<template lang="html">
  <div class="page-settings">
    <s-header :not-has-confirm="true"><span class="title" slot="title-text">设置</span></s-header>
    <group class="settings-content" gutter="0">
      <cell class="goto-modify-pwd" title="修改登录密码" is-link @click.native="gotoModifyPwd"></cell>
      <!-- <cell class="goto-my-device" title="我的设备" is-link="" @click.native="gotoMyDevice"></cell> -->
    </group>
    <x-button class="btn-logout btn-grey" type="primary" action-type="button" @click.native="logout">退出登录</x-button>
    <div class="children-container">
      <transition name="modify-pwd-fade">
        <page-modify-pwd class="modify-pwd" v-show="isModifyPwdShow" @close="closeModifyPwd"></page-modify-pwd>
      </transition>
      <!-- <transition name="my-device-fade">
        <page-my-device class="my-device" v-show="isMyDeviceShow" @close="closeMyDevice"></page-my-device>
      </transition> -->
    </div>
  </div>
</template>

<script>
import SHeader from '@/components/APageHeader/APageHeader'
import PageModifyPwd from './ModifyPwd'
import PageMyDevice from './MyDevice'
import { Group, Cell, XButton } from 'vux'

import { mapState } from 'vuex'

export default {
  data () {
    return {
      isModifyPwdShow: false,
      isMyDeviceShow: false
    }
  },
  created () {
  },
  destroyed () {
  },
  computed: {
    ...mapState({
      isLogined: state => state.user.isLogined
    })
  },
  watch: {
    'isLogined' (newVal, oldVal) {
      if (!newVal) {
        this.$router.push('/login')
      }
    }
  },
  methods: {
    gotoModifyPwd () {
      this.isModifyPwdShow = true
    },
    closeModifyPwd () {
      this.isModifyPwdShow = false
    },
    gotoMyDevice () {
      this.isMyDeviceShow = true
    },
    closeMyDevice () {
      this.isMyDeviceShow = false
    },
    logout () {
      this._alertShow('确认退出登录？', () => {
        this.$store.dispatch('user/login/logout')
      }, true)
    }
  },
  components: {
    SHeader,
    PageModifyPwd,
    PageMyDevice,
    Group,
    Cell,
    XButton
  }
}
</script>

<style lang="stylus">
@import '../../../../../../common/stylus/mixin.styl'
@import '../../../../../../common/stylus/variable.styl'

.page-settings
  .btn-logout
    width: 300px
    margin-top: 60px
  .children-container
    .modify-pwd, .my-device
      position: fixed
      left: 0
      top: 0
      bottom: 0
      width: 100%
      overflow: hidden
      background-color: $color-white-light
      &.modify-pwd-fade-enter-active, &.my-device-fade-enter-active, &.modify-pwd-fade-leave-active, &.my-device-fade-leave-active
        transition: all .6s ease
      &.modify-pwd-fade-enter, &.modify-pwd-fade-leave-to, &.my-device-fade-enter, &.my-device-fade-leave-to
        opacity: 0
</style>
