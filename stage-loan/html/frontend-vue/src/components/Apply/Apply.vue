<template>
  <div class="apply">
    <apply-title>亲，您可以立即申请借款咯！</apply-title>
    <div class="apply-content">
      <!-- 借款金额 -->
      <div class="apply-account">
        <!-- 二级标题 -->
        <div class="apply-account-border"></div>
        <div class="apply-title-wrap">
          <span class="icon-apply-money"></span>
          <span class="apply-count">借款金额：{{applyCount}}元</span>
        </div>
        <!-- 额度滑动条 -->
        <div class="apply-range-wrap">
          <range ref="countRange" v-model="applyCount" 
            :min="applyCountRange.min" :max="applyCountRange.max" :step="100"
            @on-change="onCountChange" :rangeBarHeight="16"></range>
          <!-- <span class="current-count-value-hook" v-show="currentCountShow">{{animatedApplyCount}}</span> -->
          <span class="current-count-value-hook" v-show="currentCountShow">{{applyCount}}</span>
        </div>
      </div>
      <!-- 借款天数 -->
      <div class="apply-days">
        <div class="apply-title-wrap">
          <span class="icon-apply-day"></span>
          <span class="apply-day">借款周期：{{ applyPeriod | periodTransverter }}</span>
        </div>
        <!-- <div class="apply-range-wrap">
          <range ref="dayRange" v-model="applyDay" 
            :min="7" :max="30" :step="1"
            @on-change="onDayChange" :rangeBarHeight="10"></range>
          <span class="current-day-value-hook" v-show="currentDayShow">{{applyDay}}</span>
        </div> -->
        <div class="apply-wrange-period-wrap">
          <checker class="type-box" :radio-required=true v-model="applyPeriod"  default-item-class="type-item" selected-item-class="actived">
            <checker-item v-for="(item, index) in types" :value="index" :key="`type_${index}`">{{item}}</checker-item>
          </checker>
        </div>        
      </div>
      <!-- 申请借款按钮 -->
      <x-button class="apply-button" plain type='primary' action-type="button" @click.native="applyBorrow">申请借款</x-button>
    </div>
    <transition name="fade">    
      <confirm-apply ref="confirmApply" v-show="ConfirmApllyShow"></confirm-apply>
    </transition>
  </div>
</template>

<script>
import ApplyTitle from '@/components/HomeContentTitle/HomeContentTitle'
import { Range, XButton, Checker, CheckerItem } from 'vux'
import ConfirmApply from './ConfirmApply'

import TWEEN from '@tweenjs/tween.js'
/* eslint-disable no-unused-vars */
import Api from '@/api'
import session from '@/common/js/sessionStorage'

const Periods = ['一周', '两周', '一月']
const Days = [7, 14, 30]

import { createNamespacedHelpers } from 'vuex'
const namespace = 'home'
const { mapGetters } = createNamespacedHelpers(namespace)

