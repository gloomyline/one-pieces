<template>
  <div class="home-content-title">
    <!-- 标题 -->
    <div class="title-wrap">
      <i class="icon-notification"></i>
      <span class="borrow-message"><slot></slot></span>
    </div>
  </div>
</template>

<script>
export default {
  name: 'homeContentTitle',
  data () {
    return {}
  },
  mounted () {},
  computed: {},
  methods: {},
  components: {}
}
</script>

<style lang="stylus" ref="stylesheet/stylus" scoped>
@import '../../common/stylus/mixin.styl'
@import '../../common/stylus/variable.styl'

.home-content-title
  width 100%
  margin-top -30px
  .title-wrap
    // width 335px
    width 90%
    height 25px
    margin 0 auto
    line-height 25px
    text-align center
    font-size 0
    color $color-text-white
    border-radius 13px 13px
    background $color-blue-lightter
    .icon-notification
      line-height 28px
      font-size 20px
    .borrow-message
      display inline-block
      vertical-align top
      margin-left 8px
      font-size 13px
</style>