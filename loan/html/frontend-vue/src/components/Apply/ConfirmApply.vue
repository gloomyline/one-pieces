<template>
  <div class="page-confirm-apply">
    <c-a-header not-go-back  @go-back="goBack"><span slot="title-text">借款确认</span><span slot="btn-confirm"></span></c-a-header>
    <group class="loan-infos" gutter="0">
      <cell class="loan-gist" :title="loanGist"></cell>
      <cell class="loan-count" title="借款金额" value-align="left" :value="arrivalAmount"></cell>
      <cell class="loan-day" title="借款期限" value-align="left" :value="loanDay"></cell>
      <cell class="bind-bankcard" title="到账银行" value-align="left" :value="bindBankcard"></cell>
      <cell class="arrival-amount" title="到账金额" value-align="left" :value="arrivalAmount"></cell>
      <cell class="charge" title="借款息费" value-align="left" :value="charge"></cell>
    </group>
    <section class="charge-tips">
      <p class="title">根据您当前信用情况，借款息费计算如下：</p>
      <p class="equation">息费总计{{charge}}元 = 利息 {{interest}} 元 + 费用 {{others.total}} 元</p>
      <ul class="details">
        <li class="interest">
          <p class="title">(1) 利息共计 {{interest}} 元</p>
          <p class="detail">年化利率{{ interestRate * 100 }}%，由银行，信托，消费金融公司等收取</p>
        </li>
        <li class="others">
          <p class="title">(2) 费用共计 {{others.total}} 元</p>
          <p class="detail">信审费 {{others.trialFee}} 元 平台服务费 {{others.serviceFee}} 元 手续费 {{others.poundage}} 元</p>
        </li>
      </ul>
    </section>
    <div class="footer">
      <div class="user-protocol-link-wrap">
        <div class="icon-check-wrap" @click="protocolCheck = !protocolCheck">
          <span class="icon" :class="{'icon-check': protocolCheck, 'icon-not-check': !protocolCheck}"></span>
        </div>
        <div class="user-protocol-link" @click="showProtocol">同意<span class="color-text-blue">《借款合同》</span></div> 
      </div>
      <transition name="fade">
        <div class="user-protocol-content" v-show="protocolShow">
          <page-protocol protocol-type="loanContract" ref="protocol" @close="protocolShow = !protocolShow"></page-protocol>
        </div>
      </transition>
      <x-button class="btn-loan-confirm" type="primary" action-type="button" @click.native="applyForLoan">确认借款</x-button> 
    </div>
  </div>
</template>

<script>
import CAHeader from '@/components/APageHeader/APageHeader'
import { Group, Cell, XButton } from 'vux'
import PageProtocol from '@/pages/Protocol'

import api from '@/api'
import event from '@/common/js/event'

export default {
  name: 'pageConfirmApply',
  data () {
    return {
      day: 7,
      count: 800,
      bindBankcard: '招商银行（尾号0447）',
      arrivalAmount: 0,
      charge: 8,
      interest: 3,
      interestRate: '1.25%',
      others: {
        total: 5,
        trialFee: 3,
        serviceFee: 2,
        poundage: 0
      },
      protocolCheck: false,
      protocolShow: false
    }
  },
  mounted () {},
  computed: {
    loanGist () {
      return `您需${this.day}天内，还款${this.count + this.charge}元`
    },
    loanDay () {
      return `${this.day}天`
    },
    loanCount () {
      return `${this.arrivalAmount + this.charge}元`
    }
  },
  methods: {
    goBack () {
      this.$parent.ConfirmApllyShow = false
    },
    show (res) {
      res = res[0]
      this.count = res.amount
      this.day = res.period
      this.charge = res.interest
      this.bindBankcard = res.bank
      this.arrivalAmount = res.arrival_amount
      this.interest = res.accrual
      this.interestRate = res.annualized_interest_rate
      this.others.total = res.otherFee
      this.others.trialFee = res.trialFee
      this.others.serviceFee = res.serviceFee
      this.others.poundage = res.poundage
    },
    showProtocol () {
      this.protocolShow = !this.protocolShow
      this.$refs.protocol.show()
    },
    hideProtocol () {
      this.protocolShow = !this.protocolShow
    },
    async applyForLoan () {
      if (!this.protocolCheck) {
        this.$vux.alert.show({
          content: '请认真阅读用户协议后勾选'
        })
        return
      }
      let postData = {
        amount: this.count,
        period: this.day
      }
      let res = await api.confirmLoan(postData)
      if (res.status === 'SUCCESS') {
        this.$vux.alert.show({
          content: '订单提交成功',
          onHide: function () {
            event.eventBus.$emit(event.eventType.EVENT_LOAN_CONFIRMED)
            this.goBack()
          }.bind(this)
        })
      }
    }
  },
  components: {
    CAHeader,
    Group,
    Cell,
    XButton,
    PageProtocol
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus">
@import '../../common/stylus/mixin.styl'
@import '../../common/stylus/variable.styl'

.page-confirm-apply
  position fixed
  top 0
  left 0
  bottom 0
  width 100%
  overflow hidden
  background $color-white-light
  z-index 10
  .loan-infos
    font-size 15px
    color $color-text-grey-higher
    .weui-cell
      height 48px !important
      padding-left 25px
      .weui-cell__ft
        padding-left 47px
        color $color-text-black
    .loan-gist
      height 65px
      color $color-text-yellow
  .charge-tips
    width 100%
    height 145px
    margin-top 16px
    padding 0 25px
    font-size 12px
    line-height 1.8em
    color $color-text-grey-highest
    overflow hidden
    .details
      list-style-type none
  .footer
    .user-protocol-link-wrap
      padding 0 25px
      font-size 0
      color $color-text-black
      .icon-check-wrap
        display inline-block
        vertical-align top
        padding 4px
        .icon
          color $color-text-blue
          font-size 18px
          // inline-icon(15px, 15px)
          // bg-img('./icon_check')
          // &.actived
          //   bg-img('./icon_check_s')
      .user-protocol-link
        display inline-block
        vertical-align top
        margin-top 6px
        margin-left 4px
        font-size 14px
    .user-protocol-content
      position fixed
      top 0
      left 0
      bottom 0
      width 100%
      overflow hidden
      background $color-white
      &.fade-enter-active, &.fade-leave-active
        transition all .6s ease
      &.fade-enter, &.fade-leave-to
        opacity 0
    .btn-loan-confirm
      position fixed
      left 0
      bottom 0
      height 48px
      border-radius 0
      background-color $color-yellow
      &:active
        background-color $color-yellow
</style>