export default {
  name: 'apply',
  data () {
    return {
      applyCount: 0,
      applyCountRange: {
        min: 500,
        max: 2000
      },
      animatedApplyCount: 0,
      applyPeriod: 1,
      animatedApplyDay: 0,
      ConfirmApllyShow: false,
      types: Periods
    }
  },
  created () {
    this.applyCountRange.min = this.user.minQuota ? this.user.minQuota : this.applyCountRange.min
    this.applyCountRange.max = this.user.maxQuota ? this.user.maxQuota : this.applyCountRange.max
  },
  mounted () {
    this.countRangeNode = this.$refs.countRange.$el
    let division = this.division = this.countRangeNode.clientWidth / ((this.applyCountRange.max - this.applyCountRange.min) / 100)
    this.$nextTick(() => {
      let rangeHandleNode = this.countRangeNode.querySelector('.range-handle')
    })
  },
  watch: {
    'applyCount' (newValue, oldValue) {
      // let _this = this
      // /* eslint-disable no-new */
      // new TWEEN.Tween({ tweeningNumber: oldValue })
      //   .easing(TWEEN.Easing.Quadratic.Out)
      //   .to({ tweeningNumber: newValue }, 1000)
      //   .onUpdate(function () {
      //     _this.animatedApplyCount = this.tweeningNumber.toFixed(0)
      //   })
      //   .start()
      // 优化动画播放，使之效果平滑
      // this._optimizeAnimation()
    },
    'applyDay' (newValue, oldValue) { // TODO: off tweeningNumber animation on applyDay
      let _this = this
      /* eslint-disable no-new */
      new TWEEN.Tween({ tweeningNumber: oldValue })
        .easing(TWEEN.Easing.Quadratic.Out)
        .to({ tweeningNumber: newValue }, 1000)
        .onUpdate(function () {
          _this.animatedApplyDay = this.tweeningNumber.toFixed(0)
        })
        .start()
      // 优化动画播放，使之效果平滑
      this._optimizeAnimation()
    }
  },
  computed: {
    ...mapGetters(['user']),
    currentCountShow () {
      let offset = 40 / this.division * 100
      return this.applyCount >= (this.applyCountRange.min + offset) && this.applyCount <= (this.applyCountRange.max - offset)
    }
  },
  methods: {
    _optimizeAnimation () {
      let animate = function () {
        if (TWEEN.update()) {
          requestAnimationFrame(animate)
        }
      }
      animate()
    },
    onCountChange () {
      let rangeHandleNode = this.countRangeNode.querySelector('.range-handle')
      rangeHandleNode.style.top = '-5px'
      let left = rangeHandleNode.style.left.replace(/px/, '') * 1
      this.$el.querySelector('.current-count-value-hook').style.left = `${left + 34}px`
    },
    onDayChange () {
      let rangeHandleNode = this.dayRangeNode.querySelector('.range-handle')
      rangeHandleNode.style.top = '-5px'
      let left = rangeHandleNode.style.left.replace(/px/, '') * 1
      this.$el.querySelector('.current-day-value-hook').style.left = `${left + 40}px`
    },
    async applyBorrow () {
      // confirm user authority
      let isLogon = session.loadLoginStatus().isLogin
      if (!isLogon) {   // user is not logon
        this.$vux.alert.show({
          content: '亲，请先登录！',
          onHide: function () {
            this.$router.push('/login')
          }.bind(this)
        })
        return
      } else {          // user is Logon
        let res = await Api.applyLoan({amount: this.applyCount, period: Days[this.applyPeriod]})
        this.$refs.confirmApply.show(res)
        this.ConfirmApllyShow = true
      }
    }
  },
  filters: {
    periodTransverter (num) {
      return Periods[num]
    }
  },
  components: {
    ApplyTitle,
    Range,
    XButton,
    Checker,
    CheckerItem,
    ConfirmApply
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus">
@import '../../common/stylus/mixin.styl'
@import '../../common/stylus/variable.styl'

.apply
  width 100%
  color $color-text-black
  .apply-content
    width 100%
    margin-top 20px
    .apply-account
      position relative
      margin-bottom 48px
      .apply-account-border
        position absolute
        top 0
        left 5%
        width 90%
        height 90px
        // border 1px solid $color-black
        border-radius 5px 5px
        box-shadow 0px 0px 10px rgba(28, 28, 28, .2)
      .apply-range-wrap
        .current-count-value-hook
          bottom -32px
          font-size 18px
        .vux-range-input-box
          .range-bar
            .range-min
              top 32px
              font-size 16px
              color $color-text-black
              &:after
                display inline-block
                position absolute
                top 0
                right -20px
                width 16px
                height 14px
                font-size 14px
                content "元"
            .range-max
              top 32px
              right 10px
              font-size 16px
              color $color-text-black
              &:after
                display inline-block
                position absolute
                top 0
                right -28px
                width 16px
                height 14px
                font-size 14px
                content "元"
    .apply-days
      width 90%
      height 90px
      margin 0 auto
      border-radius 5px 5px
      box-shadow 0px 0px 10px rgba(28, 28, 28, .2)
      .apply-wrange-period-wrap
        z-index 99
        .type-box
          display flex
          flex-flow row wrap
          justify-content space-around
          width 100%
          height 95px
          margin 0 auto
          padding-top 12px
          .type-item
            flex 0 0 80px
            width 80px
            height 30px
            line-height 30px
            text-align center
            font-size 14px
            color $color-text-blue
            border 1px solid $color-blue
            border-radius 10px
            &.actived
              color $color-text-white
              background-color $color-blue
      .apply-title-wrap
        margin-left 4px
    .apply-title-wrap
      width 100%
      height 30px
      margin-left 24px
      padding-top 4px
      line-height 30px
      font-size 0
      font-weight 600
      .icon-apply-money
        margin-top 5px
        inline-icon(20px, 20px)
        bg-img('./icon_borrow_money')
      .apply-count
        display inline-block
        vertical-align top
        margin-left 10px
        font-size 16px
      .icon-apply-day
        margin-top 5px
        inline-icon(20px, 20px)
        bg-img('./icon_borrow_days')
      .apply-day
        display inline-block
        vertical-align top
        margin-left 10px
        font-size 16px
    .apply-range-wrap
      position relative
      margin-top 10px
      .vux-range-input-box
        left -10px
        .range-bar
          background $color-blue-lightest
          .range-handle
            top -12px
            width 24px
            height 32px
            box-shadow none
            background transparent
            border-radius 0
            bg-img('./icon_range_handle')
            background-size 100% 100%
            background-repeat no-repeat
          .range-min, .range-max
            top 20px
            font-size 12px 
            color $color-text-grey
          .range-min
            left -5px
            &:after
              display inline-block
              position absolute
              top 0
              width 16px
              height 14px
              font-size 12px
              content "天"
          .range-max
            right 5px
            &:after
              display inline-block
              position absolute
              top 0
              right -10px
              width 16px
              height 14px
              font-size 12px
              content "天"
          .range-quantity
            border-radius 15px 0 0 15px
            background url('./box_range_quantity.png')
            background-size 100% 100%
            background-repeat no-repeat
      .current-count-value-hook, .current-day-value-hook
        position absolute
        bottom -18px
        font-size 13px
        color $color-text-black
    .apply-button
      // width 250px
      width 90%
      height 40px
      margin-top 16px
      font-size 18px
      color $color-text-blue
      letter-spacing 2px
      border 1px solid $color-blue
      border-radius 20px 20px
      &:active
        color $color-text-blue-lightter
        border-color $color-blue-lightter
  .page-confirm-apply
    &.fade-enter-active, &.fade-leave-active
      transition all .6s ease
    &.fade-enter, &.fade-leave-to
      opacity 0
</style>