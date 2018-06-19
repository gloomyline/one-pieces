<!--
@Date:   2018-01-02T10:47:15+08:00
@Last modified time: 2018-01-02T18:06:46+08:00
-->
<template>
  <div class="page-help">
    <h-header :not-has-confirm="true"><span slot="title-text">帮助中心</span></h-header>
    <group class="help-content" gutter="0">
      <cell class="common-que" title="常见问题" is-link link="/me/help/question"></cell>
      <cell class="service-phone" title="客服电话" is-link @click.native="isServicePhoneShow = true"></cell>
    </group>
    <div class="service-telephone-wrap" v-show="isServicePhoneShow">
      <div class="service-popup-box" >
        <a class="dial" href="tel:0592-5767737">客服电话：0592-5767737</a>
        <span class="icon-cross" @click="isServicePhoneShow = false"></span>
      </div>
    </div>
    <transition name="help-fade">
      <router-view class="help-children-container"></router-view>
    </transition>
  </div>
</template>

<script>
import HHeader from '@/components/APageHeader/APageHeader'
import { Group, Cell } from 'vux'

export default {
  name: 'pageHelp',
  data () {
    return {
      isServicePhoneShow: false
    }
  },
  mounted () {},
  computed: {},
  methods: {},
  components: {
    HHeader,
    Group,
    Cell
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus">
@import '../../../../../../common/stylus/mixin.styl'
@import '../../../../../../common/stylus/variable.styl'

.page-help
  .help-children-container
    position: fixed
    left: 0
    top: 0
    bottom: 0
    width: 100%
    background-color: $color-white-high
    &.help-fade-enter-active, &.help-fade-leave-active
      transition: all .6s ease
    &.help-fade-enter, &.help-fade-leave-to
      opacity: 0
  .service-telephone-wrap
    position: fixed
    left: 0
    top: 0
    bottom: 0
    width: 100%
    background-color: transparent
    .service-popup-box
      absolute: left 50% top 50%
      width: 240px
      height: 120px
      margin-left: -120px
      margin-top: -60px
      background-color: rgba(0, 0, 0, .4)
      .dial
        display: block
        width: 100%
        height: 48px
        line-height: 48px
        text-align: center
        margin-top: 32px
        font-size: 18px
        color: $color-text-yellow
      .icon-cross
        absolute: right 4px top 4px
        font-size: 20px
        color: $color-text-yellow
</style>
