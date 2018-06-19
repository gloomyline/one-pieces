<template>
  <div class="wait-refund">
    <wait-refund-title>距离本期还款日还有{{ leftRefundDays }}天，请您注意及时还款</wait-refund-title>
    <div class="wait-refund-content">
      <div class="wait-refund-content-wrap clearfix">
        <div class="refund-left-days">
          <w-circle :percent="refundDatePersent" trailColor="#d8f3fb">
            <div class="refund-left-days-content">
              <p class="title">计划还款日期:</p>
              <p class="date">{{ orderInfo.planned_repayment_at | dateFormatter}}</p>
            </div>
          </w-circle>
        </div>
        <div class="refund-wait-count">
          <w-circle :percent="refundCountPersent" trailColor="#d8f3fb">
            <div class="refund-wait-count-content">
              <p class="title">待还金额:</p>
              <p class="count">{{ orderInfo.repay_amount }}元</p>
            </div>
          </w-circle>
        </div>
      </div>
    </div>
    <div class="wait-refund-footer">
      <x-button class="btn-refund" plain type="primary" action-type="button" @click.native="doRefund">立即还款</x-button>
    </div>
    <!-- 请求连连支付第三方接口还款申请 -->
    <form ref="form" action="https://wap.lianlianpay.com/authpay.htm" method="POST">
      <input type="hidden" name="req_data" :value="refundData">
    </form>
  </div>
</template>

<script>
import WaitRefundTitle from '@/components/HomeContentTitle/HomeContentTitle'
import WCircle from '@/components/WCircle/WCircle'
import { XButton } from 'vux'
import TWEEN from '@tweenjs/tween.js'

import { createNamespacedHelpers } from 'vuex'
const namespace = 'home'
const { mapGetters, mapActions } = createNamespacedHelpers(namespace)

import { format, differenceInDays } from 'date-fns'

export default {
  name: 'waitrefund',
  data () {
    return {
      refundDatePersent: 0,
      refundCountPersent: 0
    }
  },
  created () {
  },
  mounted () {
    this._autoPlayOnce()
  },
  computed: {
    ...mapGetters([
      'orderDetail', 'refundData'
    ]),
    orderInfo () {
      return this.orderDetail[0].content
    },
    leftRefundDays () {
      let deadLine = format(this.orderInfo.planned_repayment_at * 1000, 'YYYY-MM-DD')
      let deadLineDate = new Date(deadLine)
      return differenceInDays(deadLineDate, new Date())
    }
  },
  methods: {
    ...mapActions([
      'refund'
    ]),
    _autoPlayOnce (cb) {
      this.playAnimation('date', cb)
      this.playAnimation('count', cb)
      this._optimizeAnimation()
    },
    _optimizeAnimation () {
      let animate = function () {
        if (TWEEN.update()) {
          requestAnimationFrame(animate)
        }
      }
      animate()
    },
    playAnimation (animationName, cb) {
      let tweeningObj = { tweeningPercent: 0 }
      new TWEEN.Tween(tweeningObj) /* eslint-disable no-new */
              .easing(TWEEN.Easing.Quartic.Out)
              .to({tweeningPercent: 100}, 2000)
              .onUpdate(() => {
                if (animationName === 'date') {
                  this.refundDatePersent = tweeningObj.tweeningPercent
                } else if (animationName === 'count') {
                  this.refundCountPersent = tweeningObj.tweeningPercent
                }
              })
              .onComplete(cb || function () { return })
              .delay(1000)
              .start()
    },
    async doRefund () {
      await this.refund()
      this.$nextTick(() => {
        this.$refs.form.submit()
      })
    }
  },
  filters: {
    dateFormatter (timestamp) {
      return format(timestamp * 1000, 'YYYY-MM-DD')
    }
  },
  components: {
    WaitRefundTitle,
    WCircle,
    XButton
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus">
@import '../../common/stylus/variable.styl'

.wait-refund
  .wait-refund-content
    margin-top 20px
    .wait-refund-content-wrap
      width 100%
      padding 0 32px
      font-size 0
      .refund-left-days
        float left
        width 135px
        height 135px
        .refund-left-days-content
          left 50%
          top 30%
          .title
            font-size 16px
            color $color-text-black
          .date
            font-size 16px
            color $color-text-black
      .refund-wait-count
        float right
        width 135px
        height 135px
        .refund-wait-count-content
          left 50%
          top 30%
          .title
            font-size 16px
            color $color-text-black
          .count
            font-size 16px
            color $color-text-black
  .wait-refund-footer
    margin-top 30px
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