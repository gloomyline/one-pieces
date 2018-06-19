<!--
@Date:   2018-01-23T15:21:23+08:00
@Last modified time: 2018-01-31T16:29:05+08:00
-->
<template>
  <div class="page-refund-fail">
    <r-f-header @go-back="goBack" not-go-back><span slot="title-text">还款失败</span><span slot="btn-confirm"></span></r-f-header>
    <div class="content">
      <div class="result-icon-wrap">
        <span class="icon-failure"></span>
      </div>
      <p class="result-text">{{ failureReason }}</br>请您重试还款操作。</p>
      <x-button class="btn-tip btn-yellow" plain type="primary" action-type="button" @click.native="goBack">完成</x-button>
    </div>
  </div>
</template>

<script>
import RFHeader from '@/components/APageHeader/APageHeader'
import { XButton } from 'vux'

export default {
  name: 'pageRefundFail',
  data () {
    return {
      failureReason: ''
    }
  },
  created () {
    this.failureReason = this.$route.query.reason
  },
  mounted () {},
  computed: {},
  methods: {
    goBack () {
      this.$router.push('/me/refund')
    }
  },
  components: {
    RFHeader,
    XButton
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus">
@import '../../../../../../common/stylus/variable.styl'
@import '../../../../../../common/stylus/mixin.styl'

.page-refund-fail
  .content
    text-align center
    .result-icon-wrap
      padding-top 70px
      padding-bottom 36px
      .icon-failure
        inline-icon(64px, 64px)
        bg-img('../../../../../../assets/imgs/icon-failure')
    .result-text
      font-size 18px
      font-weight 400
      color $color-text-yellow
    .btn-tip
      width: 340px
      margin-top 42px
</style>
