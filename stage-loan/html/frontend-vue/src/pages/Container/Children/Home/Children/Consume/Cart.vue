<!--
@Date:   2018-01-16T18:37:51+08:00
@Last modified time: 2018-02-01T16:26:58+08:00
-->
<template lang="html">
  <div class="page-cart">
    <c-header class="cart-header" not-go-back not-has-confirm @go-back="goBack($event)">
      <span class="title" slot="title-text">合并分期</span>
    </c-header>
    <group class="cart-content" gutter="0">
      <x-number class="product" ref="xNumber" v-for="(product, index) in cartProducts" :key="index" :value="product.quantity" :min="0" :max="product.stock"
        :title="`${product.name}(${product.specDesc})`" align="right" fillable @on-change="changeHandle(index)"></x-number>
    </group>
    <footer class="cart-footer">
      <group class="period" gutter="0">
        <cell title="合并分期数：" ref="scroller">
          <checker class="period-checker" ref="checker" type="radio" radio-required v-model="curPeriod"
            selected-item-class="selected-period">
            <checker-item class="period-checker-item" :key="index"
              v-for="(period, index) in productDetail.term" :value="index">{{ period }}</checker-item>
          </checker>
        </cell>
      </group>
      <div class="cart-settle">
        <div class="total-amount">
          <span class="total-count">合计：{{ totalAmount }}</span></br>
          <span class="monthly">(月供：￥{{ monthly }})</span>
        </div>
        <div class="btn-times-submit" @click="submit">
          <span class="label">去分期</span>
          <span class="product-count">{{ cartProducts.length }}</span>
        </div>
      </div>
    </footer>
  </div>
</template>

<script>
import CHeader from '@/components/APageHeader/APageHeader'
import { Group, XNumber, Cell, Checker, CheckerItem } from 'vux'

import { createNamespacedHelpers, mapState } from 'vuex'
const namespace = 'container/home/shop'
const { mapGetters, mapActions } = createNamespacedHelpers(namespace)
import Api from '@/api'

import BScroll from 'better-scroll'

export default {
  data () {
    return {
      curPeriod: 0,
      totalAmount: 0,
      monthly: 0
    }
  },
  mounted () {
  },
  watch: {
    'curPeriod' () {
      this.fetchAmount()
    },
    'totalCount' () {
      this.fetchAmount()
    },
    'isPhotoConfirmed' () {
      if (!this.$parent.isCartShow) return
      let details = []
      this.cartProducts.forEach((item, index) => {
        let product = {}
        product.shop_product_id = item.shop_product_id
        product.quantity = item.quantity
        product.spec_id = item.spec_id
        details.push(product)
      })
      let payload = {
        shop_id: this.shop_id,
        period: this.productDetail.term[this.curPeriod] * 1,
        details
      }
      this.submitConsume(payload)
    }
  },
  computed: {
    ...mapState({
      shop_id: state => state.container.home.consume.shopBaseInfo.shop_id,
      isPhotoConfirmed: state => state.container.home.shop.personalPhoto.isPhotoConfirmed
    }),
    ...mapGetters([
      'cartProducts',
      'productDetail'
    ]),
    totalCount () {
      let total = 0
      this.cartProducts.forEach((item, index) => {
        total += item.quantity * item.price
      })
      return total
    }
  },
  methods: {
    ...mapActions([
      'submitConsume'
    ]),
    _initData () {
      this.fetchAmount()
    },
    async fetchAmount () {
      let res = await Api.fetchAmount({
        term: this.productDetail.term[this.curPeriod] * 1,
        amount: this.totalCount
      })
      this.totalAmount = res.results.total_amount
      this.monthly = res.results.monthly
    },
    show () {
      this._initData()
      let checkItemWidth = 56 * this.constants.WINDOW_ADAPT_RATIO.width
      let margin = 12 * this.constants.WINDOW_ADAPT_RATIO.width
      this.$refs.checker.$el.style.width = `${(checkItemWidth + margin) * this.productDetail.term.length - margin}px`
      this.$nextTick(() => {
        this._initScroll()
      })
    },
    _initScroll () {
      if (!this.$refs.scroller) return
      if (!this.scroll) {
        this.scroll = new BScroll(this.$refs.scroller.$el.querySelector('.weui-cell__ft'), {
          click: true,
          scrollX: true,
          eventPassthrough: 'vertical'
        })
      } else {
        this.scroll.refresh()
      }
    },
    goBack (event) {
      this.$emit('close', event)
    },
    changeHandle (index) {
      // fetch the indexing child x-number current value
      let count = this.$refs.xNumber[index].currentValue * 1
      this.$store.commit(`${namespace}/CHANGE_PRODUCT_COUNT_IN_SHOP_CART`, { index, count })
    },
    submit () {
      if (this.cartProducts.length <= 0) {
        this._alertShow('请先添加商品，然后去分期')
        return
      }
      this.$parent.isPersonalPhotoShow = true
    }
  },
  components: {
    CHeader,
    Group,
    XNumber,
    Cell,
    Checker,
    CheckerItem
  }
}
</script>

<style lang="stylus">
@import '../../../../../../common/stylus/mixin.styl'
@import '../../../../../../common/stylus/variable.styl'

.page-cart
  .cart-content
    .weui-cells
      .weui-cell
        p
          width: 200px
          overflow: hidden
          text-overflow: ellipsis
          white-space: nowrap
        .weui-cell__ft
          input
            height: 24px
            padding: 0 0 4px
          .vux-number-selector
            height: 24px
            svg
              fill: $color-text-yellow
  .cart-footer
    fixed: left bottom
    width: 100%
    .period
      .weui-cells
        .weui-cell
          height: 60px !important
          .weui-cell__ft
            width: 240px
            overflow: hidden
            .period-checker
              display: flex
              .period-checker-item
                flex: 0 0 56px
                width: 56px
                height: 30px
                line-height: 30px
                text-align: center
                margin-right: 12px
                font-size: 16px
                color: $color-text-yellow
                border-radius: 8px 8px
                border: 1px solid $color-border-yellow
                &.selected-period
                  color: $color-text-white
                  font-weight: bold
                  background: linear-gradient(to right, $color-yellow, #f4bb7e)
    .cart-settle
      display: flex
      height: 60px
      border-1px($color-border-grey-light, 'top')
      .total-amount
        flex: 1
        padding: 10px 18px 0 18px
        font-size: 16px
        color: $color-text-black
      .btn-times-submit
        flex: 0 0 90px
        width: 90px
        text-align: center
        font-size: 0
        background-color: $color-yellow
        .label
          display: inline-block
          vertical-align: middle
          line-height: 60px
          font-size: 18px
          color: $color-white-high
        .product-count
          display: inline-block
          vertical-align: middle
          width: 18px
          height: 18px
          margin-left: 8px
          font-size: 14px
          color: $color-text-yellow
          border-radius: 50% 50%
          background-color: $color-white-high
</style>
