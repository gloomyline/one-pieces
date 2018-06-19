<template>
  <div class="fail">
    <fail-title>很抱歉，您的借款申请审核失败。</fail-title>
    <div class="fail-content">
      <timeline class="order-item-list" color="#40d3fe" :isShowIcon="false">
        <timeline-item class="order-item" v-for="(item, index) in orderDetail" :key="index">
          <div class="order-item-index">{{orderDetail.length - index}}</div>
          <h4 class="order-item-title">{{item.title}}</h4>
          <p class="order-item-content" v-if="isReviewFailed(item.title)">原因：您的信用评分不足，您可以在认证中心检查、更新填写信息，并于冻结时间后重新提交申请</p>
          <p class="order-item-content" v-else>申请借款金额{{ item.content.quota }}元，期限{{ item.content.period }}天，手续费用{{ item.content.poundage }}元</p>
          <p class="order-item-time">{{ item.time }}</p>
        </timeline-item>
      </timeline>
    </div>
    <div class="fail-footer">
      <p class="freeze-cooling" v-show="freezeCoolingShow">冻结倒计时：{{ freezeCooling | dateFormat }}</p>
      <x-button class="btn-tip" plain type="primary" action-type="button" v-show="freezeCoolingShow">请您耐心等待</x-button>
      <x-button class="btn-blue btn-reapply" type="primary" action-type="button" v-show="!freezeCoolingShow" @click.native="reapply">重新借款</x-button>
    </div>
  </div>
</template>

<script>
import FailTitle from '@/components/HomeContentTitle/HomeContentTitle'
import { Timeline, TimelineItem, XButton } from 'vux'
// import moment from 'moment'
import { formatDateString } from '@/common/js/utils'

import { createNamespacedHelpers } from 'vuex'
const namespace = 'home'
const { mapGetters } = createNamespacedHelpers(namespace)
import { OrderStateMap } from '@/store/modules/Home'

export default {
  name: 'fail',
  data () {
    return {
      freezeCooling: 0,
      initCooling: 24 * 3600 * 1000,
      freezeCoolingShow: true
    }
  },
  created () {
    let failAt = this.orderDetail[0].content.failure_at * 1000                   // 订单审核失败时间, 单位ms
    this.freezeCooling = this.initCooling - (new Date().getTime() - failAt)     // 倒计时初始时间, 单位ms
  },
  mounted () {
    if (this.freezeCooling <= 0) {
      this.freezeCoolingShow = false
      return
    }

    this.intervalTimer = setInterval(() => {
      if (this.freezeCooling <= 0) {
        this.freezeCoolingShow = false
        clearInterval(this.intervalTimer)
        this.intervalTimer = null
        return
      }
      this.freezeCooling -= 1000
    }, 1000)
  },
  computed: {
    ...mapGetters([
      'orderDetail'
    ])
  },
  filters: {
    dateFormat (timeStamp) {
      return formatDateString(timeStamp)
    }
  },
  methods: {
    isReviewFailed (state) {
      for (let k in OrderStateMap) {
        if (OrderStateMap[k] === state && k === 'audit_failure') {
          return true
        }
      }
      return false
    },
    reapply () {
      this.$store.commit(`${namespace}/APPLY_AGAIN`)
    }
  },
  components: {
    FailTitle,
    Timeline,
    TimelineItem,
    XButton
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus">
@import '../../common/stylus/variable.styl'

.fail
  color $color-text-black
  .fail-content
    .vux-timeline 
      margin-top 23px
      padding 0 0 0 25px
      .vux-timeline-item
        position relative
        .vux-timeline-item-color
          width 18px
          height 18px
          left -2px
          top 0
          z-index 1
        .vux-timeline-item-tail
          width 4px
        .vux-timeline-item-content
          // margin-left 15px
          padding-bottom .6rem
          .order-item-index
            position absolute
            left 3px
            top 2px
            font-size 13px
            color $color-text-white
            z-index 10
          .order-item-title
            font-size 16px
            font-weight normal
          .order-item-content
            padding-right 32px
            margin-top 8px
            line-height 1.6em
            font-size 12px
            color $color-text-grey-higher
          .order-item-time
            position absolute
            right 20px
            top 4px
            font-size 11px 
            color $color-text-grey-higher
        &:first-child
          .order-item-title
            color $color-text-yellow
          .order-item-content
            color $color-text-black  
  .fail-footer
    margin-top -16px
    text-align center
    .freeze-cooling
      height 30px
      line-height 30px
      text-align center
      font-size 16px
      color $color-text-grey-higher
    .btn-tip
      // width 250px
      width 80%
      height 40px
      border-radius 20px 20px
      font-size 16px
      color rgb(173, 173, 173)
      border none
      background $color-grey
      &:active
        background $color-grey
</style>