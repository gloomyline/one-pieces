<template>
  <div class="lend-detail">
    <l-d-header @go-back="close" not-go-back><span slot="title-text">借款详情</span><span slot="btn-confirm"></span></l-d-header>
    <group class="detail-content" gutter="0">
      <cell class="quota" title="借款金额" :value="`${loan.quota}元`"></cell>
      <cell class="period" title="借款期限" :value="`${loan.period}天`"></cell>
      <cell class="bank" title="到账银行" :value="loan.bank"></cell>
      <cell class="lending-at" title="到账时间" :value="loan.lending_at | timeFormatter"></cell>
      <cell class="refund-plan-at" title="计划还款时间" :value="loan.planned_repayment_at | timeFormatter"></cell>
      <cell class="state" :class="{'error': formatFlag(loan.state)}" title="到账详情"  :value="loan.state | stateFormatter"></cell>
    </group>
  </div>
</template>

<script>
import LDHeader from '@/components/APageHeader/APageHeader'
import { Group, Cell } from 'vux'

import recordStateMap from '@/assets/datas/recordStateMap'

import { createNamespacedHelpers } from 'vuex'
const namespace = 'personalCenter/record'
const { mapGetters, mapActions } = createNamespacedHelpers(namespace)

import { format } from 'date-fns'

export default {
  name: 'lendDetail',
  data () {
    return {}
  },
  mounted () {
  },
  computed: {
    ...mapGetters([
      'loan'
    ])
  },
  methods: {
    ...mapActions([
      'fetchLoanRecordById'
    ]),
    close () {
      this.$store.commit(`${namespace}/HIDE_LOAN_DETAIL`)
    },
    formatFlag (state) {
      // if we have not requested yet, wo would not get any data on loan here
      // so we need to do nothing, just return
      if (!state) return
      let stateMap = recordStateMap.loan
      return (stateMap.find(el => el.name === state).flag) === 0
    }
  },
  filters: {
    timeFormatter (timeStamp) {
      let datePattern = 'YYYY-MM-DD'
      return typeof timeStamp === 'boolean' ? '' : format(timeStamp * 1000, datePattern)
    },
    stateFormatter (str) {
      // if we have not requested yet, wo would not get any data on loan here
      // so we need to do nothing, just return
      if (!str) return
      let stateMap = recordStateMap.loan
      return stateMap.find(el => el.name === str).value
    }
  },
  components: {
    LDHeader,
    Group,
    Cell
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus">
@import '../../common/stylus/variable.styl'

  .lend-detail
    background-color: $color-white
    .detail-content
      .weui-cell
        padding-left 20px
        .vux-cell-bd
          .vux-label
            font-size 15px
            color $color-text-grey-higher
        .weui-cell__ft
          font-size 15px
          color $color-text-black
        &.state
          .weui-cell__ft
            color $color-text-blue
          &.error
            .weui-cell__ft
              color $color-text-error
</style>
