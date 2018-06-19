<template>
  <div class="page-identity">
    <i-header @confirm="confirm">
      <span slot="title-text">身份认证</span>
      <span slot="btn-confirm">{{btnConfirm}}</span>
    </i-header>
    <group class="input-area" gutter="0">
      <x-input class="input-name" v-model="name" v-if="btnConfirmShow
      " placeholder="请输入姓名">
        <span class="label" slot="label">真实姓名</span>
      </x-input>
      <cell class="cell-name" v-else title="真实姓名" :value="name"></cell>
      <x-input class="input-ic-number" v-model="icNumber" v-if="btnConfirmShow" placeholder="请输入身份证号码">
        <span class="label" slot="label">身份证号</span>
      </x-input>
      <cell class="cell-ic-number" v-else title="身份证号" :value="icNumber"></cell>
      <div class="error" v-show="!isValid">{{errMsg}}</div>
    </group>
  </div>
</template>

<script>
import IHeader from '@/components/APageHeader/APageHeader'
import { Group, Cell, XInput } from 'vux'

import { isChineseName, isIDNumber } from '@/common/js/utils'

import { createNamespacedHelpers } from 'vuex'
const { mapGetters, mapActions } = createNamespacedHelpers('authenticationCenter/identity')

export default {
  name: 'pageIdentity',
  data () {
    return {
      isValid: false
    }
  },
  created () {
    this.fetchIdentity()
  },
  mounted () {},
  computed: {
    ...mapGetters([
      'btnConfirmShow', 'confirmMsg'
    ]),
    name: {
      get () {
        return this.$store.state.authenticationCenter.identity.name
      },
      set (name) {
        this.$store.commit('authenticationCenter/identity/UPDATE_IDENTITY_NAME', { name })
      }
    },
    icNumber: {
      get () {
        return this.$store.state.authenticationCenter.identity.icNumber
      },
      set (icNumber) {
        this.$store.commit('authenticationCenter/identity/UPDATE_IDENTITY_ICNUMBER', { icNumber })
      }
    },
    errMsg () {
      if (this.name === '') {
        this.isValid = false
        return '姓名不能为空'
      }
      if (!isChineseName(this.name)) {
        this.isValid = false
        return '请输入正确的姓名'
      }
      if (this.icNumber === '') {
        this.isValid = false
        return '身份证号码不能为空'
      }
      if (!isIDNumber(this.icNumber)) {
        this.isValid = false
        return '请输入正确的身份证号码'
      }

      this.isValid = true
      return ''
    },
    btnConfirm () {
      return this.btnConfirmShow ? '提交' : ''
    }
  },
  watch: {
    'confirmMsg' () {
      let msg = this.confirmMsg.replace(/(\d)+/g, '')
      let msgStr = (msg === 'pass') ? '认证已通过' : '认证未通过'
      this._alertShow(msgStr, () => {
        if (msg !== 'pass') return
        this.$router.push('/authentication')
      })
    }
  },
  methods: {
    ...mapActions([
      'fetchIdentity', 'confirmIdentity'
    ]),
    confirm () {
      // 本地校验不通过则不发送请求
      if (!this.isValid) return
      // 向服务端发送提交请求
      let postData = {
        username: this.name,
        identityno: this.icNumber
      }
      this.confirmIdentity(postData)
    }
  },
  components: {
    IHeader,
    Group,
    Cell,
    XInput
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus">
@import '../../../../common/stylus/variable.styl'

  .page-identity
    .input-area
      .weui-cells
        &:after
          display none
      .input-name, .input-ic-number
        height 55px
        border-bottom 1px solid $color-grey-higher1
        .label
          font-size 14px
          color $color-text-black
        .weui-input
          padding-left 8px
          font-size 14px
          cursor pointer
      .error
        width 100%
        height 55px
        padding 10px 15px
        line-height 35px
        font-size 14px
        color $color-text-error
        
</style>