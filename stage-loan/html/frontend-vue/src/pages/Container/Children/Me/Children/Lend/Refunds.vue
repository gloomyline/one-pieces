<!--
@Date:   2018-01-09T15:31:01+08:00
@Last modified time: 2018-01-19T13:56:03+08:00
-->
<template lang="html">
  <div class="page-refunds">
    <scroll class="scroller"
            ref="scroll"
            :data="all"
            :options="scrollOptions"
            @pulling-down="onPullingDown"
            @pulling-up="onPullingUp">
      <li class="refunds-item" v-for="(refund, index) in all">
        <div class="img-wrap">
          <i class="icon" :class="{'icon-cash-logo': refund.type === 1, 'icon-consume-logo': refund.type === 2}"></i>
        </div>
        <div class="desc">
          <p class="money-order">￥{{ refund.money_order }}</p>
          <p class="term-and-use">{{ refund.current_term | termFormater(refund.period) }}期 {{ refund.use }}</p>
        </div>
        <div class="time">{{ refund.created_at | timeFormater }}</div>
        <div class="state" :class="{
          'text-green': refund.state === 'SUCCESS',
          'text-yellow': refund.state !== 'SUCCESS' }">{{ refund.state | stateFormater }}</div>
      </li>
    </scroll>
  </div>
</template>

<script>
import { createNamespacedHelpers } from 'vuex'
const namespace = 'container/me/lend/refunds'
const { mapGetters, mapActions } = createNamespacedHelpers(namespace)

import { Scroll } from 'cube-ui'

import { format } from 'date-fns'

export default {
  data () {
    return {
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
            more: '加载更多',
            noMore: '没有更多数据了'
          }
        }
      }
    }
  },
  activated () {
    this.$store.commit(`${namespace}/INIT_DATA_FOR_REFUNDS`)
    this.fetchRefunds()
  },
  computed: {
    ...mapGetters([
      'all', 'hasMore'
    ])
  },
  methods: {
    ...mapActions([
      'fetchRefunds'
    ]),
    onPullingDown () {
      this.$store.commit(`${namespace}/INIT_DATA_FOR_REFUNDS`)
      this.fetchRefunds({ offset: 0 })
    },
    onPullingUp () {
      if (this.hasMore) {
        this.fetchRefunds()
      } else {
        this.$refs.scroll.forceUpdate()
      }
    }
  },
  filters: {
    termFormater (term, period) {
      // term = term || 1
      return `0${term}`.substr(-2) + '/' + `0${period}`.substr(-2)
    },
    timeFormater (timestamp) {
      return format(timestamp * 1000, 'YYYY-MM-DD HH:mm:ss')
    },
    stateFormater (state) {
      return state === 'success' ? '还款成功' : '还款失败'
    }
  },
  components: {
    Scroll
  }
}
</script>

<style lang="stylus" scoped>
@import '../../../../../../common/stylus/mixin.styl'
@import '../../../../../../common/stylus/variable.styl'

.page-refunds
  .scroller
    height: 550px
    .refunds-item
      position: relative
      width: 100%
      height: 80px
      font-size: 0
      border-1px($color-border-grey-light, 'bottom')
      .img-wrap
        display: inline-block
        vertical-align: top
        padding: 15px 16px 15px 15px
        .icon
          font-size: 50px
          color: $color-text-yellow
      .desc
        display: inline-block
        vertical-align: top
        padding-top: 20px
        font-size: 16px
        .money-order
          color: $color-text-black
        .term-and-use
          margin-top: 12px
          color: $color-text-black
      .time
        absolute: top 24px right 15px
        font-size: 14px
        color: $color-text-grey
      .state
        absolute: top 48px right 15px
        font-size: 16px
        &.text-green
          color: $color-text-green
        &.text-yellow
          color: $color-text-yellow
</style>
