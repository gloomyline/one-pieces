<!--
@Date:   2017-12-14T17:05:37+08:00
@Last modified time: 2018-01-30T17:52:59+08:00
-->
<template>
  <div class="a-page-header">
    <div class="header-wrap">
      <div class="title-wrap"><slot name="title-text">这里是标题</slot></div>
      <div class="btn-return" v-if="!notHasReturn" @click="goBack"><i class="icon-arrow-left"></i></div>
      <div class="btn-confirm" v-if="!notHasConfirm" @click="confirm"><slot name="btn-confirm">这里是确定按钮内容</slot></div>
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
    },
    notHasReturn: {
      type: Boolean,
      default: false
    },
    notHasConfirm: {
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
  color $color-text-black
  background $color-white
  border-1px($color-border-grey-light, 'bottom')
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
    .icon-arrow-left
      font-size 20px
  .btn-confirm
    position absolute
    right 14px
    top 13px
    padding 4px
    font-size 15px
</style>
