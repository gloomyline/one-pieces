<!--
@Date:   2017-12-28T10:27:18+08:00
@Last modified time: 2018-02-01T10:58:36+08:00
-->
<template lang="html">
  <div class="page-shop">
    <s-header class="shop-header" :not-has-confirm="true">
      <span class="title" slot="title-text">合作商户</span>
    </s-header>
    <div class="shop-content">
      <swiper class="swiper" :list="banners" :loop="true" :auto="true"
        dots-position="center" dots-class="slider-dots" :aspect-ratio="200/375"></swiper>
      <div class="scroller" ref="scroller">
        <div class="scroller-container">
          <div class="shop-title">
            <div class="logo-wrap">
              <img :src="shop.logo" width="64" height="64" v-if="shop.logo">
              <img :src="defaulShopAvatar" width="64" height="64" v-else>
            </div>
            <div class="title">
              <p class="shop-name">{{ shop.shop_name }}</p>
              <p class="shop-slogan">{{ shop.intro }}</p>
            </div>
          </div>
          <div class="shop-desc">
            <p class="address">
              <span class="icon-wrap"><i class="icon-shop-address"></i></span>
              <span class="text">{{ shop.shop_addr }}</span>
            </p>
            <p class="telephone">
              <span class="icon-wrap"><i class="icon-shop-tel"></i></span>
              <a class="text" :href="`tel:${shop.corporate_contacts_mobile}`">{{ shop.corporate_contacts_mobile }}</a>
            </p>
            <p class="service-title">
              <span class="before-block"
                style="display:inline-block;width:4px;height:12px;background-color:#f39800"></span>
              <span class="text">如何享受服务？</span>
            </p>
            <p class="service-content">根据上述地址、电话，到店咨询商家，于消费分期中输入商家提供的编码进行分期，审核通过即可享受服务。</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import SHeader from '@/components/APageHeader/APageHeader'
import { Swiper, SwiperItem } from 'vux'

import { createNamespacedHelpers } from 'vuex'
const namespace = 'container/mall'
const { mapGetters, mapActions } = createNamespacedHelpers(namespace)

import BScroll from 'better-scroll'

export default {
  data () {
    return {
      shopId: '',
      defaulShopAvatar: require('@/../static/imgs/default_shop_avatar.png')
    }
  },
  created () {
    // this.shopId = this.$route.params.shopId * 1
    // this._initData()
  },
  mounted () {
    // this._initScroll()
  },
  activated () {
    this.shopId = this.$route.params.shopId * 1
    this._initData()
    this._initScroll()
  },
  computed: {
    ...mapGetters([
      'shop'
    ]),
    banners () {
      return this.shop.banner.map((item, index) => ({
        url: 'javascript:',
        img: item,
        title: `图片${index + 1}`
      }))
    }
  },
  methods: {
    ...mapActions([
      'fetchShopDetail'
    ]),
    _initData () {
      this.fetchShopDetail({ shop_id: this.shopId })
    },
    _initScroll () {
      this.$nextTick(() => {
        if (!this.scroll) {
          this.scroll = new BScroll(this.$refs.scroller, {
            click: true
          })
        } else {
          this.scroll.refresh()
        }
      })
    }
  },
  components: {
    SHeader,
    Swiper,
    SwiperItem
  }
}
</script>

<style lang="stylus">
@import '../../../../common/stylus/mixin.styl'
@import '../../../../common/stylus/variable.styl'

.page-shop
  .shop-header
  .shop-content
    .swiper
      width: 100%
      height: 200px
      &.vux-slider
        .vux-swiper
          .vux-swiper-item
            a
              .vux-swiper-desc
                height: auto !important
                color: $color-text-yellow
        .slider-dots
          .vux-icon-dot
            width: 8px
            height: 8px
            border-radius: 50% 50%
            &.active
              background-color: $color-yellow
    .scroller
      height: 295px
      overflow: hidden
      .shop-title
        height: 80px
        font-size: 0
        background-color: $color-white
        .logo-wrap
          display: inline-block
          width: 90px
          vertical-align: top
          padding: 8px 20px 0 16px
          img
            border-radius: 50% 50%
        .title
          display: inline-block
          vertical-align: top
          width: 285px
          padding-right: 12px
          font-size: 18px
          .shop-name
            margin-top: 8px
            font-weight: bolder
          .shop-slogan
            margin-top: 6px
            font-size: 14px
            color: $color-text-grey-higher
      .shop-desc
        margin-top: 16px
        .address, .telephone
          height: 50px
          line-height: 50px
          font-size: 0
          font-weight: bold
          background-color: $color-white
          .icon-wrap
            display: inline-block
            vertical-align: top
            line-height: 50px
            margin-left: 16px
            margin-right: 16px
            font-size: 16px
            color: $color-text-yellow
          .text
            display: inline-block
            vertical-align: top
            font-size: 16px
            color: $color-text-black
        .address
          border-1px($color-border-grey-light, 'bottom')
        .service-title
          height: 35px
          line-height: 35px
          font-size: 0
          border-1px($color-border-grey-light, 'bottom')
          .before-block
            margin: 0 12px 0 18px
          .text
            font-size: 16px
            color: $color-text-yellow
        .service-content
          padding: 12px 24px
          text-indent: 2em
          font-size: 14px
          color: $color-text-black
</style>
