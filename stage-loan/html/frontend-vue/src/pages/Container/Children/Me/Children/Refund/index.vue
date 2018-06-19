<!--
@Date:   2018-01-04T16:44:01+08:00
@Last modified time: 2018-01-23T16:25:00+08:00
-->
<template lang="html">
  <div class="page-refund">
    <r-header class="refund-header" not-go-back :not-has-confirm="true" @go-back="goBack">
      <span class="title" slot="title-text">我要还款</span>
    </r-header>
    <div class="refund-content">
      <div class="latest-refund">
        <h3 class="title">近30天待还款</h3>
        <p class="value">￥{{ amount }}元</p>
      </div>
      <scroll class="scroller"
              ref="scroll"
              :data="lends"
              :options="scrollOptions"
              @pulling-down="onPullingDown"
              @pulling-up="onPullingUp">
          <li class="lend-item" v-for="(lend, index) in lends" @click="previewDetail(lend.loan_id)">
            <div class="logo-wrap">
              <i class="icon" :class="iconClassMap(lend.type)"></i>
            </div>
            <div class="desc">
              <p class="term-amount">￥{{ lend.term_amount }}</p>
              <p class="loan-summary">
                <span class="period">({{ lend.current_term | termFormater(lend.period) }})</span>
                <span class="quota">借款{{ lend.quota }}元</span>
              </p>
              <p class="apply-date">
                <span class="label">申请时间：</span>
                <span class="value">{{ lend.created_at | dateFormater }}</span>
              </p>
              <p class="repay-date">
                <span class="label">还款时间：</span>
                <span class="value">{{ lend.repayment_at | dateFormater }}</span>
              </p>
            </div>
            <div class="extra">
              <span class="loan-state" :class="stateClassMap(lend.overdue_days)">{{ isOverdue(lend.overdue_days) }}</span>
              <span class="repay-tip">还款<i class="icon-arrow-right"></i></span>
            </div>
          </li>
      </scroll>
      <transition name="router-fade">
        <keep-alive>
          <router-view class="refund-detail-container"></router-view>
        </keep-alive>
      </transition>
    </div>
  </div>
</template>

<script>
import { Scroll } from 'cube-ui'
import RHeader from '@/components/APageHeader/APageHeader'

import { createNamespacedHelpers } from 'vuex'
const namespace = 'container/me/refund'
const { mapGetters, mapActions } = createNamespacedHelpers(namespace)

import { format } from 'date-fns'

export default {
  data () {
    return {
      scrollOptions: {
        scrollbar: true,
        pullDownRefresh: {
          threshold: 90,
          stop: 40,
          txt: '刷新成功'
        },
        pullUpLoad: {
          threshold: 0,
          txt: {
            more: '加载更多',
            noMore: '没有更多数据了'
          }
        }
      }
    }
  },
  created () {
    this.fetchLends()
  },
  computed: {
    ...mapGetters([
      'amount',
      'lends'
    ])
  },
  methods: {
    ...mapActions([
      'fetchLends'
    ]),
    goBack () {
      this.$router.push('/me')
    },
    onPullingDown () {
      this.$store.commit(`${namespace}/INIT_DATA_FOR_LENDS_IN_REFUND`)
      this.fetchLends({ offset: 0 })
    },
    onPullingUp () {
      if (this.hasMore) {
        this.fetchLends()
      } else {
        this.$refs.scroll.forceUpdate()
      }
    },
    iconClassMap (type) {
      let clsName = ''
      switch (type) {
        case '1':
          clsName = 'icon-cash-logo'
          break
        case '2':
          clsName = 'icon-consume-logo'
          break
        default:
          break
      }
      return clsName
    },
    stateClassMap (overdueDays) {
      return overdueDays > 0 ? 'overdue' : 'normal'
    },
    isOverdue (overdueDays) {
      let maxOverdueDays = this.lends[0].max_overdue_days
      if (overdueDays < maxOverdueDays) { // not accumulatived to max overdue days
        return overdueDays > 0 ? `逾期${overdueDays}天` : '正常'
      } else {
        return `逾期${maxOverdueDays}天`
      }
    },
    previewDetail (loanId) {
      this.$router.push(`/me/refund/detail/${loanId}`)
    }
  },
  filters: {
    termFormater (term, period) {
      let convert2tow = function (num) {
        return ('0' + num).substr(-2)
      }
      return `${convert2tow(term)}/${convert2tow(period)}`
    },
    dateFormater (timestamp) {
      return format(timestamp * 1000, 'YYYY-MM-DD')
    }
  },
  components: {
    RHeader,
    Scroll
  }
}
</script>

<style lang="stylus">
@import '../../../../../../common/stylus/mixin.styl'
@import '../../../../../../common/stylus/variable.styl'

.page-refund
  .refund-header
    border-1px($color-border-grey-light, 'bottom')
  .refund-content
    .latest-refund
      height: 100px
      padding: 15px 0 0 10px
      color: $color-text-white
      background-color: $color-yellow
      .title
        font-size: 16px
      .value
        margin-top: 8px
        text-align: center
        font-size: 36px
    .scroller
      height: 538px
      margin-top: 12px
      overflow: hidden
      border-1px($color-border-grey-light, 'top')
      .lend-item
        position: relative
        height: 100px
        font-size: 0
        border-1px($color-border-grey-light, 'bottom')
        .logo-wrap
          display: inline-block
          vertical-align: top
          margin-right: 18px
          padding: 24px 0 0 16px
          .icon
            font-size: 50px
            color: $color-text-yellow
        .desc
          display: inline-block
          vertical-align: top
          margin-top: 15px
          font-size: 14px
          color: $color-text-black
          .term-amount
            line-height: 24px
            font-size: 18px
          .loan-summary
            line-height: 20px
            color: $color-text-grey
          .apply-date, .repay-date
            line-height: 18px
        .extra
          absolute: top 32px right 8px
          font-size: 18px
          .loan-state
            display: block
            &.normal
              color: $color-text-green
            &.overdue
              color: $color-text-warn
          .repay-tip
            display: block
            .icon-arrow-right
              font-size: 16px
              margin-left: 6px
    .refund-detail-container
      position: fixed
      left: 0
      top: 0
      bottom: 0
      width: 100%
      background-color: $color-white-high
      &.router-fade-enter-active, &.router-fade-leave-active
        transition: all .6s ease
      &.router-fade-enter, &.router-fade-leave-to
        opacity: 0
</style>
