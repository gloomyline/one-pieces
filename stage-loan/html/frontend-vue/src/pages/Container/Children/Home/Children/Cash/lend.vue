<!--
@Date:   2017-12-25T13:35:12+08:00
@Last modified time: 2018-02-05T15:46:26+08:00
-->
<template lang="html">
  <div class="page-cash-lend">
    <c-l-header class="cash-lend-header" :not-has-confirm="true"><span class="title" slot="title-text">现金分期</span></c-l-header>
    <div class="cash-lend-content">
      <div class="lend-count-wrap">
        <div class="bg-img-wrap">
          <img :src="bgImg" class="bg-img" width="100%" height="100%">
        </div>
        <group class="lend-count" gutter="0" title="借款金额（元）">
          <x-input class="lend-input" title="￥" v-model="lendCountTxt" :show-clear="false" type="number"></x-input>
          <cell class="tip-count" :value="lendTip" :border-intent="false"></cell>
          <cell class="tip-interest" value="按日计息，支持随借随还" :border-intent="false"></cell>
        </group>
      </div>
      <div class="lend-detail-wrap">
        <group class="lend-detail" gutter="0">
          <x-address class="use-direction" title="消费用途" :list="comUses" v-model="comUse"></x-address>
          <x-address class="periods" title="分期数（月）" :list="comPeriods" v-model="comPeriod"></x-address>
          <cell class="refund-per-month" title="每月还款" :value="`￥${averageRefund}`" :border-intent="false"></cell>
          <cell class="refund-summary" title="合计需还" :value="`￥${summaryRefund}`" is-link @click.native="openRefundDetailPanel" :border-intent="false"></cell>
          <cell class="to-bank-account" title="到账银行" :value="bankcardTxt" :border-intent="false"></cell>
        </group>
      </div>
    </div>
    <div class="cash-lend-footer">
      <div class="cash-lend-protocol-link-wrap">
        <span class="icon-check-wrap" @click="isProtocolChecked = !isProtocolChecked"><i class="icon" :class="{'icon-box-checked': isProtocolChecked, 'icon-box-unchecked': !isProtocolChecked}"></i></span>
        <span class="cash-lend-protocol-link"><span class="label">同意</span><span class="protocol-link btn-open-protocol" @click="openProtocol">《借款合同》</span></span>
      </div>
      <div class="btn-confirm-lend" @click="confirmLend">确认借款</div>
      <transition name="page-fade">
        <page-protocol class="page-cash-lend-protocol" ref="protocol" protocol-type="lendContract" v-show="isProtocolShow" @close="closeProtocol"></page-protocol>
      </transition>
      <transition name="panel-fade">
        <page-refund-plan class="page-refund-plan" ref="refundPlan" :rows="refundPlan" :sum="summaryRefund" :fine="fine" v-show="isRefundPlanShow" @close="closeRefundPlan"></page-refund-plan>
      </transition>
    </div>
  </div>
</template>

<script>
import CLHeader from '@/components/APageHeader/APageHeader'
import PageProtocol from '@/pages/Protocol'
import PageRefundPlan from './refundPlan'
import { Group, XInput, XAddress, Cell } from 'vux'

import { format, addMonths } from 'date-fns'
import { createNamespacedHelpers, mapState } from 'vuex'
const namespace = 'container/home/cash'
const { mapGetters, mapActions } = createNamespacedHelpers(namespace)

