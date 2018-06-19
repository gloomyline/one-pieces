<!--
@Date:   2017-12-18T13:30:38+08:00
@Last modified time: 2018-01-31T17:57:59+08:00
-->
<template lang="html">
  <div class="page-mall">
    <header class="mall-header">
      <popup-picker class="mall-picker" :data="areas" v-model="currentCity"
      show-name value-text-align="left">
        <i class="icon-location-mark" slot="title"></i>
      </popup-picker>
      <search class="mall-search" :auto-fixed="false" position="absolute" v-model="searchVal"
        @on-submit="search" @on-cancel="cleanSearch"></search>
    </header>
    <tab class="mall-tab" active-color="#f39800" default-color="#282828" bar-active-color="#f39800" :line-width="2" custom-bar-width="40px">
      <tab-item class="cate-all" selected @on-item-click="chooseShopCate(0)">全部</tab-item>
      <tab-item class="shop-cate" v-for="(cate, index) in cates" :key="`cate_tab_${index}`"
        @on-item-click="chooseShopCate(cate.id)">{{ cate.title }}</tab-item>
    </tab>
    <scroller class="mall-scroller" height="-172" lock-x scrollbar-y ref="scroller">
      <div class="scroller-container">
        <ul class="shop-list" v-if="shops && shops.length > 0">
          <li class="shop-item" v-for="(shop, index) in shops">
            <router-link class="shop-link" :to="link(shop.id)">
              <div class="img-wrap">
                <img :src="shop.logo" width="64" height="64" v-if="shop.logo">
                <img :src="shopDefaultLogo" width="64" heig ht="64" v-else>
              </div>
              <div class="desc">
                <p class="name">{{ shop.shop_name }}</p>
                <p class="address">{{ shop.shop_addr }}</p>
              </div>
            </router-link>
          </li>
        </ul>
        <div class="no-data-img-wrap" v-else>
          <img :src="noDataImg" width="200" height="200">
          <p class="no-data-desc">暂无商家！</p>
        </div>
        <div class="btn-load-more" @click="loadMore" v-if="shops.length >= rowCount">{{ btnLoadMoreLabel }}</div>
      </div>
    </scroller>
    <transition name="shop-fade">
      <keep-alive>
        <router-view class="shop-container"></router-view>
      </keep-alive>
    </transition>
  </div>
</template>

<script>
import { PopupPicker, Search, Tab, TabItem, Scroller } from 'vux'

import { createNamespacedHelpers } from 'vuex'
const namespace = 'container/mall'
const { mapGetters, mapActions } = createNamespacedHelpers(namespace)

import shopAreas from '@/assets/static-configs/shopAreas_config'
import cates from '@/assets/static-configs/category_config'

// how many rows shall display in per page
const rowPerPage = 5

export default {
  data () {
    return {
      selectedCateId: 0,
      cates,
      areas: [],
      rowCount: rowPerPage,
      shopDefaultLogo: require('@/../static/imgs/default_shop_avatar.png'),
      noDataImg: require('@/assets/imgs/default-list-data-img.png')
    }
  },
  created () {
    this._initData()
  },
  watch: {
    'cateId' () {
      this.fetchShopsByChanging()
      this._resetScroll()
    },
    'cityId' () {
      this.fetchShopsByChanging()
      this._resetScroll()
    }
  },
  computed: {
    ...mapGetters([
      'shops',
      'cateId',
      'cityId',
      'shopName',
      'limit',
      'hasMore'
    ]),
    currentCity: {
      get () {
        return [this.cityId]
      },
      set (cityArr) {
        this.$store.commit(`${namespace}/UPDATE_FOR_SHOPS_IN_MALL`, {
          key: 'selectedCityId',
          val: cityArr.join('')
        })
      }
    },
    searchVal: {
      get () {
        return this.shopName
      },
      set (val) {
        this.$store.commit(`${namespace}/UPDATE_FOR_SHOPS_IN_MALL`, {
          key: 'shopName',
          val
        })
      }
    },
    btnLoadMoreLabel () {
      return this.hasMore ? '点击加载更多' : '已经到底了'
    }
  },
  methods: {
    ...mapActions([
      'fetchShops',
      'fetchShopsByChanging',
      'fetchShopsByLoadingMore'
    ]),
    _initData () {
      // default all cate shops from server
      this.fetchShops({
        category_id: '',
        city_id: '',
        // shop_name: '', do not prefer this field to avoid searching
        offset: 0,
        limit: 5
      })
      // format shop areas
      this.areas.push(shopAreas.map((area, index) => ({
        name: area.name,
        value: String(area.id)
      })))
    },
    _resetScroll () {
      this.$nextTick(() => {
        this.$refs.scroller.reset({ top: 0 })
      })
    },
    chooseShopCate (cateId) {
      this.$store.commit(`${namespace}/UPDATE_FOR_SHOPS_IN_MALL`, {
        key: 'selectedCateId',
        val: cateId * 1
      })
    },
    loadMore () {
      this.fetchShopsByLoadingMore()
      this._resetScroll()
    },
    link (shopId) {
      return `/mall/shop/${shopId}`
    },
    locate () {
      console.log('start to locate')
    },
    search () {
      this.fetchShopsByChanging()
      this._resetScroll()
    },
    cleanSearch () {
      this.$store.commit(`${namespace}/CLEAR_SEARCH`)
      this.fetchShopsByChanging()
      this._resetScroll()
    }
  },
  components: {
    PopupPicker,
    Search,
    Tab,
    TabItem,
    Scroller
  }
}
</script>

