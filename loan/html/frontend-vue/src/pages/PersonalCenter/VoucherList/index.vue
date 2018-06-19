<template>
  <div class="page-voucher-list">
    <v-header @confirm="openPageRule"><span slot="title-text">代金券</span><span class="icon-btn-rule" slot="btn-confirm"><span class="icon-que-mark-1"></span></span></v-header>
    <transition name="fade">
      <voucher-rule class="page-voucher-rule" ref="voucherRule" v-show="vouhcerRuleShow" @close="closePageRule"></voucher-rule>
    </transition>
    <div class="have-vouchers" v-if="haveVouchers">
      <scroller class="scroller-container" 
        height="-55" lock-x scrollbar-y use-pullup
        @on-pullup-loading="loadMore"
        v-model="status" ref="scroller">
        <div class="voucher-list">
          <voucher class="voucher-item" v-for="(voucher, index) in vouchers" :voucherInfo="voucher" :key="`voucher_${index}`"></voucher>
        </div>
        <div class="pullup-container" slot="pullup">
          <div class="unloaded" v-show="hasMore">
            <span v-show="status.pullupStatus === 'default'">上拉加载数据</span>
            <span class="pullup-arrow" v-show="status.pullupStatus === 'down' || status.pullupStatus === 'up'" :class="{'rotate': status.pullupStatus === 'up'}">↑</span>
            <!-- <span v-show="status.pullupStatus === 'loading'">
              <spinner type="ios-small"></spinner>
            </span> -->
          </div>
          <div class="loaded" v-show="!hasMore && vouchers.length >= 6">
            <span>已经到底了</span>
          </div>
        </div>
      </scroller>
    </div>
    <div class="not-have-vouchers" v-else>
      <div class="img-wrap">
        <img src="../../../assets/imgs/img_no_data.png" width="200" height="200" alt="">
      </div>
      <p class="no-voucher-tip">暂无代金券</p>
    </div>
  </div>
</template>

<script>
import VHeader from '@/components/APageHeader/APageHeader'
import Voucher from '@/components/Voucher/Voucher'
import VoucherRule from './VoucherRule'

import { Scroller, Spinner } from 'vux'

import { createNamespacedHelpers } from 'vuex'
const namespace = 'personalCenter/voucher'
const { mapGetters, mapActions } = createNamespacedHelpers(namespace)

export default {
  name: 'pageVoucherList',
  data () {
    return {
      status: {
        pullupStatus: 'default'
      },
      loadedMore: false,
      vouhcerRuleShow: false
    }
  },
  created () {
    this.fetchVouchers()
  },
  mounted () {},
  computed: {
    ...mapGetters([
      'vouchers', 'hasMore'
    ]),
    haveVouchers () {
      return this.vouchers.length !== 0
    }
  },
  watch: {
    'vouchers' () {
      if (!this.loadedMore) return
      this.$nextTick(() => {
        this.$refs.scroller.donePullup()
      })
    }
  },
  methods: {
    ...mapActions([
      'fetchVouchers'
    ]),
    loadMore () {
      if (!this.hasMore) return
      this.loadedMore = true
      this.fetchVouchers()
    },
    openPageRule () {
      this.vouhcerRuleShow = true
      this.$refs.voucherRule.show()
    },
    closePageRule () {
      this.vouhcerRuleShow = false
    }
  },
  components: {
    VHeader,
    Voucher,
    VoucherRule,
    Scroller,
    Spinner
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus">
@import '../../../common/stylus/mixin.styl'
@import '../../../common/stylus/variable.styl'

.page-voucher-list
  .icon-btn-rule
    font-size 20px
    color $color-text-white
  .page-voucher-rule
    position fixed
    left 0
    top 0
    bottom 0
    width 100%
    background $color-white
    z-index 5
    overflow hidden
    &.fade-enter-active, &.fade-leave-active
      transition all .6s ease
    &.fade-enter, &.fade-leave-to
      opacity 0
  .have-vouchers
    .scroller-container
      .voucher-list
        padding-top 18px
        .voucher-item
          margin-bottom 15px
          &:last-child
            margin-bottom 0
      .pullup-container
        position absolute
        width 100%
        height 40px
        bottom -40px
        line-height 40px
        text-align center        
        font-size 18px
        color $color-text-grey-higher
        .pullup-arrow
          transition all linear .3s
          font-size 25px
          &.rotate
            display inline-block
            transform rotate(-180deg)
  .not-have-vouchers
    .img-wrap
      width 200px
      height 200px
      margin 80px auto 0
    .no-voucher-tip
      text-align center
      font-size 15px
      letter-spacing 1px
      color $color-text-grey-higher
</style>