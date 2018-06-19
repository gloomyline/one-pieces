<template>
  <div class="success">
   <success-title>审核通过！等待系统放款</success-title>
    <div class="success-content">
      <timeline class="order-item-list" color="#40d3fe" :isShowIcon="false">
        <timeline-item ref="timelineItem" class="order-item" v-for="(item, index) in orderDetail" :key="index">
          <div class="order-item-index">{{orderDetail.length - index}}</div>
          <h4 class="order-item-title">{{item.title}}</h4>
          <p class="order-item-content" v-if="isReviewSucceed(item.title)">借款将打款至您的{{ item.content.bank_name }}（{{ item.content.end_bank_no }}）的账户中</p>
          <p class="order-item-content" v-else>申请借款金额{{ item.content.quota }}元，期限{{ item.content.period }}天，手续费用{{ item.content.poundage }}元</p>
          <p class="order-item-time">{{item.time}}</p>
        </timeline-item>
      </timeline>
    </div>
  </div>
</template>

<script>
import SuccessTitle from '@/components/HomeContentTitle/HomeContentTitle'
import { Timeline, TimelineItem, XButton } from 'vux'

import { createNamespacedHelpers } from 'vuex'
const namespace = 'home'
const { mapGetters } = createNamespacedHelpers(namespace)
import { OrderStateMap } from '@/store/modules/Home'

let mockData = [
  {
    title: '审核通过',
    content: '',
    time: '2017-04-20 07:50'
  },
  {
    title: '申请中',
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
  name: 'success',
  data () {
    return {
      orderData: mockData
    }
  },
  mounted () {},
  computed: {
    ...mapGetters([
      'orderDetail'
    ])
  },
  methods: {
    isReviewSucceed (state) {
      for (let k in OrderStateMap) {
        if (OrderStateMap[k] === state && k === 'audit_success') {
          return true
        }
      }
      return false
    }
  },
  filters: {
  },
  components: {
    SuccessTitle,
    Timeline,
    TimelineItem,
    XButton
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus">
@import '../../common/stylus/variable.styl'

.success
  color $color-text-black
  .success-content
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
</style>