<style lang="stylus">
@import '../../../../common/stylus/mixin.styl'
@import '../../../../common/stylus/variable.styl'

.page-mall
  .mall-header
    font-size: 0
    border-1px($color-border-grey-light, 'bottom')
    .mall-picker
      display: inline-block
      vertical-align: top
      width: 25%
      font-size: 18px
      background-color: $color-white
      .weui-cell
        height: 70px !important
        padding-left: 4px
        padding-right: 0
        .weui-cell__hd
          .icon-location-mark
            font-size: 30px
            color: $color-text-yellow
        .vux-popup-picker-select
          padding-bottom: 6px
        .weui-cell__ft
          display: none
          &:after
            display: none
    .mall-search
      display: inline-block
      vertical-align: top
      width: 75%
      font-size: 12px
      z-index: 10
      .weui-search-bar
        height: 70px
        background-color: $color-white
        &:after
          display: none
        .weui-search-bar__form
          height: 40px
          margin-top: 7px
          border: none
          .weui-search-bar__box
            .weui-icon-search
              margin-top: 7px
            .weui-search-bar__input
              margin-top: 7px
        .weui-search-bar__label
          height: 40px
          padding-top: 8px
          border: .5px solid $color-border-grey-light
      .weui-search-bar__cancel-btn
        padding-top: 14px
        font-size: 16px
        color: $color-text-yellow
  .mall-tab
    .shop-cate
      font-size: 16px
  .mall-scroller
    .scroller-container
      padding-top: 16px
      background-color: $color-white-high
      .shop-list
        border-1px-tb()
        .shop-item
          width: 100%
          height: 80px
          border-1px($color-border-grey-light, 'bottom')
          &:after /* mv here to rewrite the pos of the border bottom line */
            width: 340px
            absolute: left 35px bottom
          .shop-link
            display: block
            height: 100%
            text-decoration: none
            .img-wrap
              display: inline-block
              vertical-align: top
              padding: 8px 12px 8px 8px
              img
                border-radius: 100% 100%
                border: 1px solid $color-border-grey-light
            .desc
              display: inline-block
              vertical-align: top
              .name
                margin-top: 12px
                font-size: 16px
                color: $color-text-black
              .address
                margin-top: 18px
                font-size: 14px
                color: $color-text-grey-higher
      .no-data-img-wrap
        text-align: center
        padding-top: 120px
      .btn-load-more
        height: 32px
        line-height: 32px
        text-align: center
        font-size: 16px
        color: $color-text-black
  .shop-container
    position: fixed
    left: 0
    top: 0
    bottom: 0
    width: 100%
    overflow: hidden
    background-color: $color-white-light
    z-index: 99
    &.shop-fade-enter-active, &.shop-fade-leave-active
      transition: all .6s ease
    &.shop-fade-enter, &.shop-fade-leave-to
      opacity: 0
</style>