export default {
  name: 'cashLend',
  data () {
    return {
      bgImg: require('@/assets/imgs/cash-consume-bg.jpg'),
      isProtocolChecked: true,
      isProtocolShow: false,
      isRefundPlanShow: false,
      fine: 0, // the fine of lend
      timerId: null
    }
  },
  created () {
    this.fetchCashLendData()
  },
  watch: {
  },
  computed: {
    ...mapState({
      minQuota: state => state.user.quota.min_quota,
      maxQuota: state => state.user.quota.max_quota
    }),
    ...mapGetters([
      'lendCount',
      'uses',
      'use',
      'periods',
      'period',
      'rate',
      'bankcardName',
      'bankcardNo'
    ]),
    lendCountTxt: {
      get () {
        return `${Math.floor(this.lendCount)}`
      },
      set (val) {
        let num = Number(val)
        val = num > this.maxQuota
          ? this.maxQuota : num < this.minQuota
          ? this.minQuota : num
        let base100 = val / 100
        if (Math.floor(base100) !== base100) {
          val = Math.floor(base100) * 100
        }
        if (this.timerId) clearTimeout(this.timerId)
        this.timerId = setTimeout(() => {
          this.$store.commit(`${namespace}/UPDATE_FOR_CASH`, { key: 'lendCount', val })
          this.timerId = null
        }, 800)
      }
    },
    lendTip () {
      return `请输入￥${this.minQuota}-￥${this.maxQuota}之间的整百金额`
    },
    bankcardTxt () {
      return `${this.bankcardName} 尾号（${this.bankcardNo}）`
    },
    comUses () {
      const useList = []
      this.uses.forEach((item, index) => {
        useList.push({ name: item, value: String(index) })
      })
      return useList
    },
    comPeriods () {
      const periodList = []
      this.periods.forEach((item, index) => {
        periodList.push({ name: item, value: String(index) })
      })
      return periodList
    },
    comUse: {
      get () {
        return this.use
      },
      set (val) {
        this.$store.commit(`${namespace}/UPDATE_FOR_CASH`, { key: 'use', val })
      }
    },
    comPeriod: {
      get () {
        return this.period
      },
      set (val) {
        this.$store.commit(`${namespace}/UPDATE_FOR_CASH`, { key: 'period', val })
      }
    },
    periodN () { // the num of period
      return this.periods[Number(this.period[0])]
    },
    averageRefund () {
      return (this.summaryRefund / this.periodN).toFixed(2)
    },
    summaryRefund () {
      return this.calc(this.lendCount, this.periodN)
    },
    refundPlan () {
      let today = new Date()
      let curYear = today.getFullYear()
      let curMonth = today.getMonth()
      let startMonth = today.getDate() >= 10 ? (curMonth + 1) : curMonth
      let startDate = addMonths(new Date(`${curYear}/${startMonth}/10`), 1)
      let averagePrincipal = (this.lendCount / this.periodN).toFixed(2)
      let fine = this.fine = Number(this.summaryRefund).toFixed(2) - this.lendCount
      let averageFine = (fine / this.periodN).toFixed(2)
      let PAI = (averageFine * 1 + averagePrincipal * 1).toFixed(2)
      const plan = []
      for (let i = 0; i < this.periodN; ++i) {
        let row = []
        row.push(format(addMonths(startDate, i), 'YYYY-MM-DD'))
        row = com(row)
        plan.push(row)
      }
      // add PAI, principal, fine
      function com (arr) {
        return Array.prototype.concat.call(
          arr,
          [PAI, averagePrincipal, averageFine]
        )
      }
      return plan
    }
  },
  methods: {
    ...mapActions([
      'fetchCashLendData',
      'confirmCashLend'
    ]),
    openRefundDetailPanel () {
      this.isRefundPlanShow = true
      this.$refs.refundPlan.show()
    },
    closeRefundPlan () {
      this.isRefundPlanShow = false
    },
    openProtocol () {
      this.isProtocolShow = true
      this.$refs.protocol.show()
    },
    closeProtocol () {
      this.isProtocolShow = false
    },
    async confirmLend () {
      if (!this.isProtocolChecked) {
        this._alertShow('请认真阅读借款合同后勾选', () => {
          this.isProtocolChecked = true
        })
        return
      }
      let postData = {
        amount: Number(this.lendCount),
        use: this.uses[Number(this.use[0])],
        period: this.periodN
      }
      let msg = await this.confirmCashLend(postData)
      this._alertShow(msg, () => { // if lend success, go to home
        this.$router.push('/home')
      })
    },
    calc (principal, period) { // calc the principal and interest
      let fine = principal * period * (this.rate.annualized_interest_rate / 12)
      let trial = principal * this.rate.trial_rate // 信审
      let service = principal * this.rate.service_rate
      let poundage = principal * this.rate.poundage // 手续
      return principal + fine + trial + service + poundage
    }
  },
  components: {
    CLHeader,
    PageRefundPlan,
    PageProtocol,
    Group,
    XInput,
    XAddress,
    Cell
  }
}
</script>

<style lang="stylus">
@import '../../../../../../common/stylus/mixin.styl'
@import '../../../../../../common/stylus/variable.styl'

.page-cash-lend
  .cash-lend-header
    border-1px($color-border-grey-light, 'bottom')
  .cash-lend-content
    width: 100%
    .lend-count-wrap
      position: relative
      height: 150px
      .bg-img-wrap
        absolute: left top
        height: 100%
        z-index: -1
      .lend-count
        color: $color-text-white
        .weui-cells__title
          margin-top: 0
          padding-top: 12px
          padding-left: 20px
          font-size: 18px
          color: $color-text-white
        .weui-cells
          &:after, &:before
            display: none
          background-color: transparent
          .weui-cell
            padding-left: 20px
            &:before
              display: none
            &.lend-input
              padding-top: 16px
              padding-bottom: 10px
              font-size: 40px
            &.tip-count
              margin-top: 12px
            &.tip-count, &.tip-interest
              height: 20px !important
              padding-left: 24px
              .weui-cell__ft
                font-size: 14px
                color: $color-text-white
            .vux-cell-bd
              display: none
    .lend-detail-wrap
      .lend-detail
        .weui-cells
          &:after, &:before
            display: none
          .weui-cell
            height: 50px !important
            font-size: 16px
            color: $color-text-black
  .cash-lend-footer
    margin-top: 20px
    .cash-lend-protocol-link-wrap
      font-size: 0
      .icon-check-wrap
        display: inline-block
        vertical-align: top
        padding: 4px 8px 4px 14px
        font-size: 20px
        color: $color-text-yellow
      .cash-lend-protocol-link
        display: inline-block
        vertical-align: top
        padding-top: 4px
        .label
          font-size: 16px
        .protocol-link
          font-size: 16px
          color: $color-text-yellow
    .page-protocol
      position: fixed
      left: 0
      top: 0
      bottom: 0
      width: 100%
      overflow: hidden
      background-color: $color-white-light
      &.page-fade-enter-active, &.page-fade-leave-active
        transition: all .6s ease
      &.page-fade-enter, &.page-fade-leave-to
        opacity: 0
    .page-refund-plan
      position: fixed
      left: 0
      top: 0
      bottom: 0
      width: 100%
      overflow: hidden
    .btn-confirm-lend
      fixed: left bottom
      width: 100%
      height: 55px
      line-height: 55px
      text-align: center
      font-size: 18px
      font-weight: bolder
      color: $color-text-white
      background-color: $color-yellow
      &:active
        box-shadow: inset 4px 4px 6px $color-shadow-black-24
</style>
