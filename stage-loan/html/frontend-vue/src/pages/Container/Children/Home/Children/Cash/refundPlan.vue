<!--
@Date:   2017-12-26T17:08:54+08:00
@Last modified time: 2018-02-01T17:17:02+08:00
-->
<template lang="html">
  <div class="page-plan">
    <div class="bg-wrap" @click="closePanel($event)"></div>
    <div class="plan-wrap">
      <header class="refund-plan-header">
        <h2 class="title">还款详情</h2>
        <span class="btn-close" @click="closePanel($event)"><i class="icon-cross"></i></span>
      </header>
      <div class="refund-detail">
        <div class="detail-table">
          <p class="detail-head">
            <span class="cell date">日期</span>
            <span class="cell monthly-installment">月供</span>
            <span class="cell principal">本金</span>
            <span class="cell fine">借款息费</span>
          </p>
          <div class="detail-body" ref="scroller">
            <div class="detail-body-wrap">
              <p class="detail-row" v-for="(row, j) in rows">
                <span class="cell" :class="classMap(k)" v-for="(cell, k) in row">{{ cell }}</span>
              </p>
            </div>
          </div>
        </div>
      </div>
      <footer class="refund-plan-footer">
        <p class="summary">
          <span class="title">合计：</span><span class="count">￥{{ sum }}元</span>
        </p>
        <p class="fine">（含借款息费￥{{ fine }}）</p>
      </footer>
    </div>
  </div>
</template>

<script>
import BScroll from 'better-scroll'

export default {
  data () {
    return {}
  },
  mounted () {
    this._initScroll()
  },
  props: {
    rows: {
      type: Array,
      default () {
        return [
          ['2017-08-09', '￥676.67', '￥676.67', '￥10.00'],
          ['2017-08-09', '￥676.67', '￥676.67', '￥10.00'],
          ['2017-08-09', '￥676.67', '￥676.67', '￥10.00'],
          ['2017-08-09', '￥676.67', '￥676.67', '￥10.00']
        ]
      }
    },
    sum: {
      type: Number,
      required: true
    },
    fine: {
      type: Number,
      required: true
    }
  },
  computed: {
  },
  methods: {
    show () {
      this.$nextTick(() => {
        this._initScroll()
      })
    },
    _initScroll () {
      if (!this.scroll) {
        this.scroll = new BScroll(this.$refs.scroller, {})
      } else {
        this.scroll.refresh()
      }
    },
    classMap (index) {
      let className = ''
      switch (index) {
        case 0:
          className = 'date'
          break
        case 1:
          className = 'monthly-installment'
          break
        case 2:
          className = 'principal'
          break
        case 3:
          className = 'fine'
          break
        default:
          break
      }
      return className
    },
    closePanel (event) {
      this.$emit('close', event)
    }
  }
}
</script>

<style lang="stylus">
@import '../../../../../../common/stylus/mixin.styl'
@import '../../../../../../common/stylus/variable.styl'

.page-plan
  .bg-wrap
    absolute: left top
    width: 100%
    height: 100%
    background-color: rgba(0, 0, 0, .4)
    z-index: -1
  .plan-wrap
    absolute: left 50% top 50%
    width: 350px
    height: 480px
    margin-top: -240px
    margin-left: -175px
    border-radius: 10px 10px
    background-color: $color-white
    .refund-plan-header
      position: relative
      height: 48px
      border-1px($color-border-grey-light, 'bottom')
      .title
        height: 100%
        line-height: 48px
        text-align: center
        font-size: 18px
        color: $color-text-yellow
      .btn-close
        absolute: right 8px top 8px
        padding: 6px
        font-size: 20px
    .refund-detail
      padding-top: 12px
      .detail-table
        font-size: 0
        color: $color-text-black
        .cell
          display: inline-block
          height: 36px
          line-height: 36px
          text-align: center
          font-size: 14px
          border-bottom: 1px solid $color-border-grey-light
          &.date
            width: 115px
          &.monthly-installment
            width: 75px
          &.principal
            width: 75px
          &.fine
            width: 85px
        .detail-body
          height: 324px
          overflow: hidden
          .detail-body-wrap
            .detail-row
              &:hover
                background-color: $color-grey-higher1
    .refund-plan-footer
      height: 72px
      text-align: center
      color: $color-text-white
      border-radius: 0 0px 10px 10px
      background-color: $color-yellow
      .summary
        padding-top: 2px
        font-size: 0
        .title
          font-size: 16px
        .count
          font-size: 28px
      .fine
        font-size: 14px
</style>
