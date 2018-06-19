<!--
@Date:   2017-12-14T17:05:37+08:00
@Last modified time: 2018-01-03T17:19:43+08:00
-->
<template>
  <div class="home-page">
    <div class="bg-wrap"><img :src="bgImg" width="100%" height="100%"></div>
    <div class="container">
      <div class="consume times-card">
        <div class="img-wrap"><img :src="consumeImg" width="100%" height="100%"></div>
        <div class="desc">
          <h3 class="title">消费分期</h3>
          <p class="detail">线下商户扫码消费</p>
          <div class="link-wrap">
            <router-link to="/home/consume">立即分期</router-link>
          </div>
        </div>
      </div>
      <div class="cash times-card">
        <div class="img-wrap"><img :src="cashImg" width="100%" height="100%"></div>
        <div class="desc">
          <h3 class="title">现金分期</h3>
          <p class="detail">线上预借现金</p>
          <div class="link-wrap">
            <router-link to="/home/cash">立即分期</router-link>
          </div>
        </div>
      </div>
      <transition name="router-fade">
        <router-view class="home-children-container"></router-view>
      </transition>
    </div>
  </div>
</template>

<script>
export default {
  name: 'homePage',
  data () {
    return {
      bgImg: require('../../../../assets/imgs/bg-home.jpg'),
      consumeImg: require('../../../../assets/imgs/times-consume.jpg'),
      cashImg: require('../../../../assets/imgs/times-cash.jpg')
    }
  },
  computed: {},
  methods: {
  },
  components: {}
}
</script>

<style lang="stylus" ref="stylesheet/stylus" scoped>
@import '../../../../common/stylus/mixin.styl'
@import '../../../../common/stylus/variable.styl'

.home-page
  .bg-wrap
    absolute: top left
    height: 100%
    padding-bottom: 58px
    z-index: -1
  .container
    padding-top: 140px
    .times-card
      width: 325px
      height: 170px
      margin 0 auto
      border-radius 8px 8px
      border 1px solid $color-border-grey
      background-color: $color-white
      box-shadow: 4px 4px 8px $color-shadow-black-24
      .img-wrap
        display: inline-block
        vertical-align: top
        width: 185px
        height: 160px
        padding 18px 24px 0 26px
      .desc
        display: inline-block
        vertical-align: top
        .title
          margin-top: 36px
          font-size: 20px
          font-weight: bold
          color: $color-text-yellow
        .detail
          margin-top: 15px
          font-size: 14px
          color $color-text-black-light
        .link-wrap
          margin-top: 16px
          width: 100px
          height: 35px
          border 1px solid $color-border-yellow
          border-radius 16px
          a
            display: block
            width: 100%
            height: 100%
            line-height: 35px
            text-align: center
            font-size: 16px
            color $color-text-yellow
            text-decoration none
      &.cash
        margin-top: 25px
    .home-children-container
      position fixed
      left 0
      top 0
      bottom 0
      width: 100%
      overflow: hidden
      background-color: $color-white-high
      z-index: 20
      &.router-fade-enter-active, &.router-fade-leave-active
        transition: all .6s ease
      &.router-fade-enter, $.router-fade-leave-to
        opacity: 0
</style>
