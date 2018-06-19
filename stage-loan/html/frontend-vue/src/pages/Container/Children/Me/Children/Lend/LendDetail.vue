<!--
@Date:   2018-01-10T17:23:00+08:00
@Last modified time: 2018-01-30T13:43:01+08:00
-->
<template lang="html">
  <div class="page-lend-detail">
    <l-d-header class="lend-detail-header" not-has-confirm>
      <span class="title" slot="title-text">借款详情</span>
    </l-d-header>
    <group class="lend-detail-content" gutter="0">
      <cell class="use" title="借款项目" :value="lend.use"></cell>
      <cell class="date" title="借款时间" :value="lend.created_at | dateFormater"></cell>
      <cell class="quota" title="借款金额" :value="`￥${lend.quota}元`"></cell>
      <cell class="periods" title="借款期数" :value="`${lend.period}期`"></cell>
      <cell class="monthly" title="每期应还" :value="`${lend.monthly}元`"></cell>
    </group>
    <div class="lend-detail-logs" v-if="logs">
      <timeline>
  			<timeline-item :class="{
          'not-checked': !log.created_at }" v-for="(log, index) in logs" :key="index">
          <div class="item-wrap">
            <span class="index">{{ index + 1 }}</span>
    				<h4 :class="" class="title">{{ log.title | titleFormater}}<span class="time">{{ log.created_at | timeFormater }}</span></h4>
    				<p :class="" class="content">{{ log.content }}</p>
          </div>
  			</timeline-item>
  		</timeline>
    </div>
    <div class="lend-detail-plan" v-if="plans">
      <div class="plan-header">
        <span class="title">还款计划</span>
        <span class="summary">已还{{ repayment }}元，待还{{ Number(surplus).toFixed(2) }}元</span>
      </div>
      <scroller class="plan-body" lock-x scrollbar-y  height="-325">
        <div class="plan-tablet">
          <ul class="plan-list">
            <li class="plan-item" v-for="(plan, index) in plans" :class="{ 'is-finished': plan.state === 'finished', 'is-overdue': plan.state === 'overdue' }">
              <span class="item-index">{{ `0${index + 1 }`.substr(-2) }}</span>
              <span class="item-monthly">{{ plan.monthly }}元</span>
              <span class="item-repaying-date">{{ plan.planned_repayment_at | noHMSdateFormater }}</span>
              <span class="item-state" >{{ plan.state | stateFormater }}</span>
            </li>
          </ul>
        </div>
      </scroller>
    </div>
  </div>
</template>

<script>
import LDHeader from '@/components/APageHeader/APageHeader'
import { Group, Cell, Timeline, TimelineItem, Scroller } from 'vux'

import { createNamespacedHelpers } from 'vuex'
const namespace = 'container/me/lend/lends'
const { mapGetters, mapActions } = createNamespacedHelpers(namespace)

import { format } from 'date-fns'

const logMap = [
  {
    state: 'submit_success',
    value: '提交资料成功'
  },
  {
    state: 'auditing',
    value: '审核中'
  },
  {
    state: 'audit_success',
    value: '审核通过'
  },
  {
    state: 'audit_failure',
    value: '审核未通过'
  },
  {
    state: 'confirming',
    value: '商家确认中'
  },
  {
    state: 'confirm_success',
    value: '商家确认通过'
  },
  {
    state: 'confirm_failure',
    value: '商家确认未通过'
  },
  {
    state: 'granting',
    value: '放款中'
  },
  {
    state: 'grant_failure',
    value: '放款失败'
  },
  {
    state: 'grant_success',
    value: '放款成功'
  },
  {
    state: 'finished',
    value: '已还完'
  },
  {
    state: 'submit',
    value: '提交资料'
  },
  {
    state: 'audit',
    value: '审核中'
  },
  {
    state: 'confirm',
    value: '商家确认'
  },
  {
    state: 'loan',
    value: '借款成功'
  }
]

const planStateDict = {
  overdue: '逾期',
  repaying: '待还',
  finished: '已还'
}

