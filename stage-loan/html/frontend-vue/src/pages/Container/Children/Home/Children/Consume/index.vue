<!--
@Date:   2017-12-19T13:19:05+08:00
@Last modified time: 2018-01-11T17:40:57+08:00
-->
<template lang="html">
  <div class="page-consume">
    <c-header class="consume-header" @confirm="search">
      <span class="title" slot="title-text">查找商家</span>
      <span class="btn-search" slot="btn-confirm">搜索</span>
    </c-header>
    <group class="consume-content" gutter="0">
      <x-input class="shop-num" title="商家商户号" placeholder="请输入商家编号" v-model="shopNo"></x-input>
      <cell class="shop-name" title="商户名称：" :value="shopBaseInfo.shop_name" ></cell>
    </group>
    <footer class="consume-footer">
      <x-button class="btn-goto-shop" :disabled="!isShopAvailable" type="primary"
        action-type="button" @click.native="gotoShop">进入商家</x-button>
    </footer>
  </div>
</template>

<script>
import CHeader from '@/components/APageHeader/APageHeader'
import { Group, XInput, Cell, XButton } from 'vux'

import { createNamespacedHelpers } from 'vuex'
const namespace = 'container/home/consume'
const { mapGetters, mapActions } = createNamespacedHelpers(namespace)

export default {
  data () {
    return {
    }
  },
  watch: {
    'errMsg' () {
      this._alertShow(this.errMsg.replace(/\d+$/, ''))
    }
  },
  computed: {
    ...mapGetters([
      'no', 'shopBaseInfo', 'isShopAvailable', 'errMsg'
    ]),
    shopNo: {
      get () {
        return this.no
      },
      set (no) {
        this.$store.commit(`${namespace}/UPDATE_SHOP_NO`, { no })
      }
    }
  },
  methods: {
    ...mapActions([
      'search'
    ]),
    gotoShop () {
      this.$router.push('/home/shop')
    }
  },
  components: {
    CHeader,
    Group,
    XInput,
    Cell,
    XButton
  }
}
</script>

<style lang="stylus">
@import '../../../../../../common/stylus/variable.styl'

.page-consume
  .consume-content
    .shop-name
      color: $color-text-yellow
      .vux-cell-bd
        flex: 0 0 92px
        width: 92px
        font-size: 18px
      .weui-cell__ft
        font-size: 18px
        color: $color-text-yellow
  .consume-footer
    .btn-goto-shop
      width: 300px
      margin-top: 18px
</style>
