<template>
  <div class="record-lend">
    <scroll class="scroller"
            ref="scroll"
            :data="loans"
            :options="scrollOptions"
            @pulling-down="onPullingDown"
            @pulling-up="onPullingUp">
      <li class="lend-item" v-for="(loan, index) in loans" :key="`loan_${index}`" @click="lookOverDetail($event, loan.loan_id)">
        <p class="count"><span class="label">借款金额：</span><span class="value">{{loan.quota}}元</span></p>
        <p class="days"><span class="label">借款期限：</span><span class="value">{{loan.period}}天</span></p>
        <span class="status" :class="{error: stateFormat(loan.state)}">{{loan.state | stateFormatter}}</span>
        <span class="date">{{loan.state_time | timeFormatter}}</span>
      </li>
    </scroll>
    <div class="no-data-container" v-show="loans.length === 0">
      <div class="img-wrap">
        <img src="../../assets/imgs/img_no_data.png" width="200" height="200">
      </div>
    </div>
    <!-- lend-detail -->
    <transition name="fade">
      <div class="lend-detail-wrap" v-show="loanDetailShow">
        <lend-detail class="page-lend-detail" ref="lendDetail"></lend-detail>
      </div>
    </transition>
  </div>
</template>

<script>
import LendDetail from './LendDetail'
import { Scroll } from 'cube-ui'

import recordStateMap from '@/assets/datas/recordStateMap'
import { createNamespacedHelpers } from 'vuex'
const namespace = 'personalCenter/record'
const { mapGetters, mapActions } = createNamespacedHelpers(namespace)

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
      },
      loanOffset: 0,
      loanLimit: 10
    }
  },
  created () {
    // send request to fetch list data
    this.fetchLoanRecords({offset: this.loanOffset})
  },
  destroyed () {
    this.$store.commit(`${namespace}/CLEAR_LOANS`)
  },
  computed: {
    ...mapGetters([
      'loans', 'loanDetailShow', 'isLoansLoadedFull'
    ])
  },
  watch: {
  },
  methods: {
    ...mapActions([
      'fetchLoanRecords'
    ]),
    onPullingDown () {
      this.$store.commit(`${namespace}/CLEAR_LOANS`)
      this.fetchLoanRecords({ offset: 0 })
    },
    onPullingUp () {
      if (!this.isLoansLoadedFull) {
        this.fetchLoanRecords()
      } else {
        this.$refs.scroll.forceUpdate()
      }
    },
    stateFormat (str) {
      let state = recordStateMap.loan.find(el => {
        return el.name === str
      })
      return state.flag === 0
    },
    lookOverDetail (event, loanId) {
      // avoid to dispatch twice 'click' event in pc web evironment
      if (!event._constructed) return
      this.$refs.lendDetail.fetchLoanRecordById({loan_id: loanId})
    }
  },
  filters: {
    stateFormatter (str) {
      let state = recordStateMap.loan.find(el => {
        return el.name === str
      })
      return state.value
    },
    timeFormatter (timeStamp) {
      // timeStamp from server correct to seconds, need to correct to milliseconds here
      return format(timeStamp * 1000, 'YYYY-MM-DD')
    }
  },
  components: {
    LendDetail,
    Scroll
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus" scoped>
  @import '../../common/stylus/variable.styl'
  @import '../../common/stylus/mixin.styl'

  .record-lend
    position: relative
    .scroller
      height: 551px
      // border-top 1px solid $color-grey-higher1
      .lend-item
        position relative
        width 100%
        height 70px
        padding 10px 15px
        font-size 14px
        color $color-text-black
        border-bottom 1px solid $color-grey-higher1
        .count, .days
          line-height 1.6em
        .status, .date
          position absolute
        .status
          top 10px
          right 15px
          color $color-text-blue
          &.error
            color $color-text-error
        .date
          top 2.5em
          right 15px
          color $color-text-grey-higher
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
    .lend-detail-wrap
      position fixed
      left 0
      top 0
      bottom 0
      width 100%
      background $color-white
      overflow hidden
      z-index: 99
      &.fade-enter-active, &.fade-leave-active
        transition all .6s ease
      &.fade-enter, &.fade-leave-to
        opacity 0
</style>
