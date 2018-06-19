<template>
  <div class="refund-success">
    <refund-success-title>您已还款成功，可以继续借款啦</refund-success-title>
    <div class="refund-success-content">
      <timeline class="order-item-list" color="#40d3fe" :isShowIcon="false">
        <timeline-item ref="timelineItem" class="order-item" v-for="(item, index) in orderData" :key="index">
          <div class="order-item-index">{{orderData.length - index}}</div>
          <h4 class="order-item-title">{{item.title}}</h4>
          <p class="order-item-content">{{item.content}}</p>
          <p class="order-item-time">{{item.time}}</p>
        </timeline-item>
      </timeline>
    </div>
    <div class="refund-success-footer">
      <x-button class="btn-tip" plain type="primary" action-type="button" @click.native="reApplay">我知道了</x-button>
    </div>
  </div>
</template>

<script>
import RefundSuccessTitle from '@/components/HomeContentTitle/HomeContentTitle'
import { Timeline, TimelineItem, XButton } from 'vux'

const namespace = 'home'

const mockData = [
  {
    title: '还款成功',
    content: '您已还款成功，可以继续借款啦！',
    time: '2017-04-20 07:40'
  },
  {
    title: '还款申请提交成功',
    content: '还款金额502.5元，本金500元，违约罚金2.50元',
    time: '2017-04-20 07:30'
  }
]

export default {
  name: 'refundSuccess',
  data () {
    return {
      orderData: mockData
    }
  },
  mounted () {
    // 重写最后一个 timeline vm 的 display property
    let lastTimelineVm = this.$refs.timelineItem[this.$refs.timelineItem.length - 1]
    lastTimelineVm.$el.querySelector('.vux-timeline-item-tail').style.display = 'block'
  },
  computed: {},
  methods: {
    reApplay () {
      this.$store.commit(`${namespace}/APPLY_AGAIN`)
    }
  },
  components: {
    RefundSuccessTitle,
    Timeline,
    TimelineItem,
    XButton
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus">
@import '../../common/stylus/variable.styl'

.refund-success
  color $color-text-black
  .refund-success-content
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
            margin-top 8px
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
  .refund-success-footer
    margin-top 50px
    text-align center
    .btn-tip
      // width 250px
      width 80%
      height 40px
      border-radius 20px 20px
      font-size 16px
      color $color-text-blue
      border-color $color-blue
      &:active
        color $color-text-blue
        border-color $color-blue
</style>