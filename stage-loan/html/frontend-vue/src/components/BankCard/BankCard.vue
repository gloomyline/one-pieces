<template>
  <div class="bankcard">
    <div class="card" :class="classMapBox" @click="changeCard($event)">
      <header class="card-head">
        <div class="icon-bank-wrap">
          <span class="icon-bank" :class="classMapIcon"></span>
        </div>
        <div class="bank-name">{{bankName}}</div>
        <div class="card-holder">{{bankcardInfos.cardHolder}}</div>
        <div class="card-default" v-if="bankcardInfos.isDefault">(默认银行卡)</div>
      </header>
      <div class="content">
        <p class="card-number">{{bankcardInfos.cardNumber}}</p>
      </div>
      <footer class="card-foot" v-show="isListItem">
        <p class="tip">点击银行卡查看详情</p>
      </footer>
    </div>
  </div>
</template>

<script>
import bankData from '@/assets/datas/bankcard.json'

export default {
  name: 'bankcard',
  data () {
    return {
      banks: bankData
    }
  },
  created () {
  },
  mounted () {
  },
  props: {
    bankId: {
      type: Number,
      default: 0
    },
    bankcardInfos: {
      type: Object,
      default () {
        return {
          cardHolder: '王**',
          cardNumber: '6207 ****  **** 3182',
          isDefault: true
        }
      }
    },
    isListItem: {
      type: Boolean,
      default: true
    }
  },
  computed: {
    classMapBox () {
      return this.banks[this.bankId].boxName
    },
    classMapIcon () {
      return this.banks[this.bankId].iconName
    },
    bankName () {
      return this.banks[this.bankId].name
    }
  },
  methods: {
    changeCard (event) {
      this.$emit('change-card', event)
    }
  },
  components: {}
}
</script>

<style lang="stylus" ref="stylesheet/stylus">
@import '../../common/stylus/mixin.styl'
@import '../../common/stylus/variable.styl'

.bankcard
  .card
    width 345px
    height 115px
    margin 0 auto
    padding-top 12px
    color $color-text-white  
    background-size 100% 100%
    background-repeat no-repeat
    &.card-abc
      background-image url('./box_abc.png')
    &.card-boc
      background-image url('./box_boc.png')
    &.card-cb
      background-image url('./box_cb.png')
    &.card-ccb
      background-image url('./box_ccb.png')
    &.card-ceb
      background-image url('./box_ceb.png')
    &.card-cmbc
      background-image url('./box_cmbc.png')
    &.card-cmsb
      background-image url('./box_cmsb.png')
    &.card-gdb
      background-image url('./box_gdb.png')
    &.card-hxb
      background-image url('./box_hxb.png')
    &.card-icbc
      background-image url('./box_icbc.png')
    &.card-pab
      background-image url('./box_pab.png')
    &.card-psb
      background-image url('./box_psb.png')
    &.card-sgb
      background-image url('./box_sgb.png')
    .card-head
      line-height 35px
      font-size 0
      letter-spacing 1px
      .icon-bank-wrap
        display inline-block
        vertical-align top
        margin 0 14px 0 25px
        .icon-bank
          inline-icon(35px, 35px)
          &.icon-abc
            bg-img('./icon_abc')
          &.icon-boc
            bg-img('./icon_boc')
          &.icon-cb
            bg-img('./icon_cb')
          &.icon-ccb
            bg-img('./icon_ccb')
          &.icon-ceb
            bg-img('./icon_ceb')
          &.icon-cmbc
            bg-img('./icon_cmbc')
          &.icon-cmsb
            bg-img('./icon_cmsb')
          &.icon-gdb
            bg-img('./icon_gdb')
          &.icon-hxb
            bg-img('./icon_hxb')
          &.icon-icbc
            bg-img('./icon_icbc')
          &.icon-pab
            bg-img('./icon_pab')
          &.icon-psb
            bg-img('./icon_psb')
          &.icon-sgb
            bg-img('./icon_sgb')  
      .bank-name
        display inline-block
        vertical-align top
        margin-right 20px
        font-size 15px
      .card-holder
        display inline-block
        vertical-align top
        font-size 15px
      .card-default
        display inline-block
        vertical-align top
        font-size 15px
    .content
      line-height 36px
      text-align center
      font-size 20px
      letter-spacing 2px
    .card-foot
      padding 0 12px
      .tip
        text-align right
        letter-spacing 2px
</style>