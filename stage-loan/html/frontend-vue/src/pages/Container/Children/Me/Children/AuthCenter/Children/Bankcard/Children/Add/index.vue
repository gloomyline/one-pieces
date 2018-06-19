<!--
@Date:   2018-01-02T14:30:29+08:00
@Last modified time: 2018-01-02T15:29:07+08:00
-->
<template>
  <div class="page-bankcard-add">
    <b-a-header @confirm="confirm">
      <span slot="title-text">添加银行卡</span>
      <span slot="btn-confirm">确定</span>
    </b-a-header>
    <group class="bankcard-input-area" gutter="0">
      <x-input class="card-number" title="银行卡号" v-model="bankcardNum" ref="cardNumInput" placeholder="请输入银行卡号"></x-input>
    </group>
    <div class="btn-open-supported-bankcard">
      <span class="label" @click="openSupported">查看支持的银行</span>
    </div>
    <transition name="supported-fade">
      <div class="page-bankcards-supported-wrap" v-show="pageSupportedShow">
        <page-supported class="supported" @close-page="closeSupported"></page-supported>
      </div>
    </transition>
    <section class="warm-tip">
      <div class="tip-title">
        <p class="line"></p>
        <p class="text">温馨提示</p>
        <p class="line"></p>
      </div>
      <ol class="tip-content">
        <li class="tip-item">放款使用，需填写本人名下有效银行卡</li>
      </ol>
    </section>
    <!-- 请求连连支付第三方接口注册银行卡申请 -->
    <form ref="form" action="https://wap.lianlianpay.com/signApply.htm" method="POST">
      <input type="hidden" name="req_data" :value="reqDataStr">
    </form>
  </div>
</template>

<script>
import BAHeader from '@/components/APageHeader/APageHeader'
import PageSupported from '../Supported'
import { Group, XInput } from 'vux'

import { createNamespacedHelpers } from 'vuex'
const namespace = 'authenticationCenter/bankcard'
const { mapGetters, mapActions } = createNamespacedHelpers(namespace)

export default {
  name: 'pagBankcardAdd',
  data () {
    return {
      pageSupportedShow: false
    }
  },
  created () {
  },
  mounted () {},
  computed: {
    ...mapGetters([
      'formData', 'reqData', 'validateErr', 'responseErrMsg'
    ]),
    reqDataStr () {
      return JSON.stringify(this.reqData)
    },
    bankcardNum: {
      get () {
        return this.formData.card_no
      },
      set (num) {
        this.$store.commit(`${namespace}/UPDATE_BANKCARD_NUM`, { num })
      }
    }
  },
  destroyed () {
    this.$store.commit(`${namespace}/CLEAR_BANKCARD_NUM`)
  },
  watch: {
    'validateErr' () {
      this._alertShow(this.validateErr.replace(/\d+/, ''))
    },
    'responseErrMsg' () {
      this._alertShow(this.responseErrMsg.replace(/\d+/, ''))
    }
  },
  methods: {
    ...mapActions([
      'validateBankcard', 'addBankcard', 'authBankcard'
    ]),
    async confirm () { // request for add bank card
      if (!this.formData.card_no.replace(/\s+/g, '')) {
        this._alertShow('银行卡号不可空！')
        return
      }

      // card bin api
      if (process.env.NODE_ENV === 'production') {
        await this.validateBankcard()
      }

      let reqData = await this.addBankcard()
      if (!reqData) return

      this.$nextTick(() => {
        this.$store.commit(`${namespace}/CLEAR_BANKCARD_NUM`)
        this.$refs.form.submit()
      })
    },
    openSupported () {
      this.pageSupportedShow = true
    },
    closeSupported () {
      this.pageSupportedShow = false
    }
  },
  components: {
    BAHeader,
    PageSupported,
    Group,
    XInput
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus">
@import '../../../../../../../../../../common/stylus/variable.styl'

.page-bankcard-add
  .bankcard-input-area
    .weui-cell
      .weui-cell__hd
        font-size 15px
        color $color-text-black
      .weui-cell__bd > input
        text-align right
        font-size 15px
        color $color-text-grey-higher
      .vux-label
        font-size 14px
      .vux-popup-picker-select
        font-size 15px
        color $color-text-grey-higher
  .btn-open-supported-bankcard
    width 100%
    height 25px
    line-height 25px
    text-align right
    padding-right 15px
    .label
      font-size 13px
      color $color-text-blue
  .page-bankcards-supported-wrap
    position fixed
    top 0
    left 0
    bottom 0
    width 100%
    background $color-white
    overflow hidden
    z-index 99
    &.supported-fade-enter-active, &.supported-fade-leave-active
      transition all .6s ease
    &.supported-fade-enter, &.supported-fade-leave-to
      opacity 0
  .warm-tip
    width 80%
    margin 80px auto 0
    .tip-title
      display flex
      font-size 0
      .line
        flex 1
        position relative
        top -8px
        border-bottom 1px solid $color-grey-higher1
      .text
        font-size 13px
        color $color-text-grey-higher
    .tip-content
      width 70%
      margin 20px auto 0
      font-size 12px
      color $color-text-grey-higher
      .tip-item
        list-style-type decimal
        line-height 1.8em
</style>
