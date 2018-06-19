<!--
@Date:   2018-01-23T15:54:19+08:00
@Last modified time: 2018-01-23T16:14:29+08:00
-->
<template lang="html">
  <div class="page-consume-submit-result">
    <c-s-header class="header" not-has-confirm>
      <span class="title" slot="title-text">消费分期</span>
    </c-s-header>
    <div class="content">
      <div class="img-wrap">
        <img :src="icon" width="64" height="64">
      </div>
      <p class="submit-success">您的分期申请提交成功！</p>
    </div>
    <footer class="footer">
      <x-button class="btn-continue-shopping" type="primary" action-type="button" @click.native="continueShopping">继续购物</x-button>
      <x-button class="btn-preview-lending" type="primary" action-type="button" @click.native="previewLending">查看借款</x-button>
    </footer>
  </div>
</template>

<script>
import CSHeader from '@/components/APageHeader/APageHeader'
import { XButton } from 'vux'

export default {
  data () {
    return {
      icon: require('@/assets/imgs/icon_add_success.png')
    }
  },
  methods: {
    continueShopping () {
      this.$router.go(-1)
    },
    previewLending () {
      this.$router.push('/me/refund')
    }
  },
  components: {
    CSHeader,
    XButton
  }
}
</script>

<style lang="stylus">
@import '../../../../../../common/stylus/mixin.styl'
@import '../../../../../../common/stylus/variable.styl'

.page-consume-submit-result
  background-color: $color-white-high
  .header
    border-1px($color-border-grey-light, 'bottom')
  .content
    text-align: center
    .img-wrap
      margin-top: 36px
    .submit-success
      margin-top: 36px
      font-size: 18px
      color: $color-text-black
  .footer
    margin-top: 36px
    .btn-continue-shopping
      width: 340px
      font-size: 18px
      color: $color-white
    .btn-preview-lending
      width: 340px
      margin-top: 16px
      font-size: 18px
      color: $color-text-black
      background-color: $color-grey
</style>
