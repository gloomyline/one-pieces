<template>
  <div class="voucher" :class="{'out-validity': !isInValidity, 'is-used': isUsed}">
    <div class="name">
      <p class="name-text">{{name}}</p>
    </div>
    <div class="infos">
      <div class="info-wrap">
        <p class="title">{{title}}</p>
        <p class="validity">有效期至：{{voucherInfo.end_at | dateFormatter}}</p>
        <p class="condition">使用条件：{{condition}}</p>
      </div>
    </div>
    <div class="img-wrap">
      <img v-if="isUsed" src="./icon_used.png" width="80" height="80">
      <img v-if="!isInValidity" src="./icon_out_validity.png" width="80" height="80">
    </div>
  </div>
</template>

<script>
import { format } from 'date-fns'

const nameArray = ['还款抵扣券', '现金券']

export default {
  name: 'voucher',
  data () {
    return {}
  },
  mounted () {},
  props: {
    voucherInfo: {
      type: Object,
      default () {
        return {
          'coupon_id': 1, // 用户代金券ID
          'coupon_name': 2, // 代金券名称：1-还款抵扣券 2-现金券
          'coupon_amount': 1020, // 代金券金额
          'validity_period': 5, // 有效期
          'min_repayment': 500, // 还款金额下限
          'min_withdrawal': 8100, // 提现金额下限
          'end_at': 1508232701, // 截止时间
          'state': 0 // 代金券使用状态：0-未使用/未提现 1-已使用/已提现 2-已过期
        }
      }
    }
  },
  computed: {
    name () {
      let nameType = this.voucherInfo.coupon_name - 1
      switch (nameType) {
        case 0:
          return nameArray[nameType]
        case 1:
          return `${this.voucherInfo.coupon_amount}元${nameArray[nameType]}`
        default:
          break
      }
    },
    title () {
      return `${this.name}一张`
    },
    condition () {
      return `金额不低于${this.voucherInfo.coupon_name === 1 ? this.voucherInfo.min_repayment : this.voucherInfo.min_withdrawal}元`
    },
    isInValidity () {
      return this.voucherInfo.state !== 2
    },
    isUsed () {
      return this.voucherInfo.state === 1
    }
  },
  methods: {},
  filters: {
    dateFormatter (timestamp) {
      return format(timestamp * 1000, 'YYYY-MM-DD')
    }
  },
  components: {}
}
</script>

<style lang="stylus" ref="stylesheet/stylus" scoped>
@import '../../common/stylus/variable.styl'

.voucher
  display flex
  position relative
  width 325px
  height 110px
  margin 0 auto
  padding 22px 0
  color $color-text-black
  background url('./box_voucher_in.png')
  background-size 100% 100%
  background-repeat no-repeat
  .name
    flex 0 0 100px
    width 100px
    border-right 1px solid $color-grey-higher1
    .name-text
      width 70%
      margin 10px auto 0
      line-height 1.2em
      text-align center
      font-size 18px
  .infos
    flex 1
    font-size 14px
    .info-wrap
      width 80%
      margin 0 auto
      .title
        line-height 1.6em
      .validity
        line-height 2em
        font-size 12px
        color $color-text-yellow
      .condition
        font-size 12px
        color $color-text-grey-higher
  .img-wrap
    position absolute
    right 15px
    bottom 12px
  &.out-validity, &.is-used
    color $color-text-grey-higher
    background url('./box_voucher_out.png')
    background-size 100% 100%
    background-repeat no-repeat
    .infos
      .info-wrap
        .validity
          color $color-text-grey-higher
</style>