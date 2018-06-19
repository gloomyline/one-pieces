<template>
  <div class="page-msg-center">
    <m-c-header><span slot="title-text">消息中心</span><span slot="btn-confirm"></span></m-c-header>
    <div class="not-have-msgs">
      <div class="img-wrap">
        <img src="../../../assets/imgs/img_no_data.png" width="200" height="200" alt="">
      </div>
    <p class="no-msg-tip">暂无消息</p>
    </div>
  </div>
</template>

<script>
import MCHeader from '@/components/APageHeader/APageHeader'

export default {
  name: 'pageMsgCenter',
  data () {
    return {}
  },
  mounted () {},
  computed: {},
  methods: {},
  components: {
    MCHeader
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus" scoped>
@import '../../../common/stylus/mixin.styl'
@import '../../../common/stylus/variable.styl'

.page-msg-center
  .not-have-msgs
    .img-wrap
      width 200px
      height 200px
      margin 80px auto 0
    .no-msg-tip
      text-align center
      font-size 15px
      letter-spacing 1px
      color $color-text-grey-higher
</style>