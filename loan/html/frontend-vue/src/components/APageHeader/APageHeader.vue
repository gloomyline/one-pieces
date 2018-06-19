<template>
  <div class="a-page-header">
    <div class="header-wrap">
      <div class="title-wrap"><slot name="title-text">这里是标题</slot></div>
      <div class="btn-return" @click="goBack"><i class="icon-return"></i></div>
      <div class="btn-confirm" @click="confirm"><slot name="btn-confirm">这里是确定按钮内容</slot></div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'app',
  data () {
    return {}
  },
  mounted () {},
  props: {
    notGoBack: {
      type: Boolean,
      default: false
    }
  },
  computed: {},
  methods: {
    goBack (event) {
      if (!this.notGoBack) {
        this.$router.go(-1)
      }
      this.$emit('go-back', event)
    },
    confirm (event) {
      this.$emit('confirm', event)
    }
  },
  components: {}
}
</script>

<style lang="stylus" ref="stylesheet/stylus" scoped>
@import '../../common/stylus/mixin.styl'
@import '../../common/stylus/variable.styl'

.a-page-header
  position relative
  width 100%
  height 55px
  color $color-text-white
  background $color-blue
  .title-wrap
    height 100%
    line-height 55px
    text-align center
    font-size 20px
    letter-spacing 2px
  .btn-return
    position absolute
    left 14px
    top 13px
    padding 4px
    .icon-return
      font-size 20px
  .btn-confirm
    position absolute
    right 14px
    top 13px
    padding 4px
    font-size 15px
</style>