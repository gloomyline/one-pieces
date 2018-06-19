<!--
@Date:   2018-01-08T10:39:26+08:00
@Last modified time: 2018-01-19T17:49:16+08:00
-->
<template lang="html">
  <div class="page-refund-detail">
    <r-header class="refund-detail-header" :not-has-confirm="true">
      <span class="title" slot="title-text">还款详情</span>
    </r-header>
    <div class="refund-detail-content">
      <div class="left-refund">
        <h3 class="title-tip">剩余未还总额：</h3>
        <p class="left-amount">￥{{ lend.surplus_amount | figure}}</p>
        <span class="refund-date-tip">还款日10号</span>
      </div>
      <div class="current-refund">
        <h3 class="title-tip">本月应还</h3>
        <p class="current-count">￥{{ lend.term_amount }}</p>
        <p class="current-date">
          <span class="term">{{ lend.current_term | termFormater(lend.period) }}</span>
          <span class="date">{{ lend.repayment_at | dateFormater }}</span>
        </p>
        <div class="state">
          <span class="state-text" :class="{'overdue': isOverdue}">{{ isOverdue ? '逾期中' : '正常' }}</span>
          <span class="btn-bill-detail" @click="openBillPanel">账单详情</span>
        </div>
      </div>
      <div class="bill-panel-wrap" v-show="isBillPanelShow">
        <div class="bill-panel">
          <h4 class="panel-title">第{{ lend.current_term | termFormater(lend.period) }}</h4>
          <div class="panel-content">
            <p class="principal panel-item">
              <span class="label">本期未还借款本金</span>
              <span class="value">￥{{ lend.need_repay_principal }}</span>
            </p>
            <p class="overdue-days panel-item">
              <span class="label">逾期天数</span>
              <span class="value">{{ lend.overdue_days }}天</span>
            </p>
            <p class="overdue-fine panel-item">
              <span class="label">逾期罚金</span>
              <span class="value">￥{{ lend.overdue_fine }}</span>
            </p>
            <p class="refund-amount panel-item">
              <span class="label">应还总额</span>
              <span class="value">￥{{ lend.term_amount }}</span>
            </p>
          </div>
          <div class="btn-confirm" @click="billConfirm">确定</div>
        </div>
      </div>
    </div>
    <footer class="refund-detail-footer">
      <div class="btn-early-settle" @click="earlySettle">提前结清</div>
      <div class="btn-refund-immediately" @click="refundImediately">立即还款</div>
      <p-settle class="settle" v-show="isSettleShow" @close="isSettleShow = false" :loan-id="loanId" ref="settle"></p-settle>
      <!-- 请求连连支付第三方接口还款申请 -->
      <form ref="form" action="https://wap.lianlianpay.com/authpay.htm" method="POST">
        <input type="hidden" name="req_data" :value="imediateData">
      </form>
    </footer>
  </div>
</template>

<script>
import RHeader from '@/components/APageHeader/APageHeader'
import PSettle from './Settle'

import { createNamespacedHelpers } from 'vuex'
const namespace = 'container/me/refund'
const { mapGetters, mapActions } = createNamespacedHelpers(namespace)

import { format } from 'date-fns'

