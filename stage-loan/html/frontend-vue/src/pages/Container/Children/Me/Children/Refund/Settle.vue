<!--
@Date:   2018-01-08T15:59:57+08:00
@Last modified time: 2018-01-16T09:52:29+08:00
-->
<template lang="html">
  <div class="page-early-settle">
    <e-s-header class="settle-header" not-go-back :not-has-confirm="true" @go-back="close">
      <span class="title" slot="title-text">提前结清</span>
    </e-s-header>
    <div class="settle-content">
      <section class="amount">
        <h3 class="title">提前还款金额：</h3>
        <p class="amount-count">￥{{ settle.term_amount }}</p>
      </section>
      <section class="detail">
        <p class="principal detail-item">
          <span class="label">借款本金</span>
          <span class="value">￥{{ settle.surplus_principal }}</span>
        </p>
        <p class="interest detail-item">
          <span class="label">借款息费</span>
          <span class="value">￥{{ settle.term_interest }}</span>
        </p>
        <p class="fee detail-item">
          <span class="label">提前还款手续费</span>
          <span class="value">￥{{ settle.prepayment_fee }}</span>
        </p>
      </section>
      <section class="bankcard">
        <div class="logo-wrap">
          <img :src="settle.bank_code | logoConverter" width="36" height="36">
        </div>
        <div class="desc">
          <p class="bank-card-name">{{ settle.bank_name }}（{{ settle.end_bank_no }}）</p>
          <p class="tip">单笔限额100万、每日限额1000万</p>
        </div>
      </section>
    </div>
    <footer class="settle-footer">
      <x-button class="btn-settle-imediately" type="primary" action-type="button" @click.native="settleImediately">立即结清</x-button>
      <!-- 请求连连支付第三方接口还款申请 -->
      <form ref="form" action="https://wap.lianlianpay.com/authpay.htm" method="POST">
        <input type="hidden" name="req_data" :value="settleData">
      </form>
    </footer>
  </div>
</template>

<script>
import ESHeader from '@/components/APageHeader/APageHeader'
import { XButton } from 'vux'

import { createNamespacedHelpers } from 'vuex'
const namespace = 'container/me/refund'
const { mapGetters, mapActions } = createNamespacedHelpers(namespace)

import bankcardDatas from '@/assets/datas/bankcard'

export default {
  data () {
    return {}
  },
  props: {
    loanId: {
      type: Number | String,
      default: 0
    }
  },
  computed: {
    ...mapGetters([
      'settle', 'settleData'
    ])
  },
  methods: {
    ...mapActions([
      'confirmSettle', 'doSettle'
    ]),
    close () {
      this.$emit('close')
    },
    show () {
      this.confirmSettle({ loan_id: this.loanId })
    },
    async settleImediately () {
      await this.doSettle({ loan_id: this.loanId })
      this.$nextTick(() => {
        this.$refs.form.submit()
      })
    }
  },
  filters: {
    logoConverter (bank_code) {
      let bankcard = bankcardDatas.find(item => item.code === bank_code)
      if (!bankcard || bankcard === undefined) return
      return require(`../../../../../../components/BankCard/${bankcard.iconName.replace('-', '_')}@${window.devicePixelRatio}x.png`)
    }
  },
  components: {
    ESHeader,
    XButton
  }
}
</script>

<style lang="stylus" scoped>
@import '../../../../../../common/stylus/mixin.styl'
@import '../../../../../../common/stylus/variable.styl'

.page-early-settle
  position: fixed
  left: 0
  top: 0
  bottom: 0
  width: 100%
  background-color: $color-white-high
  .settle-header
    border-1px($color-border-grey-light, 'bottom')
  .settle-content
    font-size: 16px
    .amount
      height: 100px
      border-1px($color-border-grey-light, 'bottom')
      .title
        padding: 16px 0 0 16px
      .amount-count
        margin-top: 20px
        padding-left: 16px
        font-size: 32px
        color: $color-text-yellow
    .detail
      height: 100px
      padding: 0 16px 0 16px
      border-1px($color-border-grey-light, 'bottom')
      .detail-item
        line-height: 30px
        font-size: 0
        .label
          display: inline-block
          width: 60%
          font-size: 16px
        .value
          display: inline-block
          text-align: right
          width: 40%
          font-size: 16px
    .bankcard
      margin-top: 10px
      height: 60px
      font-size: 0
      border-1px($color-border-grey-light, 'top')
      .logo-wrap
        display: inline-block
        vertical-align: top
        padding: 12px 12px 12px 16px
      .desc
        display: inline-block
        vertical-align: top
        margin-top: 12px
        font-size: 16px
        .tip
          margin-top: 4px
          font-size: 14px
          color: $color-text-grey
  .settle-footer
    padding-top: 30px
    border-1px($color-border-grey-light, 'top')
    .btn-settle-imediately
      width: 300px
</style>
