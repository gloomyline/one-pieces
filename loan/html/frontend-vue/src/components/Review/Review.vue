<template>
  <div class="review">
    <review-title>您的借款申请已提交，系统审核中</review-title>
    <div class="review-content">
      <timeline class="order-item-list" color="#40d3fe" :isShowIcon="false">
        <timeline-item ref="timelineItem" class="order-item" v-for="(item, index) in orderDetail" :key="index">
          <div class="order-item-index">{{orderDetail.length - index}}</div>
          <h4 class="order-item-title">{{item.title}}</h4>
          <p class="order-item-content">申请借款金额{{ item.content.quota }}元，期限{{  item.content.period }}天，手续费用{{ item.content.poundage }}元</p>
          <p class="order-item-time">{{ item.time }}</p>
        </timeline-item>
      </timeline>
    </div>
    <div class="review-footer">
      <x-button class="btn-tip" plain type="primary" action-type="button">借款审核中，请您耐心等待</x-button>
    </div>
  </div>
</template>

<script>
import ReviewTitle from '@/components/HomeContentTitle/HomeContentTitle'
import { Timeline, TimelineItem, XButton } from 'vux'

const mockData = [
  {
    title: '审核中',
    content: '',
    time: '2017-04-20 07:40'
  },
  {
    title: '申请提交成功',
    content: '申请借款金额500元，期限7天，手续费用8元',
    time: '2017-04-20 07:30'
  }
]

import { createNamespacedHelpers } from 'vuex'
const namespace = 'home'
const { mapGetters } = createNamespacedHelpers(namespace)

export default {
  name: 'review',
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
  computed: {
    ...mapGetters([
      'orderDetail'
    ])
  },
  methods: {},
  components: {
    ReviewTitle,
    Timeline,
    TimelineItem,
    XButton
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus">
@import '../../common/stylus/variable.styl'

.review
  color $color-text-black
  .review-content
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
  .review-footer
    margin-top 50px
    text-align center
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