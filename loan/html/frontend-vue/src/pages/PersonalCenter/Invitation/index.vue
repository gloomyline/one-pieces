<template>
  <div class="page-invitation">
    <div class="invitation-bg">
      <img src="./bg_invitation.jpg" width="375" height="603" alt="">
    </div>
    <i-header ref="header"><span slot="title-text">邀请好友</span><span slot="btn-confirm"></span></i-header>
    <x-button class="btn-invite" type="primary" action-type="button" @click.native="invite">立即邀请</x-button>
    <div class="infos">
      <div class="invitaion-count">
        <h3 class="title">邀请人数</h3>
        <p class="count">{{ invitationCount }}<small>人</small></p>
        <x-button class="btn-invitation-record" plain type="primary" action-type="button" @click.native="openInvitationRecord">邀请记录</x-button>
      </div>
      <div class="vertical-line"></div>
      <div class="my-income">
        <h3 class="title">我的收入</h3>
        <p class="count">{{ income }}<small>元</small></p>
        <x-button class="btn-withdraw-deposit" plain type="primary" action-type="button" @click.native="withdrawDeposit">立即提现</x-button>
      </div>
      <transition name="fade">
        <div class="withdraw-tip-popup" v-show="withdrawTipShow"> 
          <div class="tip-box-mask" @click="hideWidthDrawTip"></div>
          <div class="tip-box">
            <div class="icon-wrap">
              <span class="icon-excalmatory"></span>
            </div>
            <p class="least-withdraw">最低提现金额10.00元</p>
            <p class="single-withdraw">邀请1人借款&nbsp;&nbsp;赚10元</p>
            <p class="more-withdraw"><span class="left">多邀多得</span><span class="right">上不封顶</span></p>
            <div class="btn-close-tip" @click="hideWidthDrawTip">
              <span class="icon-close"></span>
            </div>
          </div>
        </div>
      </transition>
    </div>
    <transition name="router-fade" mode="out-in">
      <keep-alive>
        <router-view class="invitation-children-container"></router-view>
      </keep-alive>
    </transition>
  </div>
</template>

<script>
import IHeader from '@/components/APageHeader/APageHeader'
import { XButton } from 'vux'

export default {
  name: 'app',
  data () {
    return {
      invitationCount: 0,
      income: 0,
      withdrawTipShow: false
    }
  },
  mounted () {
    this.$refs.header.$el.style.background = 'transparent'
  },
  computed: {
  },
  methods: {
    invite () {
      // wechat js-sdk handle
    },
    openInvitationRecord () {
      this.$router.push('/personal/invitation/record')
    },
    withdrawDeposit () {
      if (this.income < 10) { // 提现金额不足最低10，弹出提现弹窗
        this.withdrawTipShow = true
        return
      } else {                // 满足提现条件，转到提现页
        this.$router.push('/personal/invitation/withdraw')
      }
    },
    hideWidthDrawTip () {
      this.withdrawTipShow = false
    }
  },
  filters: {
    convertBigNumber (value) {
      if (value !== 0 && !value) return 0
      value = value * 1
      const tenThousand = Math.pow(10, 4)
      let tenThousandVal = Math.floor(value / tenThousand)
      return `${tenThousandVal === 0 ? '' : tenThousandVal + '万'}${(value % tenThousand === 0) ? '' : (value % tenThousand)}`
    }
  },
  components: {
    IHeader,
    XButton
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus" scoped>
@import '../../../common/stylus/mixin.styl'
@import '../../../common/stylus/variable.styl'

.page-invitation
  .invitation-bg
    position fixed
    left 0
    top 0
    bottom 0
    width 100%
    overflow hidden
    z-index -1
  .btn-invite
    width 300px
    height 40px
    margin-top 180px
    font-size 18px
    color $color-text-white
    letter-spacing 1px
    font-family 'Microsoft Yahei'
    background-color $color-yellow
    border-radius 20px
    box-shadow 3px 2px 5px rgba(28, 28, 28, .2)
    &:active
      background-color $color-yellow
  .infos
    display flex
    position relative
    width 100%
    margin-top 36px
    height 108px
    padding 4px 0
    color $color-text-white
    .invitaion-count, .my-income
      flex 0 0 50%
      width 50%
      text-align center
      .title
        line-height 24px
        font-size 12px
    .btn-invitation-record, .btn-withdraw-deposit
      width 100px
      height 35px
      font-size 14px
      color $color-text-white
      border-color $color-white
      border-radius 17px
      &:active
        border-color $color-white
    .invitaion-count
      .count
        font-size 20px
        line-height 32px
    .vertical-line
      position absolute
      left 50%
      top 4px
      width 1px
      height 100%
      margin-left -1px
      background linear-gradient(rgb(35, 145, 251), rgba(255, 255, 255, .5), rgb(35, 145, 251))
    .my-income
      .count
        font-size 20px
        line-height 32px
    .withdraw-tip-popup
      position fixed
      left 0
      top 0
      bottom 0
      width 100%
      overflow hidden
      &.fade-enter-active, &.fade-leave-active
        transition all .4s ease
      &.fade-enter, &.fade-leave-to
        opacity 0
      .tip-box-mask
        position absolute
        left 0
        top 0
        width 100%
        height 100%
        background rgba(0, 0, 0, .4)
      .tip-box
        position absolute
        left 50%
        top 50%
        width 285px
        height 210px
        margin -105px 0 0 -143px
        text-align center
        background url('./box_invitation_withdraw_tip.png')
        background-repeat no-repeat
        background-size 100% 100%
        .icon-wrap
          position absolute
          top -24px
          left 50%
          margin-left -35px
          .icon-excalmatory
            inline-icon(70px, 70px)
            bg-img('./icon_exclamation_point')
        .btn-close-tip
          position absolute
          right 0
          top 0
          padding 8px
          .icon-close
            inline-icon(25px, 25px)
            bg-img('./icon_close')
        .least-withdraw
          margin-top 54px
          line-height 28px
          font-size 16px
          color $color-text-white
        .single-withdraw
          margin-top 48px
          line-height 28px
          font-size 16px
          color $color-text-yellow
        .more-withdraw
          margin-top 10px
          font-size 0
          color #7a7777
          .left
            display inline-block
            margin-right 15px
            font-size 16px
          .right
            display inline-block
            font-size 16px
  .invitation-children-container
    position fixed
    left 0
    top 0
    bottom 0
    width 100%
    background $color-white
    &.router-fade-enter-active, &.router-fade-leave-active
      transition all .6s ease
    &.router-fade-enter, &.router-fade-leave-to
      opacity 0
</style>