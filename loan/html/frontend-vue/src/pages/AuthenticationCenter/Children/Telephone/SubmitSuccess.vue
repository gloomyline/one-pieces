<template>
  <div class="page-submit-success">
    <div class="icon-wrap">
      <span class="icon"></span>
    </div>
    <div class="infos">
      <p class="tip">恭喜您，已完成手机认证</p>
      <!-- <p class="phone-number">手机号码：{{phoneNumber}}</p> -->
    </div>
    <x-button class="btn-resubmit btn-blue" type="primary" action-type="button" @click.native="resubmit">重新提交</x-button>
  </div>
</template>

<script>
import RHeader from '@/components/APageHeader/APageHeader'
import { XButton } from 'vux'

export default {
  name: 'pageReset',
  data () {
    return {
      phoneNumber: '138********465'
    }
  },
  mounted () {},
  computed: {},
  methods: {
    resubmit () {
      this.$parent.pageSubmitSuccessShow = false
    }
  },
  components: {
    RHeader,
    XButton
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus">
@import '../../../../common/stylus/mixin.styl'
@import '../../../../common/stylus/variable.styl'

.page-submit-success
  position fixed
  top 55px
  left 0
  bottom 0
  width 100%
  text-align center
  background-color $color-white
  hidden overflow
  .icon-wrap
    padding 81px 0 38px 0
    .icon
      inline-icon(80px, 80px)
      bg-img('./icon_submit_success')
  .infos
    margin-bottom 30px
    line-height 1.6em
    .tip
      font-size 14px
      color $color-text-black
    .phone-number
      font-size 13px
      color $color-text-grey-higher
</style>