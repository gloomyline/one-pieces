<template>
  <div class="page-withdraw">
    <w-header>
      <span slot="title-text">立即提现</span>
      <span slot="btn-confirm"></span>
    </w-header>
    <div class="content">
      <h3 class="title">我的收入</h3>
      <p class="income-count">{{incomeCount | currency}}</p>
      <!-- <p class="link-bankcard">到账银行：{{ bankcard }}</p> -->
      <x-button class="btn-withdraw btn-blue" type="primary" action-type="button" @click.native="withdraw">提现</x-button>
    </div>
  </div>
</template>

<script>
import WHeader from '@/components/APageHeader/APageHeader'
import { XButton } from 'vux'
import Accounting from 'accounting'

export default {
  name: 'pageWithdraw',
  data () {
    return {
      incomeCount: 0,
      bankcard: ''
    }
  },
  mounted () {},
  computed: {},
  methods: {
    withdraw () {
      // send request to server: withdraw
    }
  },
  filters: {
    currency (val) {
      return Accounting.formatMoney(val, '¥ ', 2, ',')
    }
  },
  components: {
    WHeader,
    XButton
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus" scoped>
@import '../../../../../common/stylus/variable.styl'

.page-withdraw
  .content
    text-align center
    .title
      margin 21px 0 22px
      line-height 2em
      font-size 15px
      font-weight 400
      letter-spacing 2px
      color $color-text-black
    .income-count
      margin-bottom 42px
      font-size 36px
      color $color-text-yellow
    .link-bankcard
      margin-bottom 25px
      font-size 14px
      color $color-text-black
</style>