export default {
  data () {
    return {
    }
  },
  created () {
    this.$store.commit(`${namespace}/UPDATE_LOAN_ID_FOR_LENDS`, { loanId: this.$route.params.loanId })
    this.fetchLendDetail()
  },
  watch: {
    'route.params.loanId' (newVal) {
      this.$store.commit(`${namespace}/UPDATE_LOAN_ID_FOR_LENDS`, { loanId: this.$route.params.loanId })
      this.fetchLendDetail()
    }
  },
  computed: {
    ...mapGetters([
      'lend', 'logs', 'plans', 'repayment', 'surplus'
    ])
  },
  methods: {
    ...mapActions([
      'fetchLendDetail'
    ])
  },
  filters: {
    dateFormater (timestamp) {
      return format(timestamp * 1000, 'YYYY-MM-DD HH:mm:ss')
    },
    noHMSdateFormater (timestamp) {
      return format(timestamp * 1000, 'YYYY-MM-DD')
    },
    titleFormater (type) {
      let el = logMap.find(item => item.state === type)
      return el ? el.value : ''
    },
    timeFormater (timestamp) {
      if (timestamp) {
        return format(timestamp * 1000, 'MM-DD HH:mm')
      }
    },
    stateFormater (state) {
      return planStateDict[state]
    }
  },
  components: {
    LDHeader,
    Group,
    Cell,
    Timeline,
    TimelineItem,
    Scroller
  }
}
</script>

<style lang="stylus">
@import '../../../../../../common/stylus/mixin.styl'
@import '../../../../../../common/stylus/variable.styl'

.page-lend-detail
  .lend-detail-header
    border-1px($color-border-grey-light, 'bottom')
  .lend-detail-content
    .weui-cells
      .weui-cell
        height: 45px !important
        .weui-cell__ft
          color: $color-text-black
  .lend-detail-logs
    margin-top: 20px
    font-size: 18px
    .vux-timeline
      padding-top: 8px
      .vux-timeline-item
        &.not-checked
          .vux-timeline-item-color, .vux-timeline-item-tail
            background-color: $color-grey
          .vux-timeline-item-content
            .item-wrap
              .index
                color: $color-text-grey-higher
              .title
                color: $color-text-grey-higher
                background-color: #eee
              .content
                display: none
        .vux-timeline-item-color
          top: -4px
          left: -5px
          width: 24px
          height: 24px
          z-index: -1
          .vux-timeline-item-checked
            display: none
        .vux-timeline-item-tail
          width: 6px
          z-index: -2
        .vux-timeline-item-content
          padding: 0 0 16px 32px
          .item-wrap
            border .5px solid $color-border-grey
            .index
              absolute: left 3px top
              font-size: 16px
              color: $color-text-white
            .title
              position: relative
              height: 30px
              line-height: 30px
              padding-left: 10px
              font-size: 16px
              color: $color-text-yellow
              border-1px($color-border-grey-light, 'bottom')
              .time
                absolute: top right 24px
                font-size: 12px
                color: $color-text-grey
            .content
              min-height: 30px
              line-height: 30px
              padding-left: 10px
              font-size: 14px
              color: $color-text-black
  .lend-detail-plan
    .plan-header
      height: 45px
      line-height: 45px
      font-size: 0
      color: $color-text-white-high
      background-color: $color-yellow
      .title
        display: inline-block
        padding-left: 20px
        font-size: 18px
      .summary
        display: inline-block
        padding-left: 24px
        font-size: 14px
    .plan-body
      .plan-tablet
        .plan-list
          padding: 0 16px
          .plan-item
            height: 45px
            line-height: 45px
            font-size: 0
            color: $color-text-yellow
            &.is-overdue
              color: $color-text-red
            &.is-finished
              color: $$color-text-grey-higher
            .item-index, .item-monthly, .item-repaying-date, .item-state
              display: inline-block
              font-size: 16px
            .item-index
              padding-left: 27px
            .item-monthly
              padding-left: 40px
            .item-repaying-date
              padding-left: 49px
            .item-state
              padding-left: 15px
</style>
