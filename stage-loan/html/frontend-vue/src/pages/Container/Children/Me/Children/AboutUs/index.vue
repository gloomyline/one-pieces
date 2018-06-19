<!--
@Date:   2017-12-29T16:57:35+08:00
@Last modified time: 2018-01-02T10:42:33+08:00
-->
<template>
  <div class="us">
    <header class="us-header">
      <u-header ref="header"><span slot="title-text">关于我们</span><span slot="btn-confirm"></span></u-header>
    </header>
    <group class="us-support" gutter="0">
      <cell class="wk-desc" title="分期平台介绍" is-link @click.native="descShow = !descShow"><span class="icon icon-list-1" slot="icon"></span></cell>
      <cell class="version-update" title="版本更新" is-link @click.native="versionShow = true"><span class="icon icon-up-arrow" slot="icon"></span></cell>
      <cell class="feedback" title="问题反馈" is-link @click.native="feedbackShow = true">
        <span class="icon icon-que-mark-1" slot="icon"></span>
      </cell>
      <cell class="official-wechat" title="官方微信" is-link></cell>
    </group>
    <transition name="desc-fade">
      <div class="desc-popup-container" v-show="descShow">
        <w-desc @close="descShow = !descShow"></w-desc>
      </div>
    </transition>
    <transition name="feedback-fade">
      <feedback v-show="feedbackShow" @close="feedbackShow = !feedbackShow"></feedback>
    </transition>
    <transition name="version-fade">
      <div class="version-update-box" v-show="versionShow" @click="versionShow = false">{{versionUpdateMsg}}</div>
    </transition>
  </div>
</template>

<script>
import UHeader from '@/components/APageHeader/APageHeader'
import Feedback from './Feedback'
import { Group, Cell } from 'vux'
import WDesc from './Desc'

export default {
  name: 'us',
  data () {
    return {
      descShow: false,
      versionShow: false,
      feedbackShow: false,
      versionUpdateMsg: '当前已经是最新版本了'
    }
  },
  mounted () {
    this.$refs.header.$el.style.background = 'transparent'
  },
  computed: {},
  methods: {
  },
  components: {
    UHeader,
    Feedback,
    Group,
    Cell,
    WDesc
  }
}
</script>

<style lang="stylus">
@import '../../../../../../common/stylus/mixin.styl'
@import '../../../../../../common/stylus/variable.styl'

.us
  .us-header
    width 100%
    border-1px($color-border-grey-light, 'bottom')
    .logo-wrap
      width 71px
      height 71px
      margin 60px auto 0
      .logo
        inline-icon(71px, 71px)
        // bg-img('../../../assets/imgs/logo')
  .us-support
    .weui-cells
      &:before
        display none
      .weui-cell
        .weui-cell__hd
          margin 4px 15px 0 0
          .icon
            font-size 20px
            color $color-text-blue
        .vux-cell-bd
          font-size 15px
          color $color-text-black
  .desc-popup-container
    position fixed
    left 0
    top 0
    bottom 0
    width 100%
    background $color-white
    &.desc-fade-enter-active, &.desc-fade-leave-active
      transition all .6s ease
    &.desc-fade-enter, &.desc-fade-leave-to
      opacity 0
  .version-update-box
    width 175px
    height 45px
    margin 20px auto 0
    line-height 45px
    text-align center
    font-size 14px
    color $color-text-white
    border-radius 15px
    background rgba(0, 0, 0, .5)
    &.version-fade-enter-active, &.version-fade-leave-active
      transition all .6s ease
    &.version-fade-enter, &.version-fade-leave-to
      opacity 0
  .page-feedback
    &.feedback-fade-enter-active, &.feedback-fade-leave-active
      transition: all .6s ease
    &.feedback-fade-enter, &.feedback-fade-leave-to
      opacity: 0
</style>
