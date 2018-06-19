<!--
@comment: page of product in shop belong to consume module
@Date:   2018-01-15T14:10:16+08:00
@Last modified time: 2018-02-01T17:02:20+08:00
-->
<template lang="html">
  <div class="page-product-detail">
    <p-header class="product-detail-header" @confirm="openReviewList">
      <span class="title" slot="title-text">商品详情</span>
      <span class="shop-cart" slot="btn-confirm"><i class="icon-shopping-cart"></i></span>
    </p-header>
    <transition name="cart-fade">
      <shop-cart ref="shopCart" class="page-shop-cart" @close="closeReviewList" v-show="isCartShow"></shop-cart>
    </transition>
    <transition name="photo-fade">
      <personal-photo @close="isPersonalPhotoShow = false" v-show="isPersonalPhotoShow"></personal-photo>
    </transition>
    <div class="product-scroller" ref="scroller">
      <div class="product-detail-content">
        <swiper class="product-swiper" :list="banners" :aspect-ratio="4/7" dots-position="center" auto loop></swiper>
        <group class="detail-desc">
          <p class="title" slot="title">{{ productDetail.title }}</p>
          <cell class="summary" :title="`金额：${ price }`"></cell>
          <cell class="period" title="借款期数" ref="periodScroller">
            <checker class="period-checker" ref="checker" type="radio" radio-required v-model="curPeriod"
              selected-item-class="selected-period">
              <checker-item ref="periodCheckerItem" class="period-checker-item" :key="index"
                v-for="(period, index) in productDetail.term" :value="index">{{ period }}</checker-item>
            </checker>
          </cell>
          <cell class="monthly" title="每月还款" :value="`￥${monthly}`"></cell>
          <cell class="total-refund" title="合计需还" :value="`￥${totalRefund}`"></cell>
          <x-number class="count" :title="`库存：${stock}`" :min="0" :max="stock" fillable v-model="productCount"></x-number>
          <popup-picker class="spec" title="规格" :data="specList" v-model="curSpec" show-name></popup-picker>
          <tab class="extra-desc" active-color="#f68000">
            <tab-item class="desc-item" selected @on-item-click="tabClickHandle(0)">产品介绍</tab-item>
            <tab-item class="desc-item" @on-item-click="tabClickHandle(1)">规格参数</tab-item>
            <tab-item class="desc-item" @on-item-click="tabClickHandle(2)">售后</tab-item>
          </tab>
          <div class="container-extra-desc">
            <div class="item-intro" v-html="productDetail.intro" v-show="isDescShow[0]"></div>
            <div class="item-spec" v-html="productDetail.spec" v-show="isDescShow[1]"></div>
            <div class="item-service" v-html="productDetail.service" v-show="isDescShow[2]"></div>
          </div>
        </group>
      </div>
    </div>
    <transition name="protocol-fade">
      <page-protocol class="page-protocol" ref="protocol" protocolType="contact" @close="isProtocolShow = false" v-show="isProtocolShow"></page-protocol>
    </transition>
    <footer class="product-footer">
      <div class="lend-contact">
        <span class="btn-check" @click="isContactChecked = !isContactChecked">
          <i class="icon-check" :class="checkClassMap"></i>
        </span>
        <span class="text">同意</span>
        <span class="btn-open-page-contact" @click="openContact">《借款协议》</span>
      </div>
      <div class="btns">
        <div class="btn-add" @click="add">加入分期</div>
        <div class="btn-submit" @click="submit">立即分期</div>
      </div>
    </footer>
  </div>
</template>

<script>
// components
import PHeader from '@/components/APageHeader/APageHeader'
import ShopCart from './Cart'
import PersonalPhoto from './PersonalPhoto'
import PageProtocol from '@/pages/Protocol'
import { Swiper, Group, Cell, Checker, CheckerItem, XNumber, PopupPicker, Tab, TabItem } from 'vux'

// helper funcs to access store easily
import { mapState, createNamespacedHelpers } from 'vuex'
const namespace = 'container/home/shop'
const { mapGetters, mapActions } = createNamespacedHelpers(namespace)

// scroll plugin
import BScroll from 'better-scroll'

