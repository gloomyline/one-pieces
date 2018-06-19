<template>
  <div class="home-page">
    <div class="home-header">
      <!-- 标题 -->
      <!-- <div class="title-wrap">
        <h2 class="title">悟空互金</h2>
      </div> -->
      <!-- 个人信息 && 认证中心 -->
      <div class="extra clearfix">
        <div class="self-info" @click="openPersonal">
          <div class="icon-wrap"><i class="icon-authen-center"></i></div>
          <span class="card">{{ card }}</span>
        </div>
        <!-- 个人中心列表 -->
        <transition name="fold">
          <personal-list class="personal" v-show="personalListShow" @open-service="openService"></personal-list>
        </transition>
        <transition name="fade">
          <div class="personal-mask" @click="closePersonal" v-show="personalListShow"></div>
        </transition>
        <!-- 客服弹窗 -->
        <transition name="fade">
          <page-service class="popup-service" v-show="serviceShow" @close="closeService"></page-service>
        </transition>
        <div class="authentication-center" @click="gotoAuthentication">
          <span class="text">认证中心</span>
          <div class="icon-wrap"><i class="icon-return"></i></div>
        </div>
      </div>
      <!-- 信用额度 -->
      <div class="line-credit">
        <div class="line-credit-wrap">
          <p class="title">可用额度(元)</p>
          <p class="limit">{{ quota }}</p>
          <p class="count">成功借款：{{ successCount ? successCount : 0 }}次</p>
        </div>
      </div>
    </div>
    <div class="home-content">
      <group class="borrow-status-selector" v-if="selectorShow">
        <selector v-model="selected" :options="borrowStatusList" @on-change="onChange"></selector>
      </group>
      <apply v-if="borrowStatus === 0"></apply>
      <review v-if="borrowStatus === 1"></review>
      <apply-success v-if="borrowStatus === 2"></apply-success>
      <apply-fail v-if="borrowStatus === 3"></apply-fail>
      <wait-refund v-if="borrowStatus === 4"></wait-refund>
      <lending v-if="borrowStatus === 5"></lending>
      <lend-fail v-if="borrowStatus === 6"></lend-fail>
      <overdue v-if="borrowStatus === 7"></overdue>
      <overdue :is-upper-limit="true" v-if="borrowStatus === 8"></overdue>
      <refunding v-if="borrowStatus === 9"></refunding>
      <refund-success v-if="borrowStatus === 10"></refund-success>
      <refund-fail v-if="borrowStatus === 11"></refund-fail>
    </div>
    <transition name="router-fade">
      <router-view class="home-children-container"></router-view>
    </transition>
  </div>
</template>

<script>
import Apply from '@/components/Apply/Apply'
import Review from '@/components/Review/Review'
import ApplySuccess from '@/components/ApplySuccess/ApplySuccess'
import ApplyFail from '@/components/ApplyFail/ApplyFail'
import WaitRefund from '@/components/WaitRefund/WaitRefund'
import Lending from '@/components/Lending/Lending'
import LendFail from '@/components/LendFail/LendFail'
import Overdue from '@/components/Overdue/Overdue'
import Refunding from '@/components/Refunding/Refunding'
import RefundSuccess from '@/components/RefundSuccess/RefundSuccess'
import RefundFail from '@/components/RefundFail/RefundFail'
import { Group, Selector } from 'vux'
import PersonalList from '@/components/PersonalList/PersonalList'
import PageService from '@/pages/PersonalCenter/Service'

import _ from 'lodash'
import event from '@/common/js/event'

import session from '@/common/js/sessionStorage'

import { createNamespacedHelpers } from 'vuex'
const namespace = 'home'
const { mapGetters, mapActions } = createNamespacedHelpers(namespace)

const STATUS_APPLY = 0            // 申请
const STATUS_REVIEW = 1           // 审核中
const STATUS_SUCCESS = 2          // 审核通过
const STATUS_FAIL = 3             // 审核失败
const STATUS_WAIT_RETURN = 4      // 待还款
const STATUS_LENDING = 5          // 放款中
// const STATUS_LEND_FAIL = 6        // 放款失败
const STATUS_OVERDUE = 7          // 逾期中
// const STATUS_OVERDUE_MAX = 8      // 逾期上限
// const STATUS_REFUNDING = 9        // 还款中
const STATUS_REFUND_SUCCESS = 10  // 还款成功
// const STATUS_REFUND_FAIL = 11     // 还款失败

const Status = {
  'none': STATUS_APPLY,
  'auditing': STATUS_REVIEW,
  'audit_failure': STATUS_FAIL,
  // 'review_failure': STATUS_FAIL,
  'audit_success': STATUS_SUCCESS,
  'granting': STATUS_LENDING,
  'repaying': STATUS_WAIT_RETURN,
  'finished': STATUS_REFUND_SUCCESS,
  'overdue': STATUS_OVERDUE
}

