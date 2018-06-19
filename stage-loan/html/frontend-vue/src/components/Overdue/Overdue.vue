<template>
  <div class="overdue">
    <overdue-title>您的本期借款<span v-if="!isUpperLimit" class="color-text-warn">已逾期{{ overdueInfo.overdue_day }}天</span><span v-if="isUpperLimit" class="color-text-warn">已逾期365天</span></overdue-title>
    <div class="overdue-content">
      <div class="title-wrap">
        <div class="overdue-box-img">
          <img src="./box_overdu.png" width="77" height="77">        
        </div>
        <p class="overdue-days">{{overdueDaysStr}}</p>
      </div>
      <div class="calc-info">
        <p class="upper-limit-tip" v-if="isUpperLimit">
          <span class="icon_tip"></span>
          <span class="text">逾期时间：达到逾期上限，不再累加</span>
        </p>
        <p class="calc-equation">
          <span class="principal">应还金额</span>
          <span class="plus-sign">+</span>
          <span class="overdue-fines">逾期罚金</span>
          <span class="btn-doubt" @click="showDoubtPanel"><span class="icon_btn_doubt"></span></span>
          <span class="equal-sign">=</span>
          <span class="need-to-refund">未还金额</span>
        </p>
        <p class="calc-number">
          <span class="number-principal">{{ overdueInfo.overdue_principal }}元</span>
          <span class="number-plus-sign">+</span>
          <span class="number-overdue-fines">{{ overdueInfo.overdue_amount }}元</span>
          <span class="number-equal-sign">=</span>
          <span class="number-refund">{{ overdueInfo.refund }}元</span>
        </p>
        <transition name="panel-fade">
          <div class="doubt-panel" v-show="doubtPanelShow">
            <fine-doubt-panel @close="hideDoubtPanel"></fine-doubt-panel>
          </div>
        </transition>
      </div>
      <div class="warning-info-warp">
        <ol class="warning-info">
          <li class="warning-info-item">恶意逾期将上报人行征信黑名单</li>
          <li class="warning-info-item">如遇困难，请联系我们客服：400-123-123</li>
        </ol>
      </div>
    </div>
    <div class="overdue-footer">
      <x-button class="btn-refund" plain type="primary" action-type="button" @click.native="doRefund">立即还款</x-button>
    </div>
    <!-- 请求连连支付第三方接口还款申请 -->
    <form ref="form" action="https://wap.lianlianpay.com/authpay.htm" method="POST">
      <input type="hidden" name="req_data" :value="refundData">
    </form>
  </div>
</template>

<script>
import OverdueTitle from '@/components/HomeContentTitle/HomeContentTitle'
import FineDoubtPanel from './FineDoubtPanel'
import { XButton } from 'vux'

import { createNamespacedHelpers } from 'vuex'
const namespace = 'home'
const { mapGetters, mapActions } = createNamespacedHelpers(namespace)

export default {
  name: 'overdue',
  data () {
    return {
      doubtPanelShow: false
    }
  },
  created () {
  },
  mounted () {
    if (this.isUpperLimit) {
      this.$el.querySelector('.overdue-footer').style.setProperty('margin-top', '8px')
    }
  },
  props: {
    isUpperLimit: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    ...mapGetters([
      'orderDetail', 'refundData'
    ]),
    overdueInfo () {
      let info = this.orderDetail[0].content
      info.refund = (info.overdue_principal + info.overdue_amount).toFixed(2)
      return info
    },
    overdueDaysStr () {
      return (`00${this.overdueInfo.overdue_day}`).substr((this.overdueInfo.overdue_day + '').length)
    }
  },
  methods: {
    ...mapActions([
      'refund'
    ]),
    showDoubtPanel () {
      this.doubtPanelShow = !this.doubtPanelShow
    },
    hideDoubtPanel () {
      this.doubtPanelShow = !this.doubtPanelShow
    },
    async doRefund () {
      await this.refund()
      this.$nextTick(() => {
        this.$refs.form.submit()
      })
    }
  },
  components: {
    OverdueTitle,
    FineDoubtPanel,
    XButton
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus">
@import '../../common/stylus/mixin.styl'
@import '../../common/stylus/variable.styl'

.overdue
  .title-wrap
    position relative
    margin-top 16px
    .overdue-box-img
      width 77px
      height 77px
      margin 0 auto
      img
        width 100%
    .overdue-days
      position absolute
      left 50%
      top 50%
      width 50px
      height 50px
      line-height 50px
      margin -16px 0 0 -25px
      font-size 48px
      color $color-text-yellow
  .overdue-content
    .calc-info
      margin-top 16px
      text-align center
      .upper-limit-tip
        font-size 0
        .icon_tip
          margin-top 2px
          inline-icon(15px, 15px)
          bg-img('./icon_overdue_tip')
        .text
          margin-left 4px
          font-size 12px
          color $color-text-yellow
      .calc-equation
        font-size 0
        span
          font-size 13px
          color $color-text-black
          &.principal
            margin-right 8px
          &.plus-sign
            margin-right 8px
          &.btn-doubt
            padding 4px
            .icon_btn_doubt
              margin-top 2px
              inline-icon(15px, 15px)
              bg-img('./icon_overdue_fine')
          &.equal-sign
            margin-right 4px
      .calc-number
        margin-top 4px
        font-size 0
        span
          font-size 13px
          color $color-text-black
          &.number-plus-sign, &.number-equal-sign
            margin 0 4px
          &.number-refund
            font-size 15px
            color $color-text-yellow
      .doubt-panel
        position fixed
        top 0
        left 0
        bottom 0
        width 100%
        overflow hidden
        z-index 99
        &.panel-fade-enter-active, &.panel-fade-leave-active
          transition all .6s ease
        &.panel-fade-enter, &.panel-fade-leave-to
          opacity 0
    .warning-info-warp
      margin-top 8px
      padding-left 33px
      font-size 11px
      color $color-text-grey-higher
      .warning-info
        .warning-info-item
          line-height 1.6em
  .overdue-footer
    margin-top 8px
    .btn-refund
      // width 250px
      width 80%
      height 40px
      margin 0 auto
      border-radius 20px 20px
      color $color-text-blue
      border-color $color-blue
      &:active
        color $color-text-blue-lightter
        border-color $color-blue-light
</style>