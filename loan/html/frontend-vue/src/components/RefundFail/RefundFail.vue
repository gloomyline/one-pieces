<template>
  <div class="refund-fail">
    <refund-fail-title>很抱歉，您的还款操作失败</refund-fail-title>
    <div class="refund-fail-content">
      <timeline class="order-item-list" color="#40d3fe" :isShowIcon="false">
        <timeline-item ref="timelineItem" class="order-item" v-for="(item, index) in orderData" :key="index">
          <div class="order-item-index">{{orderData.length - index}}</div>
          <h4 class="order-item-title">{{item.title}}</h4>
          <p class="order-item-content">{{item.content}}</p>
          <p class="order-item-time">{{item.time}}</p>
        </timeline-item>
      </timeline>
    </div>
    <div class="refund-fail-footer">
      <x-button class="btn-tip" plain type="primary" action-type="button">我知道了</x-button>
    </div>
  </div>
</template>

<script>
import RefundFailTitle from '@/components/HomeContentTitle/HomeContentTitle'
import { Timeline, TimelineItem, XButton } from 'vux'

const mockData = [
  {
    title: '还款失败',
    content: '还款失败，还款失败原因【id】，请您重新操作',
    time: '2017-04-20 07:40'
  },
  {
    title: '还款申请提交成功',
    content: '还款金额502.5元，本金500元，违约罚金2.50元',
    time: '2017-04-20 07:30'
  }
]

export default {
  name: 'app',
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
  methods: {},
  components: {
    RefundFailTitle,
    Timeline,
    TimelineItem,
    XButton
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus" >
@import '../../common/stylus/variable.styl'

.refund-fail
  color $color-text-black
  .refund-fail-content
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
          margin-left 15px
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
  .refund-fail-footer
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