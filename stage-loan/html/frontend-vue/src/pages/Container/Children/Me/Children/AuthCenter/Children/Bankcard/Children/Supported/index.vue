<!--
@Date:   2018-01-02T14:30:29+08:00
@Last modified time: 2018-01-02T15:30:09+08:00
-->
<template>
  <div class="page-supported-bankcard">
    <s-b-header not-go-back @go-back="close">
      <span slot="title-text">银行卡支持列表</span>
      <span slot="btn-confirm"></span>
    </s-b-header>
    <div class="supported-list">
      <h2 class="title">支持的储蓄卡</h2>
      <p class="content">{{ supportedList }}</p>
    </div>
  </div>
</template>

<script>
import SBHeader from '@/components/APageHeader/APageHeader'

import SupportedBankcardData from '@/assets/datas/supportedBankcard'

export default {
  name: 'pageSupportedBankcard',
  data () {
    return {
      supporteds: SupportedBankcardData
    }
  },
  mounted () {},
  computed: {
    supportedList () {
      return this.supporteds.join('、')
    }
  },
  methods: {
    close (event) {
      this.$emit('close-page', event)
    }
  },
  components: {
    SBHeader
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus" scoped>
@import '../../../../../../../../../../common/stylus/variable.styl'

.page-supported-bankcard
  .supported-list
    .title
      height 40px
      line-height 40px
      text-align center
      font-size 15px
      color $color-text-blue
      border-bottom 1px solid $color-grey-higher1
    .content
      padding 25px 20px 20px
      line-height 1.6em
      font-size 14px
      color $color-text-black
</style>
