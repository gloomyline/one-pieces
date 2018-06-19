<!--
@Date:   2018-01-09T14:39:41+08:00
@Last modified time: 2018-01-31T11:00:46+08:00
-->
<template lang="html">
  <div class="page-lend">
    <l-header class="lend-header" not-go-back not-has-confirm @go-back="goBack">
      <span class="title" slot="title-text">我的借款</span>
    </l-header>
    <div class="lend-content">
      <tab class="lend-tab">
        <tab-item class="lend-item"
        :class="{ 'actived': item === selected, 'lend': index === 0, 'refund': index === 1 }"
        :selected="item === selected"
        v-for="(item, index) in tabs" :key="`item_${index}`"
        @on-item-click="select(item)">{{item}}</tab-item>
      </tab>
      <transition name="router-fade">
        <keep-alive>
          <router-view class="lend-container"></router-view>
        </keep-alive>
      </transition>
    </div>
  </div>
</template>

<script>
import LHeader from '@/components/APageHeader/APageHeader'
import { Tab, TabItem } from 'vux'

export default {
  data () {
    return {
      currentIndex: 0,
      tabs: ['借款记录', '还款记录'],
      selected: '借款记录'
    }
  },
  mounted () {
  },
  methods: {
    select (item) {
      let toPath = this.tabs[0] === item ? 'lends' : 'refunds'
      this.selected = item
      this.$router.push(`/me/lend/${toPath}`)
    },
    goBack () {
      this.$router.push('/me')
    }
  },
  components: {
    LHeader,
    Tab,
    TabItem
  }
}
</script>

<style lang="stylus">
@import '../../../../../../common/stylus/mixin.styl'
@import '../../../../../../common/stylus/variable.styl'

.page-lend
  background-color: $color-white-high
  .lend-header
    border-1px($color-border-grey-light, 'bottom')
  .lend-content
    .lend-tab
      height 36px
      margin 15px auto 10px
      border-radius 18px
      .lend-item
        line-height 32px
        font-size 15px
        color $color-text-black
        border none
        &.actived
          color $color-text-yellow
    .lend-container
      width: 100%
      height: 547px
      overflow: hidden
      &.router-fade-enter-active, &.router-fade-leave-active
        transition: all .6s ease
      &.router-fade-enter, &.router-fade-leave-to
        opacity: 0
</style>
