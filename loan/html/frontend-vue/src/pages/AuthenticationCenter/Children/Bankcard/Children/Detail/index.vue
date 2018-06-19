<template>
  <div class="page-bankcard-detail">
    <b-d-header><span slot="title-text">银行卡详情</span><span slot="btn-confirm"></span></b-d-header>
    <div class="content">
      <div class="bankcard-wrap">
        <bankcard class="bankcard" :bank-id="bankId" :bankcard-infos="bankcardInfos" :is-list-item="false"></bankcard>
      </div>
      <!-- <group class="operation-area" gutter="0" v-show="!bankcard.is_default"> -->
      <group class="operation-area" gutter="0">
        <x-switch class="set-default-bankcard" prevent-default
          title="设为默认银行卡" inline-desc="*还款日当天系统将优先从默认银行卡自动扣取"
          @on-click="setDefault" v-model="isDefault">
        </x-switch>
        <x-button class="btn-unbinding" :class="{'btn-blue': !isDefault}" type="primary" action-type="button" @click.native="unbindBankcard" v-show="!isDefault">解除绑定</x-button>
      </group>
    </div>
  </div>
</template>

<script>
import BDHeader from '@/components/APageHeader/APageHeader'
import Bankcard from '@/components/BankCard/BankCard'
import { Group, XSwitch, XButton } from 'vux'

import { createNamespacedHelpers } from 'vuex'
const namespace = 'authenticationCenter/bankcard'
const { mapGetters, mapActions } = createNamespacedHelpers(namespace)

export default {
  name: 'pageBankcardDetail',
  data () {
    return {
      bankcard: {}
    }
  },
  mounted () {
    this.findBankcard()
  },
  watch: {
    '$route' () {
      if (/\/detail\/\d+/.test(this.$route.path)) {
        this.findBankcard()
      }
    },
    'responseErrMsg' () {
      this._alertShow(this.responseErrMsg.replace(/(\d+)/g, ''))
    }
  },
  computed: {
    ...mapGetters([
      'bankcards', 'responseErrMsg'
    ]),
    bankId () {
      return this.bankcard.bankId
    },
    bankcardInfos () {
      return { cardHolder: this.bankcard.bank_user, cardNumber: this.bankcard.bank_no, isDefault: this.bankcard.is_default }
    },
    isDefault: {
      get () {
        return this.bankcard.is_default === 1
      },
      set () {}
    }
  },
  methods: {
    ...mapActions([
      'setDefaultBankcard'
    ]),
    findBankcard () {
      this.bankcard = this.bankcards.find((el) => {
        return el.id === +this.$route.params.id
      })
    },
    setDefault () {
      if (this.isDefault) {
        this._alertShow('此卡已是默认银行卡')
        return
      }
      this.setDefaultBankcard({id: this.bankcard.id})
    },
    unbindBankcard () {
      console.log(222)
    }
  },
  components: {
    BDHeader,
    Bankcard,
    Group,
    XSwitch,
    XButton
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus">
@import '../../../../../../common/stylus/variable.styl'

  .page-bankcard-detail
    .content
      .bankcard-wrap
        padding 15px 0
      .operation-area
        .weui-cells
          &:before, &:after
            display none
        .set-default-bankcard
          .weui-cell__bd
            .weui-label
              font-size 16px
              color $color-text-black
            .vux-label-desc
              font-size 12px
              color $color-text-grey
          .weui-cell__ft
            .weui-switch:checked
              background-color $color-blue
              border-color $color-blue
            .weui-switch:before
              background-color $color-grey
        .btn-unbinding
          margin-top 20px
</style>