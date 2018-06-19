<template>
  <div class="page-refund-success">
    <r-s-header @go-back="goBack" not-go-back><span slot="title-text">还款成功</span><span slot="btn-confirm"></span></r-s-header>
    <div class="content">
      <div class="result-icon-wrap">
        <span class="icon-success icon"></span>
      </div>
      <p class="result-text">恭喜，还款成功</p>
      <x-button class="btn-tip btn-blue" plain type="primary" action-type="button" @click.native="goBack">我知道了</x-button>
    </div>
  </div>
</template>

<script>
import RSHeader from '@/components/APageHeader/APageHeader'
import { XButton } from 'vux'

export default {
  name: 'pageRefundSuccess',
  data () {
    return {}
  },
  mounted () {
    // this._alertShow('还款成功，3s后自动返回首页', this.goBack)
  },
  computed: {},
  methods: {
    goBack () { // 返回借款首页
      this.$router.push('/')
    }
  },
  components: {
    RSHeader,
    XButton
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus" scoped>
@import '../../../common/stylus/variable.styl'
@import '../../../common/stylus/mixin.styl'

.page-refund-success
  .content
    text-align center
    .result-icon-wrap
      padding-top 82px
      padding-bottom 32px
      .icon-success
        inline-icon(100px, 100px)
        background-image url('./icon_add_success.png')
    .result-text
      font-size 18px
      font-weight 600
      color $color-text-black
    .btn-tip
      margin-top 32px
</style>