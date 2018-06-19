<!--
@Date:   2018-01-03T17:13:59+08:00
@Last modified time: 2018-02-05T15:09:29+08:00
-->
<template lang="html">
  <div class="page-consume-shop">
    <c-header class="consume-shop-header" not-has-confirm>
      <span class="title" slot="title-text">合作商户</span>
    </c-header>
    <div class="consume-shop-content" :class="classMap">
      <div class="content-header">
        <swiper class="swiper" :list="banners" :loop="true" :auto="true"
          dots-position="center" dots-class="slider-dots" height="200px"></swiper>
        <div class="shop-title">
          <div class="logo-wrap">
            <img class="logo" :src="logo" width="64" height="64">
          </div>
          <div class="desc">
            <p class="name">{{ baseInfo.shop_name }}</p>
            <p class="title">{{ baseInfo.intro }}</p>
          </div>
        </div>
      </div>
      <div class="content-wrap">
        <tab class="cate-list">
          <tab-item v-for="(cate, index) in cates" :key="`cate_${index}`"
            :selected="index === 0" @on-item-click="selectCate(index)">{{ cate.title }}</tab-item>
        </tab>
        <div class="scroller" ref="scroller">
          <ul class="products" ref="scrollerContainer">
            <li class="product" v-for="(product, index) in products"
              :class="{'row-last': index % 2 !== 0}" @click="selectProduct(product.id)">
              <div class="img-wrap">
                <img :src="product.pic" width="170" height="120">
              </div>
              <div class="desc">
                <p class="name">{{ product.title }}</p>
                <p class="monthly">月供：￥{{ product.monthly }}起</p>
              </div>
            </li>
            <div class="btn-load-more" @click="loadMore">{{ hasMore ? '点击加载' : '已经到底了' }}</div>
          </ul>
        </div>
      </div>
    </div>
    <transition name="router-fade">
      <keep-alive><router-view class="products-container"></router-view></keep-alive>
    </transition>
  </div>
</template>

<script>
import CHeader from '@/components/APageHeader/APageHeader'
import { Group, Cell, Swiper, SwiperItem, Tab, TabItem } from 'vux'

import { mapState, createNamespacedHelpers } from 'vuex'
const namespace = 'container/home/shop'
const { mapGetters, mapActions } = createNamespacedHelpers(namespace)

import BScroll from 'better-scroll'

const SCROLLER_HEIGHT = 545
const SCROLLER_CONTAINER_ROW_HEIGHT = 180
const SCROLLER_PULLING_DOWN_THRESHOLD = 50

