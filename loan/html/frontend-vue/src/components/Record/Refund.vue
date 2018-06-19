<template>
  <div class="record-refund">
    <scroll class="scroller"
            ref="scroll"
            :data="repayments"
            :options="scrollOptions"
            @pulling-down="onPullingDown"
            @pulling-up="onPullingUp">
      <li class="refund-item" v-for="(repayment, index) in repayments">
        <p class="count"><span class="label">还款金额：</span><span class="value">{{repayment.quota}}元</span></p>
        <span class="status">{{repayment.state | stateFormatter}}</span>
        <span class="date">{{repayment.state_time | timeFormatter}}</span>
      </li>
    </scroll>
    <div class="no-data-container" v-show="repayments.length === 0">
      <div class="img-wrap">
        <img src="../../assets/imgs/img_no_data.png" width="200" height="200">
      </div>
    </div>
  </div>
</template>

<script>
import { Scroll } from 'cube-ui'

import recordStateMap from '@/assets/datas/recordStateMap'

import {createNamespacedHelpers} from 'vuex'
const namespace = 'personalCenter/record'
const { mapGetters, mapActions } = createNamespacedHelpers(namespace)

import { format } from 'date-fns'

export default {
  name: 'recordRefund',
  data () {
    return {
      repaymentOffset: 0,
      repaymentLimit: 10,
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
            more: '加载跟多',
            noMore: '没有更多数据了'
          }
        }
      }
    }
  },
  created () {
    this.fetchRepaymentRecords({offset: this.repaymentOffset})
  },
  destroyed () {
    this.$store.commit(`${namespace}/CLEAR_REPAYMENTS`)
  },
  computed: {
    ...mapGetters([
      'repayments', 'isLoadedRepaymentsFull'
    ])
  },
  methods: {
    ...mapActions([
      'fetchRepaymentRecords'
    ]),
    onPullingDown () {
      this.$store.commit(`${namespace}/CLEAR_REPAYMENTS`)
      this.fetchRepaymentRecords({ offset: 0 })
    },
    onPullingUp () {
      if (!this.isLoadedRepaymentsFull) {
        this.fetchRepaymentRecords()
      } else {
        this.$refs.scroll.forceUpdate()
      }
    },
    flagFormat (state) {
      return recordStateMap.repayment.find(el => el.name === state).flag
    }
  },
  filters: {
    stateFormatter (state) {
      return recordStateMap.repayment.find(el => el.name === state).value
    },
    timeFormatter (timeStamp) {
      return format(timeStamp * 1000, 'YYYY-MM-DD')
    }
  },
  components: {
    Scroll
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus" scoped>
@import '../../common/stylus/variable.styl'
@import '../../common/stylus/mixin.styl'

.record-refund
  position: relative
  .scroller
    height: 551px
      // border-top 1px solid $color-grey-higher1
    .refund-item
      position relative
      height 70px
      padding 10px 15px
      font-size 14px
      color $color-text-black
      border-bottom 1px solid $color-grey-higher1
      .count
        line-height 50px
        font-size 15px
      .status, .date
        position absolute
        right 15px
      .status
        top 15px
        color $color-text-blue
      .date
        top 45px
        color $color-text-grey
  .no-data-container
    absolute: left top
    bottom: 0
    width: 100%
    display: flex
    justify-content: center
    align-items: center
    .img-wrap
      width 200px
      height 200px
</style>