export default {
  data () {
    return {
      isContactChecked: true,
      isCartShow: false,
      isProtocolShow: false,
      isDescShow: [true, false, false],
      isPersonalPhotoShow: false
    }
  },
  created () {
    this.$store.commit(`${namespace}/UPDATE_PRODUCT_ID`, {
      productId: this.$route.params.productId
    })
    this._initData()
  },
  mounted () {
    this._initScroll()
  },
  watch: {
    '$route.params.productId' (newId, oldId) {
      this.$store.commit(`${namespace}/UPDATE_PRODUCT_ID`, {
        productId: this.$route.params.productId
      })
      this._initData()
      this.$nextTick(() => {
        this._initScroll()
      })
    },
    'curPeriod' (newPeriod, oldPeriod) {
      this._fetchDataForAmount(newPeriod)
    },
    'amount' () {
      this._fetchDataForAmount(this.curPeriod)
    },
    'isPhotoConfirmed' () {
      if (!this.isCartShow) {
        this.submitConsume()
      }
    },
    'msg' () {
      if (/cart/.test(this.msg)) {
        this._alertShow(this.msg.replace(/\d+cart$/, ''))
      } else {
        this._alertShow(this.msg.replace(/\d+$/, ''), () => {
          this.isPersonalPhotoShow = false
          this.$router.push('/home/shop/submit-result')
        })
      }
    }
  },
  computed: {
    ...mapState({
      'isPhotoConfirmed': state => state.container.home.shop.personalPhoto.isPhotoConfirmed
    }),
    ...mapGetters([
      'productId',
      'productDetail',
      'currentPeriod',
      'currentSpec',
      'currentCount',
      'currentAmount',
      'cartProducts',
      'msg'
    ]),
    banners () {
      let banners = this.productDetail.banner
      if (banners.length > 0) {
        return banners.map(item => ({
          url: 'javascript:',
          img: item
        }))
      } else {
        return [{
          url: 'javascript:',
          img: require('@/assets/imgs/default-product-img.png')
        }]
      }
    },
    price () {
      let el = this.productDetail.sku.find(item => String(item.spec_id) === this.currentSpec)
      return el ? el.price : 0
    },
    amount () {
      return this.price * this.productCount
    },
    curPeriod: {
      get () {
        return this.currentPeriod
      },
      set (period) {
        this.$store.commit(`${namespace}/UPDATE_FOR_PRODUCT_IN_CONSUME`, {
          key: 'currentPeriod',
          val: period
        })
      }
    },
    monthly () {
      return this.currentAmount.monthly
    },
    totalRefund () {
      return this.currentAmount.total_amount
    },
    productCount: {
      get () {
        return this.currentCount
      },
      set (num) {
        this.$store.commit(`${namespace}/UPDATE_FOR_PRODUCT_IN_CONSUME`, {
          key: 'currentCount',
          val: num
        })
      }
    },
    stock () {
      let el = this.productDetail.sku.find(item => String(item.spec_id) === this.currentSpec)
      return el ? el.stock : 0
    },
    specList () { // List data of spec for product and the data is in the product detail
      return [this.productDetail.sku.map(item => ({
        value: String(item.spec_id), // id for product's spec
        name: item.spec // name for product's spec
      }))]
    },
    curSpec: {
      get () {
        return [String(this.currentSpec)]
      },
      set (specArr) {
        this.$store.commit(`${namespace}/UPDATE_FOR_PRODUCT_IN_CONSUME`, {
          key: 'currentSpec',
          val: specArr.join('')
        })
      }
    },
    extraDesc () {
      let pd = this.productDetail
      return [pd.intro, pd.spec, pd.service]
    },
    checkClassMap () {
      return this.isContactChecked ? 'icon-box-checked' : 'icon-box-unchecked'
    }
  },
  methods: {
    ...mapActions([
      'fetchShopProduct',
      'fetchAmount',
      'submitConsume'
    ]),
    async _initData () {
      // fetch current window size and add to data property
      this.windowAdaptRatio = this.utils.getViewPortSize().width / this.constants.WINDOW_SIZE.width
      await this.fetchShopProduct()
      // initial default spec of the good
      this.$store.commit(`${namespace}/UPDATE_FOR_PRODUCT_IN_CONSUME`, {
        key: 'currentSpec',
        val: this.productDetail.sku[0].spec_id
      })
      this._fetchDataForAmount(this.curPeriod)
      this.$nextTick(() => { // await fetching product details to get the terms
        this._initPeriodScroll()
      })
    },
    _initScroll () {
      if (!this.scroll) {
        this.scroll = new BScroll(this.$refs.scroller, {
          click: true
        })
      } else {
        this.scroll.refresh()
      }
    },
    _initPeriodScroll () {
      if (!this.periodScroll) {
        let checkItemWidth = 56 * this.windowAdaptRatio
        let margin = 18 * this.windowAdaptRatio
        this.$refs.checker.$el.style.width = `${(checkItemWidth + margin) * this.productDetail.term.length - margin}px`
        this.periodScroll = new BScroll(this.$refs.periodScroller.$el.querySelector('.weui-cell__ft'), {
          click: true,
          scrollX: true,
          eventPassthrough: 'vertical'
        })
      } else {
        this.periodScroll.refresh()
      }
    },
    _fetchDataForAmount (period) {
      // term value of times
      let term = this.productDetail.term[period] * 1
      // amount value of that order
      let amount = this.amount
      this.fetchAmount({ term, amount })
    },
    _localValidate () {
      if (this.stock <= 0) {
        this._alertShow('商品库存不足，请等待商户添加！')
        return false
      } else {
        if (this.productCount < 1) {
          this._alertShow('请选择商品数量后加入分期！')
          return false
        }
      }
      return true
    },
    tabClickHandle (_index) {
      this.isDescShow = this.isDescShow.map((item, index) => {
        return index === _index
      })
    },
    openReviewList () {
      if (this.cartProducts.length <= 0) {
        this._alertShow('请选择商品加入分期后查看购物车！')
        return
      }
      this.isCartShow = true
      this.$refs.shopCart.show()
    },
    closeReviewList () {
      this.isCartShow = false
    },
    openContact () {
      this.isProtocolShow = true
      this.$refs.protocol.show()
    },
    add () {
      if (!this._localValidate()) return
      let el = this.productDetail.sku.find(item => item.spec_id === this.curSpec)
      let specDesc = el ? el.spec : ''
      let product = {
        shop_product_id: this.productId,
        spec_id: this.currentSpec,
        quantity: this.productCount,
        name: this.productDetail.title,
        specDesc,
        price: this.price,
        stock: this.stock
      }
      this.$store.commit(`${namespace}/ADD_PRODUCT_TO_CART`, { product })
    },
    submit () {
      if (!this._localValidate()) return
      this.isPersonalPhotoShow = true
    }
  },
  components: {
    PHeader,
    ShopCart,
    PersonalPhoto,
    PageProtocol,
    Swiper,
    Group,
    Cell,
    Checker,
    CheckerItem,
    XNumber,
    PopupPicker,
    Tab,
    TabItem
  }
}
</script>

