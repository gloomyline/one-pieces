<!--
@Date:   2018-01-09T15:30:52+08:00
@Last modified time: 2018-01-31T10:58:16+08:00
-->
<template lang="html">
  <div class="page-lends">
    <scroll class="scroller"
            ref="scroll"
            :data="all"
            :options="scrollOptions"
            @pulling-down="onPullingDown"
            @pulling-up="onPullingUp">
      <li class="lends-item" v-for="(lend, index) in all">
        <router-link :to="`/me/lend/lends/detail/${ lend.loan_id }`">
          <div class="img-wrap">
            <i class="icon" :class="{ 'icon-cash-logo': lend.type === 1,
              'icon-consume-logo': lend.type === 2 }"></i>
          </div>
          <div class="desc">
            <p class="total-amount">￥{{ lend.quota }}</p>
            <p class="date-and-use">
              <span class="date">{{ lend.created_at | dateFormater }}</span>
              <span class="use">{{ lend.use }}</span>
            </p>
            <p class="left-principal">剩余未还本金￥{{ lend.surplus_principal }}</p>
          </div>
          <div class="state">
            <div class="left">
              <p class="text" :class="stateClassMap(lend.state)">{{ lend.state | stateFormater }}<br/><span class="period" v-if="lend.current_period">{{ lend.current_period | termFormater(lend.period) }}期</span></p>
            </div>
            <div class="right">
              <i class="icon-arrow-right"></i>
            </div>
          </div>
        </router-link>
      </li>
    </scroll>
    <transition name="lends-fade">
      <router-view class="lends-children-container"></router-view>
    </transition>
  </div>
</template>

<script>
import { createNamespacedHelpers } from 'vuex'
const namespace = 'container/me/lend/lends'
const { mapGetters, mapActions } = createNamespacedHelpers(namespace)

import { Scroll } from 'cube-ui'
import { format } from 'date-fns'

const stateMap = {
  auditing: '审核中',
  audit_failure: '审核失败',
  audit_success: '审核成功',
  granting: '放款中',
  confirming: '商户确认中',
  confirm_success: '商户确认通过',
  confirm_failure: '商户确认未通过',
  repaying: '还款中',
  finished: '已还完',
  overdue: '逾期中'
}

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
    this.$store.commit(`${namespace}/INIT_DATA_FOR_LENDS`)
    this.fetchLends()
    // this.$nextTick(() => {
    //   this.scroll.scrollTo(0, 0, 600)
    // })
  },
  computed: {
    ...mapGetters([
      'all', 'hasMore'
    ])
  },
  methods: {
    ...mapActions([
      'fetchLends'
    ]),
    onPullingDown () {
      this.$store.commit(`${namespace}/INIT_DATA_FOR_LENDS`)
      this.fetchLends({ offset: 0 })
    },
    onPullingUp () {
      if (this.hasMore) {
        this.fetchLends()
      } else {
        this.$refs.scroll.forceUpdate()
      }
    },
    stateClassMap (state) {
      return state === 'finished'
        ? 'text-green' : state === 'overdue'
        ? 'text-yellow' : ''
    }
  },
  filters: {
    dateFormater (timestamp) {
      return format(timestamp * 1000, 'YYYY-MM-DD')
    },
    stateFormater (state) {
      return stateMap[state]
    },
    termFormater (term, period) {
      return (`0${term}`).substr(-2) + '/' + (`0${period}`).substr(-2)
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

.page-lends
  .scroller
    height: 550px
    .lends-item
      position: relative
      height: 100px
      font-size: 0
      border-1px($color-border-grey-light, 'bottom')
      a
        display: block
        width: 100%
        height: 100%
        text-decoration: none
        .img-wrap
          display: inline-block
          vertical-align: top
          padding: 25px 16px 25px 16px
          .icon
            font-size: 50px
            color: $color-text-yellow
        .desc
          display: inline-block
          vertical-align: top
          .total-amount
            margin-top: 20px
            font-size: 18px
            color: $color-text-black
          .date-and-use
            margin-top: 4px
            color: $color-text-grey
            .date, .use
              font-size: 14px
          .left-principal
            margin-top: 8px
            font-size: 14px
            color: $color-text-black
        .state
          absolute: top 50% right 10px
          height: 40px
          margin-top: -20px
          font-size: 16px
          color: $color-text-black
          .left
            display: inline-block
            vertical-align: middle
            text-align: right
            .text
              &.text-green
                color: $color-text-green
              &.text-yellow
                color: $color-text-yellow
              .period
                color: $color-text-grey
          .right
            display: inline-block
            vertical-align: middle
            line-height: 40px
            font-size: 16px
  .lends-children-container
    position: fixed
    left: 0
    top: 0
    bottom: 0
    width: 100%
    background-color: $color-white-high
</style>
