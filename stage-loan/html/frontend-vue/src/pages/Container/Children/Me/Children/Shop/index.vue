<!--
@Date:   2018-01-03T09:53:36+08:00
@Last modified time: 2018-01-22T15:20:16+08:00
-->
<template lang="html">
  <div class="page-shop">
    <s-header class="shop-header" :not-has-confirm="true">
      <span class="title" slot="title-text">商家入驻</span>
    </s-header>
    <p class="shop-tip">若您希望加入我们分期商户平台，请留下您的信息，我们会第一时间联系您！</p>
    <group class="shop-content" gutter="0">
      <x-input class="shop-name" title="商户名称" placeholder="请输入商户名称" v-model.trim="name"></x-input>
      <x-input class="shop-relation" title="联系人" placeholder="请输入联系人姓名" v-model.trim="relation"></x-input>
      <x-input class="shop-telephone" title="联系电话" placeholder="请输入联系电话" v-model.trim="telephone"></x-input>
      <x-input class="shop-address" title="联系地址" placeholder="请输入联系地址" v-model.trim="address"></x-input>
    </group>
    <footer class="shop-footer">
      <x-button class="btn-join-us" type="primary" action-type="button" @click.native="submitShop">加入我们</x-button>
    </footer>
  </div>
</template>

<script>
import SHeader from '@/components/APageHeader/APageHeader'
import { Group, XInput, XButton } from 'vux'

import { createNamespacedHelpers } from 'vuex'
const namespace = 'container/me/shop'
const { mapGetters, mapActions } = createNamespacedHelpers(namespace)

export default {
  data () {
    return {}
  },
  created () {
  },
  watch: {
    'msg' () {
      this._alertShow(this.msg.replace(/\d+$/, ''), () => {
        this.$router.go(-1)
      })
    }
  },
  computed: {
    ...mapGetters([
      'shop',
      'msg'
    ]),
    name: {
      get () {
        return this.shop.name
      },
      set (val) {
        this.$store.commit(`${namespace}/UPDATE_FOR_SHOP`, { key: 'name', val })
      }
    },
    relation: {
      get () {
        return this.shop.relation
      },
      set (val) {
        this.$store.commit(`${namespace}/UPDATE_FOR_SHOP`, { key: 'relation', val })
      }
    },
    telephone: {
      get () {
        return this.shop.telephone
      },
      set (val) {
        this.$store.commit(`${namespace}/UPDATE_FOR_SHOP`, { key: 'telephone', val })
      }
    },
    address: {
      get () {
        return this.shop.address
      },
      set (val) {
        this.$store.commit(`${namespace}/UPDATE_FOR_SHOP`, { key: 'address', val })
      }
    }
  },
  methods: {
    ...mapActions([
      'fetchShop', 'submitShop'
    ])
  },
  components: {
    SHeader,
    Group,
    XInput,
    XButton
  }
}
</script>

<style lang="stylus">
@import '../../../../../../common/stylus/mixin.styl'
@import '../../../../../../common/stylus/variable.styl'

.page-shop
  .shop-header
    border-1px($color-border-grey-light, 'bottom')
  .shop-tip
    height: 70px
    line-height: 1.6em
    padding: 12px 24px 0 24px
    font-size: 14px
    color: $color-text-yellow
  .shop-content
    .weui-cells
      .weui-cell
        padding-left: 32px
        &.shop-relation
          .weui-cell__bd
            margin-left: 16px
  .shop-footer
    .btn-join-us
      width: 300px
      margin-top: 12px
</style>
