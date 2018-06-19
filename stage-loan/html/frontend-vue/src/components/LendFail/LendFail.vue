<template>
  <div class="lend-fail">
    <lend-fail-title>很抱歉，您的本次借款申请放款失败</lend-fail-title>
    <div class="lend-fail-content">
      <timeline class="order-item-list" color="#40d3fe" :isShowIcon="false">
        <timeline-item ref="timelineItem" class="order-item" v-for="(item, index) in orderData" :key="index">
          <div class="order-item-index">{{orderData.length - index}}</div>
          <h4 class="order-item-title">{{item.title}}</h4>
          <p class="order-item-content">{{item.content}}</p>
          <p class="order-item-time">{{item.time}}</p>
        </timeline-item>
      </timeline>
    </div>
    <div class="lend-fail-footer">
      <x-button class="btn-tip" plain type="primary" :disabled="true">我知道了</x-button>
    </div>
  </div>
</template>

<script>
import LendFailTitle from '@/components/HomeContentTitle/HomeContentTitle'
import { Timeline, TimelineItem, XButton } from 'vux'

const mockData = [
  {
    title: '放款失败',
    content: '放款失败原因：xxx，请您重试申请或咨询客服400-123-123',
    time: '2017-04-20 07:50'
  },
  {
    title: '审核通过',
    content: '申请借款金额500元，期限7天，手续费用8元',
    time: '2017-04-20 07:40'
  },
  {
    title: '申请提交成功',
    content: '申请借款金额500元，期限7天，手续费用8元',
    time: '2017-04-20 07:30'
  }
]

export default {
  name: 'lendFail',
  data () {
    return {
      orderData: mockData
    }
  },
  mounted () {},
  computed: {},
  methods: {},
  components: {
    LendFailTitle,
    Timeline,
    TimelineItem,
    XButton
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus">
@import '../../common/stylus/variable.styl'

.lend-fail
  color $color-text-black
  .lend-fail-content
    .vux-timeline 
      margin-top 12px
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
  .lend-fail-footer
    margin-top -16px
    text-align center
    .btn-tip
      // width 250px
      width 80%
      height 40px
      border-radius 20px 20px
      font-size 16px
      color $color-text-blue
      border-color $color-blue
      background-color transparent
      &:active
        border-color $color-blue
        color $color-text-blue
</style>