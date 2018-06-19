<!--
@Date:   2018-01-02T14:30:29+08:00
@Last modified time: 2018-01-02T15:19:21+08:00
-->
<template>
  <div class="page-bankcard">
    <b-header @go-back="goBack" @confirm="confirm" not-go-back>
      <span slot="title-text">银行卡管理</span>
      <div slot="btn-confirm">
        <span class="icon-add"></span>
        <span class="text">添加</span>
      </div>
    </b-header>
    <scroller class="bankcard-list-wrap" height="-55" lock-x>
      <div class="bankcard-list">
        <bankcard class="bankcard-item" v-for="(bankcard,index) in bankcards"
          :bankId="bankcard.bankId"
          :bankcardInfos="{ cardHolder: bankcard.bank_user, cardNumber: bankcard.bank_no, isDefault: bankcard.is_default }"
          :key="'card_' + index" @change-card="detail(bankcard.id)">
        </bankcard>
      </div>
    </scroller>
    <transition name="router-fade" mode="out-in">
      <!-- <keep-alive> -->
        <router-view class="bankcard-children"></router-view>
      <!-- </keep-alive> -->
    </transition>
  </div>
</template>

<script>
import BHeader from '@/components/APageHeader/APageHeader'
import Bankcard from '@/components/BankCard/BankCard'
import { Scroller } from 'vux'

import { createNamespacedHelpers } from 'vuex'
const namespace = 'authenticationCenter/bankcard'
const { mapGetters, mapActions } = createNamespacedHelpers(namespace)

export default {
  name: 'pageBankcard',
  data () {
    return {
    }
  },
  created () {
    this.fetchBankcards()
  },
  mounted () {
  },
  watch: {
  },
  computed: {
    ...mapGetters([
      'bankcards', 'bankcardsHasMore'
    ])
  },
  methods: {
    ...mapActions([
      'fetchBankcards'
    ]),
    goBack () { // goto page authentication
      this.$router.push('/me/auth')
    },
    confirm () { // goto page bankcard/add
      this.$router.push('/me/auth/bankcard/add')
    },
    detail (bankcardId) { // look over the detail of bankcard which index is preferenced
      this.$router.push(`/me/auth/bankcard/detail/${bankcardId}`)
    }
  },
  components: {
    BHeader,
    Bankcard,
    Scroller
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus" scoped>
@import '../../../../../../../../common/stylus/mixin.styl'
@import '../../../../../../../../common/stylus/variable.styl'

.page-bankcard
  .a-page-header
    .btn-confirm
      .icon-add
        margin 3px 2px 0 0
        inline-icon(15px, 15px)
        bg-img('./icon_add')
  .bankcard-list-wrap
    .bankcard-list
      padding 15px 0
      .bankcard-item
        margin-bottom 15px
        &:last-child
          margin-bottom 0
  .bankcard-children
    position fixed
    top 0
    left 0
    bottom 0
    width 100%
    background $color-white
    overflow hidden
    &.router-fade-enter-active, &.router-fade-leave-active
      transition all .6s ease
    &.router-fade-enter, &.router-fade-leave-to
      opacity 0
</style>