export default {
  name: 'homePage',
  data () {
    return {
      hasMessage: true,
      selected: '',
      borrowCount: 0,
      creditLimit: 0,
      orderDefaultStatus: 0,
      borrowStatusList: ['申请', '审核中', '审核通过', '审核失败', '待还款', '放款中', '放款失败', '逾期中', '逾期上限', '还款中', '还款成功', '还款失败'],
      personalListShow: false,
      serviceShow: false,
      selectorShow: process.env.NODE_ENV === 'development'
      // selectorShow: false
    }
  },
  async created () {
    await this.fetchData()
    this.selected = this.borrowStatusList[this.orderStatus]
  },
  mounted () {
    event.eventBus.$on(event.eventType.EVENT_LOAN_CONFIRMED, async () => {
      await this.fetchData()
      this.selected = this.borrowStatusList[this.orderStatus]
    })
  },
  async activated () {
  },
  watch: {
    'orderState' (newVal, oldVal) {
      if (newVal === null && (oldVal === 'audit_failure' || oldVal === 'finished')) { // 切换订单状态 审核失败 | 完成 => 申请
        this.selected = this.borrowStatusList[0]
      }
    }
  },
  computed: {
    ...mapGetters([
      'quota', 'successCount', 'loanId', 'orderState', 'card'
    ]),
    orderStatus () {
      if (!this.orderState) return this.orderDefaultStatus
      return Status[this.orderState] ? Status[this.orderState] : this.orderDefaultStatus
    },
    borrowStatus () {
      return _.indexOf(this.borrowStatusList, this.selected)
    }
  },
  methods: {
    ...mapActions([
      'fetchData'
    ]),
    async gotoMsgCenter () { // 转到消息中心
      console.log('go to MsgCenter')
    },
    _alertShow (content, cb) {
      this.$vux.alert.show({
        content,
        onHide: function () {
          if (this.timer) clearTimeout(this.timer)
          cb && cb()
        }.bind(this)
      })
      this.timer = setTimeout(() => { this.$vux.alert.hide() }, 3000)
    },
    openPersonal () {
      if (!session.loadLoginStatus()) {
        this._alertShow('请先登录后查看个人信息', () => {
          this.$router.push('/login')
        })
      } else {
        this.personalListShow = true
      }
    },
    closePersonal () {
      this.personalListShow = false
    },
    gotoAuthentication () {
      this.$router.push('/authentication')
    },
    onChange (val) {
      console.log('切换订单状态', val)
    },
    openService () {
      this.serviceShow = true
    },
    closeService () {
      this.serviceShow = false
    }
  },
  components: {
    Apply,
    Review,
    ApplySuccess,
    ApplyFail,
    WaitRefund,
    Lending,
    LendFail,
    Overdue,
    Refunding,
    RefundSuccess,
    RefundFail,
    Group,
    Selector,
    PersonalList,
    PageService
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus" scoped>
@import '../../common/stylus/mixin.styl'
@import '../../common/stylus/variable.styl'

.home-page
  position fixed
  left 0
  top 0
  bottom 0
  width 100%
  overflow hidden
  color $color-text-white  
  .home-header
    width 100%
    height 320px
    background url('../../assets/imgs/header_bg.png')
    background-size 100% 100%
    background-repeat no-repeat
    .title-wrap
      position relative
      height 36px
      line-height 36px
      .title
        text-align center  
        font-size 18px
        font-weight 400
        letter-spacing 3px
    .extra
      width 100%
      height 48px
      line-height 48px
      letter-spacing 2px    
      .self-info
        float left
        margin-left 14px
        font-size 0
        .icon-wrap
          display inline-block
          vertical-align top
          margin-top 13px
          .icon-authen-center
            font-size 18px
        .card
          display inline-block
          vertical-align top
          margin-top -1px
          margin-left 6px
          font-size 18px
      .personal
        position fixed
        left 0
        top 0
        bottom 0
        width 280px
        background $color-white
        transform translate3d(0, 0, 0)
        z-index 99
        &.fold-enter-active
          transition all .8s ease
        &.fold-leave-active
          transition all .6s cubic-bezier(1, .4, .71, 1.52)
        &.fold-enter, &.fold-leave-to
          opacity 0.8
          transform translate3d(-100%, 0, 0)
      .personal-mask
        position fixed
        left 0
        top 0
        bottom 0
        width 100%
        background rgba(0, 0, 0, .4)
        z-index 60
        &.fade-enter-active, &.fade-leave-active
          transition all .6s ease
        &.fade-enter, &.fade-leave-to
          opacity 0
      .popup-service
        position fixed
        left 0
        top 0
        bottom 0
        width 100%
        overflow hidden
        z-index 999
        background rgba(0, 0, 0, .4)
        &.fade-enter-active, &.fade-leave-active
          transition all .6s ease
        &.fade-enter, &.fade-leave-to
          opacity 0
      .authentication-center
        float right
        font-size 0
        .text
          display inline-block
          vertical-align top
          font-size 18px
        .icon-wrap
          display inline-block
          vertical-align top
          margin-top 14px
          padding-left 8px
          transform scaleX(-1)
          .icon-return
            font-size 18px
    .line-credit
      position relative
      width 100%
      margin-top -16px
      .line-credit-wrap
        width 210px
        height 210px
        margin 0 auto
        padding-top 40px
        text-align center
        background url('./box_line_credit.png')
        background-size 100% 100%
        background-repeat no-repeat
        .title
          line-height 32px
          font-size 18px
          letter-spacing 2px
        .limit
          line-height 80px
          font-size 55px
          letter-spacing 5px
        .count
          width 125px
          height 25px
          margin 0 auto
          font-size 14px
          line-height 25px
          border-radius 13px 13px
          border 1px solid #fff
  .home-content
    position relative
    width 100%
    .borrow-status-selector
      position absolute
      left 0
      top -100px
      width 100%
  .home-children-container
    position fixed
    left 0
    top 0
    bottom 0
    width 100%
    overflow hidden
    background #fff
    z-index 999
    &.router-fade-enter-active, &.router-fade-leave-active
      transition all .6s ease
    &.router-fade-enter, &.router-fade-leave-to
      opacity 0
</style>