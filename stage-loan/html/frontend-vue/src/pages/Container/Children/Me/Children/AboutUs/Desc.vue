<!--
@Date:   2017-12-29T17:04:29+08:00
@Last modified time: 2018-01-02T10:17:41+08:00
-->
<template>
  <div class="page-wk-desc">
    <w-header @go-back="goBack" not-go-back :not-has-confirm="true">
      <span slot="title-text">分期平台介绍</span>
    </w-header>
    <div class="container">
      <div class="box-wrap">
        <div class="box">
          <p>{{desc}}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import WHeader from '@/components/APageHeader/APageHeader'

import UsDescData from '@/assets/datas/UsDesc'

export default {
  name: 'pageWkDesc',
  data () {
    return {
      desc: UsDescData[0]
    }
  },
  mounted () {},
  computed: {},
  methods: {
    goBack (event) {
      this.$emit('close', event)
    }
  },
  components: {
    WHeader
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus" scoped>
@import '../../../../../../common/stylus/variable.styl'

.page-wk-desc
  width 100%
  height 100%
  .container
    width 100%
    height 100%
    .box-wrap
      width 100%
      height 100%
      padding-top 42px
      background url('./img_about_us_bg.jpg')
      background-size 100% 100%
      background-repeat no-repeat
      .box
        width 340px
        height 460px
        margin 0 auto
        padding 2em 1.6em
        line-height 2em
        font-size 15px
        color $color-white
        border-radius 15px
        background rgba(0, 0, 0, .5)
</style>