<style lang="stylus">
@import '../../../../../../common/stylus/mixin.styl'
@import '../../../../../../common/stylus/variable.styl'

.page-product-detail
  font-size: 16px
  color: $color-text-black
  background-color: $color-white-high
  .product-detail-header
    border-1px($color-border-grey-light, 'bottom')
    .shop-cart
      font-size: 24px
  .page-shop-cart
    position: fixed
    left: 0
    top: 0
    bottom: 0
    width: 100%
    background-color: $color-white-high
    z-index: 9998
    &.cart-fade-enter-active, &.cart-fade-leave-active
      transition: all .6s ease
    &.cart-fade-enter, &.cart-fade-leave-to
      opacity: 0
  .page-personal-photo
    position: fixed
    left: 0
    top: 0
    bottom: 0
    width: 100%
    background-color: $color-white-high
    z-index: 9999
    &.photo-fade-enter-active, &.photo-fade-leave-active
      transition: all .6s ease
    &.photo-fade-enter, &.photo-fade-leave-to
      opacity: 0
  .product-scroller
    position: fixed
    left: 0
    top: 55px
    bottom: 0
    width: 100%
    overflow: hidden
    .product-detail-content
      padding-bottom: 100px
      .detail-desc
        .title
          padding-top: 12px
          padding-left: 16px
          font-size: 14px
          color: $color-text-black
        .weui-cells
          margin-top: 0
          &:before
            display: none
          .weui-cell
            height: 45px !important
          .summary
            padding-top: 0
            color: $color-text-yellow
          .period
            .weui-cell__ft
              width: 260px
              overflow: hidden
            .period-checker
              display: flex
              .period-checker-item
                flex: 0 0 56px
                width: 56px
                height: 30px
                line-height: 30px
                text-align: center
                margin-right: 18px
                font-size: 16px
                color: $color-text-yellow
                border-radius: 8px 8px
                border: 1px solid $color-border-yellow
                &.selected-period
                  color: $color-text-white
                  font-weight: bold
                  background: linear-gradient(to right, $color-yellow, #f4bb7e)
          .count
            .weui-cell__ft
              div
                position: relative
                &:after
                  absolute: left -40px top 2px
                  content: '数量：'
                  font-size: 16px
                  color: $color-text-black
              input
                height: 24px
                padding: 0 0 2px
              .vux-number-selector
                height: 24px
                svg
                  fill: $color-text-yellow
                &.vux-number-disabled
                  svg
                    fill: #ccc
          .spec
            /* specification of product used PopupPicker component */
            .weui-cell
              .vux-popup-picker-select-box
                width: 240px
                .vux-popup-picker-select
                  overflow: hidden
                  text-overflow: ellipsis
                  white-space: nowrap
  .page-protocol
    position: fixed
    left: 0
    top: 0
    bottom: 0
    width: 100%
    background-color: $color-white-high
    z-index: 999
    &.protocol-fade-enter-active, &.protocol-fade-leave-active
      transition: all .6s ease
    &.protocol-fade-enter, &.protocol-fade-leave-to
      opacity: 0
  .product-footer
    fixed: left bottom
    width: 100%
    z-index: 99
    background-color: $color-white-high
    .lend-contact
      height: 36px
      font-size: 0
      .btn-check
        display: inline-block
        vertical-align: top
        padding: 8px
        font-size: 20px
        color: $color-text-yellow
      .text
        display: inline-block
        vertical-align: top
        margin-top: 7px
        font-size: 16px
        color: $color-text-black
      .btn-open-page-contact
        display: inline-block
        margin-top: 7px
        margin-left: .5em
        font-size: 16px
        color: $color-text-yellow
    .btns
      height: 60px
      font-size: 0
      border-1px($color-border-grey-light, 'top')
      .btn-add, .btn-submit
        display: inline-block
        width: 50%
        line-height: 60px
        text-align: center
        font-size: 18px
        color: $color-text-black
        background-color: $color-white-high
      .btn-submit
        color: $color-text-white
        background-color: $color-yellow
</style>
