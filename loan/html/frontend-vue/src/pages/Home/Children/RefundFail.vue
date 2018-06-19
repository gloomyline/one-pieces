<template>
  <div class="page-refund-fail">
    <r-f-header @go-back="goBack" not-go-back><span slot="title-text">还款失败</span><span slot="btn-confirm"></span></r-f-header>
    <div class="content">
      <div class="result-icon-wrap">
        <span class="icon-failed icon"></span>
      </div>
      <p class="result-text">很遗憾，还款失败！</p>
      <x-button class="btn-fail-detail btn-blue">查看原因</x-button>
    </div>
  </div>
</template>

<script>
import RFHeader from '@/components/APageHeader/APageHeader'
import { XButton } from 'vux'

export default {
  name: 'pageRefundFail',
  data () {
    return {}
  },
  mounted () {},
  computed: {},
  methods: {
    goBack () {
      this.$router.push('/')
    }
  },
  components: {
    RFHeader,
    XButton
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus">
@import '../../../common/stylus/variable.styl'
@import '../../../common/stylus/mixin.styl'

.page-refund-fail
  .content
    text-align center
    .result-icon-wrap
      padding-top 82px
      padding-bottom 32px
      .icon-failed
        inline-icon(100px, 100px)
        background-image url('./icon_add_failed.png')
    .result-text
      font-size 18px
      font-weight 600
      color $color-text-black
    .btn-fail-detail
      margin-top 30px
</style>