export default {
  data () {
    return {
      loanId: 0,
      isBillPanelShow: false,
      isSettleShow: false,
      isImediatelyShow: false
    }
  },
  created () {
    this.loanId = this.$route.params.loanId
    this._initData()
  },
  watch: {
    '$route.params.loanId' (newVal, oldVal) {
      if (newVal !== undefined) {
        this.loanId = newVal
        this._initData()
      } else {
        this._clearData()
      }
    }
  },
  computed: {
    ...mapGetters([
      'lend', 'imediateData'
    ]),
    isOverdue () {
      return this.overdue_days > 0
    }
  },
  methods: {
    ...mapActions([
      'fetchLend', 'doImediate'
    ]),
    _initData () {
      this.fetchLend({ loan_id: this.loanId })
    },
    _clearData () {
      // the same as detroyed hook,
      // but these componets are cached, so watch the updated
      // route params to init the internal state for these components
      this.loanId = 0
      this.isBillPanelShow = false
      this.isSettleShow = false
      this.isImediatelyShow = false
    },
    openBillPanel () {
      this.isBillPanelShow = true
    },
    billConfirm () {
      this.isBillPanelShow = false
    },
    earlySettle () {
      this.isSettleShow = true
      this.$refs.settle.show()
    },
    async refundImediately () {
      await this.doImediate({ loan_id: this.loanId })
      this.$nextTick(() => {
        this.$refs.form.submit()
      })
    }
  },
  filters: {
    figure (val) {
      return Number(val).toFixed(2)
    },
    termFormater (term, period) {
      let convert2tow = function (num) {
        return ('0' + num).substr(-2)
      }
      return `${convert2tow(term)}/${convert2tow(period)}期`
    },
    dateFormater (timestamp) {
      return format(timestamp * 1000, 'YYYY-MM-DD')
    }
  },
  components: {
    RHeader,
    PSettle
  }
}
</script>

<style lang="stylus" scoped>
@import '../../../../../../common/stylus/mixin.styl'
@import '../../../../../../common/stylus/variable.styl'

.page-refund-detail
  .refund-detail-header
    border-1px($color-border-grey-light, 'bottom')
  .refund-detail-content
    .left-refund
      position: relative
      height: 100px
      font-size: 0
      color: $color-text-white
      background: linear-gradient(to bottom, $color-yellow, #f1b34c)
      .title-tip
        padding: 15px 0 0 15px
        font-size: 16px
      .left-amount
        margin-top: 10px
        text-align: center
        font-size: 36px
      .refund-date-tip
        absolute: right 16px top 16px
        font-size: 16px
    .current-refund
      position: relative
      margin-top: 16px
      border-1px($color-border-grey-light, 'top')
      .title-tip
        line-height: 40px
        font-size: 18px
        padding: 0 16px 0 16px
        border-1px($color-border-grey-light, 'bottom')
      .current-count
        padding: 0 16px 0 16px
        line-height: 40px
        font-size: 24px
        color: $color-text-yellow
      .current-date
        padding: 0 16px 16px 16px
        font-size: 0
        border-1px($color-border-grey-light, 'bottom')
        .term, .date
          font-size: 16px
        .date
          margin-left: 16px
      .state
        absolute: top 45px right 10px
        font-size: 16px
        .state-text
          display: block
          text-align: right
          margin-right: 6px
          &.overdue
            color: $color-text-yellow
        .btn-bill-detail
          display: block
          margin-top: 10px
          padding: 6px
    .bill-panel-wrap
      fixed: left top
      bottom: 55px
      width: 100%
      height: 100%
      z-index: 99
      background-color: rgba(0, 0, 0, .4)
      .bill-panel
        absolute: left 50% top
        width: 290px
        height: 224px
        margin: 120px 0 0 -145px
        border-radius: 5px 5px
        background-color: $color-white
        .panel-title
          height: 50px
          line-height: 50px
          text-align: center
          font-size: 18px
          border-1px($color-border-grey-light, 'bottom')
        .panel-content
          height: 130px
          padding: 5px 16px 5px 16px
          border-1px($color-border-grey-light, 'bottom')
          .panel-item
            font-size: 0
            .label, .value
              display: inline-block
              vertical-align: top
              line-height: 30px
              font-size: 16px
            .label
              width: 60%
            .value
              width: 40%
              text-align: right
        .btn-confirm
          line-height: 45px
          text-align: center
          font-size: 18px
    .children-page-container
      z-index: 66
  .refund-detail-footer
    fixed: left bottom
    width: 100%
    height: 55px
    font-size: 0
    .btn-early-settle, .btn-refund-immediately
      display: inline-block
      width: 50%
      height: 55px
      line-height: 55px
      text-align: center
      font-size: 18px
      border-1px($color-border-grey-light, 'top')
    .btn-refund-immediately
      color: $color-text-white
      background-color: $color-yellow
      border-left: 1px solid $color-border-grey-light
</style>