export default {
  data () {
    return {
      products: [],
      ScrollToTop: false
    }
  },
  async created () {
    await this.fetchShopProductCate()
    await this.fetchShopProducts()
    this.products = this.updateProducts()
  },
  destroyed () {
    this.$store.commit(`${namespace}/CLEAR_SHOP_DATA_FOR_CONSUME`)
  },
  mounted () {
    this._initScroll()
  },
  watch: {
    async 'currentCateId' () {
      // not fetch data again just use the loaded data
      if (!this.all.some(item => (item.cateId === this.currentCateId))) {
        await this.fetchShopProducts()
      }
      this.products = this.updateProducts()
      this.$nextTick(() => {
        this._initScroll()
      })
    },
    'products' () {
      let scroller = this.$refs.scroller
      if (this.products.length >= 6) {
        scroller.style.height = `${SCROLLER_HEIGHT}px`
      } else {
        let row = Math.ceil(this.products.length / 2)
        let rowsHeight = row * SCROLLER_CONTAINER_ROW_HEIGHT
        scroller.style.height = `${rowsHeight - SCROLLER_PULLING_DOWN_THRESHOLD - 1}px`
      }
    }
  },
  computed: {
    ...mapState({
      baseInfo: state => state.container.home.consume.shopBaseInfo
    }),
    ...mapGetters([
      'cates',
      'all',
      'currentCateId',
      'hasMore'
    ]),
    banners () {
      let bannerPics = this.baseInfo.shop_pic
      if (bannerPics.length > 0 && bannerPics[0]) {
        return bannerPics.map((item, index) => ({
          url: 'javascript:',
          img: item
        }))
      } else {
        return [{
          url: 'javascript:',
          img: require('@/assets/imgs/default-shop-banner.png')
        }]
      }
    },
    logo () {
      return this.baseInfo.logo ? this.baseInfo.logo : require('@/assets/imgs/default-shop-logo.jpg')
    },
    classMap () { // modify the className of the block 'content-header' dynamically
      return {
        'to-top': this.ScrollToTop
      }
    }
  },
  methods: {
    ...mapActions([
      'fetchShopProductCate',
      'fetchShopProducts'
    ]),
    _initScroll () {
      let scroller = this.$refs.scroller
      if (!this.scroll) {
        this.scroll = new BScroll(scroller, {
          click: true,
          probeType: 3, // dispatch 'scroll' event momently
          pullDownRefresh: { // enable pulling down refresh in order to listen the 'pullingDown' event
            threshold: 50,
            stop: 20
          }
        })
        this._bindScrollEvents()
      } else {
        // console.log(11, scroller.style.height)
        this.scroll.refresh()
      }
    },
    _bindScrollEvents () {
      if (!this.scroll) return
      this.scroll.on('scroll', ({x, y}) => {
        // If scrollTop is bigger than the first row height which
        // is 180 pixel here, just move the scroller to the top.
        // The value of the distance is negative indicate that
        // scroller is scrolled downwards.
        if (y <= -180 && !this.ScrollToTop) {
          this.ScrollToTop = true
        }
      })
      this.scroll.on('pullingDown', () => {
        // We move the scroller to the initial position
        this.ScrollToTop = false
        this.$nextTick(() => {
          // enable pulling down again
          this.scroll.finishPullDown()
        })
      })
    },
    selectCate (index) {
      let newCate = this.cates[index]
      if (Number(newCate.id) !== Number(this.currentCateId)) {
        this.$store.commit(`${namespace}/CHANGE_CURRENT_PRODUCT_CATE`, { newCate })
      }
    },
    updateProducts () {
      // definded cateId products
      const DCProductsEl = this.all.find(item => item.cateId === this.currentCateId)
      const DCProducts = DCProductsEl ? DCProductsEl.list : []
      const resolvedDCProducts = []
      DCProducts.forEach(item => {
        let resolveItem = Object.assign({}, item)
        if (!resolveItem.pic) {
          resolveItem.pic = require('@/assets/imgs/default-product-img.png')
        }
        resolvedDCProducts.push(resolveItem)
      })
      return resolvedDCProducts
    },
    async loadMore () {
      await this.fetchShopProducts()
      this.products = this.updateProducts()
    },
    selectProduct (productId) {
      this.$router.push(`/home/shop/product/${productId}`)
    }
  },
  components: {
    CHeader,
    Group,
    Cell,
    Swiper,
    SwiperItem,
    Tab,
    TabItem
  }
}
</script>

<style lang="stylus">
@import '../../../../../../common/stylus/mixin.styl'
@import '../../../../../../common/stylus/variable.styl'

.page-consume-shop
  .consume-shop-header
    z-index: 50
  .consume-shop-content
    transition: all .4s ease
    transform: translate3d(0, 0, 0)
    z-index: 1
    &.to-top
      @media (min-device-pixel-ratio: 2.0), (-webkit-min-device-pixel-ratio: 2.0)
        transform: translate3d(0, -280px, 0)
      @media (min-device-pixel-ratio: 3.0), (-webkit-min-device-pixel-ratio: 3.0)
        transform: translate3d(0, -260px, 0)
    .content-header
      .shop-title
        height: 80px
        font-size: 0
        .logo-wrap
          display: inline-block
          vertical-align: top
          width: 25%
          padding: 8px 20px 8px 16px
          .logo
            border-radius: 50% 50%
        .desc
          display: inline-block
          vertical-align: top
          width: 75%
          padding-right: 12px
          padding-top: 6px
          font-size: 0
          .name
            font-size: 18px
            font-weight: bold
            color: $color-text-black
          .title
            margin-top: 8px
            font-size: 14px
            color: $color-text-grey
    .content-wrap
      .scroller
        height: 545px
        overflow:hidden
        .products
          display: flex
          flex-flow: row wrap
          padding: 0 10px 0 10px
          .product
            flex: 0 0 50%
            width: 50%
            height: 180px
            padding: 10px 15px 0 0
            &.row-last
              padding-right: 0
            .img-wrap
            .desc
              margin-top: 5px
              .name
                width: 100%
                font-size: 16px
                color: #5b3b1b
                overflow: hidden
                text-overflow: ellipsis
                white-space: nowrap
              .monthly
                margin-top: 5px
                font-size: 14px
                color: $color-text-yellow
          .btn-load-more
            width: 100%
            height: 32px
            line-height: 32px
            text-align: center
            margin-top: 10px
            font-size: 16px
            color: $color-text-black
            border-1px($color-border-grey-light, 'top')
  .products-container
    position: fixed
    top: 0
    left: 0
    bottom: 0
    width: 100%
    overflow: hidden
    z-index: 99
    &.router-fade-enter-active, &.router-fade-leave-active
      transition: all .6s ease
    &.router-fade-enter, &.router-fade-leave-to
      opacity: 0
</style>
