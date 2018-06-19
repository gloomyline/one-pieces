<!--
@Date:   2017-12-19T13:19:11+08:00
@Last modified time: 2018-02-02T09:44:55+08:00
-->
<template lang="html">
  <div class="page-cash">
    <c-header class="cash-header" not-has-confirm>
      <span class="title" slot="title-text">现金分期</span>
    </c-header>
    <div class="cash-content">
      <div class="bg-img-wrap"><img src="" alt="" :src="headerBg" width="100%" height="100%"></div>
      <h3 class="title">{{ title }}（元）</h3>
      <p class="available-quota">{{ quota }}</p>
      <div class="tip">按日计息，随借随还</div>
    </div>
    <footer class="cash-footer">
      <x-button class="btn-lend btn-yellow" type="primary" action-type="button"
        @click.native="lendOrGotoAuth">{{ isAuthed? '借款' : '去认证' }}</x-button>
    </footer>
    <transition name="cash-fade">
      <router-view class="cash-router-container"></router-view>
    </transition>
  </div>
</template>

<script>
import CHeader from '@/components/APageHeader/APageHeader'
import { XButton } from 'vux'

import { createNamespacedHelpers } from 'vuex'
const namespace = 'user'
const { mapGetters, mapActions } = createNamespacedHelpers(namespace)

export default {
  data () {
    return {
      headerBg: require('@/assets/imgs/cash-consume-bg.jpg')
    }
  },
  created () {
    // this.fetchQuotaAndAuth()
    this.fetchUserAuth()
  },
  mounted () {
  },
  destroyed () {
  },
  computed: {
    ...mapGetters([
      'isAuthed', 'availableQuota', 'maxQuota'
    ]),
    title () {
      return this.isAuthed ? '可用额度' : '最高可借'
    },
    quota () {
      return this.isAuthed ? this.availableQuota : this.maxQuota
    }
  },
  methods: {
    ...mapActions([
      'fetchUserAuth'
    ]),
    lendOrGotoAuth () {
      if (this.isAuthed) {
        this.$router.push('/home/cash/lend')
      } else {
        this.$router.push('/me/auth')
      }
    }
  },
  components: {
    CHeader,
    XButton
  }
}
</script>

<style lang="stylus">
@import '../../../../../../common/stylus/mixin.styl'
@import '../../../../../../common/stylus/variable.styl'

.page-cash
  .cash-header
    border-1px($color-border-grey-light, 'bottom')
  .cash-content
    width: 100%
    height: 210px
    position: relative
    color: $color-text-white
    .bg-img-wrap
      absolute: left top
      z-index: -1
    .title
      padding-left: 20px
      padding-top: 18px
      box-sizing: border-box
      font-size: 18px
    .available-quota
      margin-bottom: 12px
      text-align: center
      font-size: 70px
    .tip
      width: 100%
      height: 32px
      line-height: 32px
      text-align: center
      font-size: 18px
      background: linear-gradient(to right, $color-yellow, rgba(255, 255, 255, .33), $color-yellow)
  .cash-router-container
    position: fixed
    left: 0
    top: 0
    bottom: 0
    width: 100%
    overflow: hidden
    background-color: $color-white-high
    &.cash-fade-enter-active, &.cash-fade-leave-active
      transition: all .6s ease
    &.cash-fade-enter, &.cash-fade-leave-to
      opacity: 0
  .cash-footer
    margin-top: 20px
</style>
