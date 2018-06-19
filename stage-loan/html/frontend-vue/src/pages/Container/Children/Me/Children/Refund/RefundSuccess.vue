<!--
@Date:   2018-01-23T15:21:23+08:00
@Last modified time: 2018-02-01T17:59:18+08:00
-->
<template>
  <div class="page-refund-success">
    <r-s-header @go-back="goBack" not-go-back><span slot="title-text">还款成功</span><span slot="btn-confirm"></span></r-s-header>
    <div class="content">
      <div class="result-icon-wrap">
        <span class="icon-success icon"></span>
      </div>
      <div class="result-text">
        <p class="pay-way" v-if="bankCode">支付方式：{{ bankName }}</p>
        <p class="refund-amount" v-if="refundAmount && refundAmount !== 0">还款金额：￥{{ refundAmount }}</p>
      </div>
      <x-button class="btn-tip btn-yellow" plain type="primary" action-type="button" @click.native="goBack">完成</x-button>
    </div>
  </div>
</template>

<script>
import RSHeader from '@/components/APageHeader/APageHeader'
import { XButton } from 'vux'

import bankMap from '@/assets/datas/bankcard'

export default {
  name: 'pageRefundSuccess',
  data () {
    return {
      bankName: '',
      bankCode: '',
      refundAmount: 0
    }
  },
  created () {
    let queries = this.$route.query
    this.bankCode = queries.bank_code
    let el = bankMap.find(item => item.code === this.bankCode)
    this.bankName = el ? el.name : ''
    this.refundAmount = (queries.amount * 1).toFixed(2)
  },
  mounted () {
    // this._alertShow('还款成功，3s后自动返回首页', this.goBack)
  },
  computed: {},
  methods: {
    goBack () { // 返回借款首页
      this.$router.push('/me/refund')
    }
  },
  components: {
    RSHeader,
    XButton
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus" scoped>
@import '../../../../../../common/stylus/variable.styl'
@import '../../../../../../common/stylus/mixin.styl'

.page-refund-success
  .content
    text-align center
    .result-icon-wrap
      padding-top 70px
      padding-bottom 32px
      .icon-success
        font-size: 64px
        color: $color-yellow
    .result-text
      width: (375 - 134)px
      margin: 0 auto
      text-align: left
      font-size 18px
      font-weight 400
      color $color-text-black
      .refund-amount
        margin-top: 20px
    .btn-tip
      width: 340px
      margin-top 36px
      font-weight: 600
      letter-spacing: 10px !important
</style>
