<!--
@Date:   2018-01-02T14:30:29+08:00
@Last modified time: 2018-02-05T15:42:01+08:00
-->
<template>
  <div class="page-upper-limit">
    <header class="upper-limit-header">
      <u-l-header class="header-title" not-has-confirm>
        <span class="title" slot="title-text">提升额度</span>
      </u-l-header>
      <div class="header-content">
        <p class="text">信用额度：</p>
        <p class="value"><span class="text-bold">{{ (limitCount * 1).toFixed(2) }}</span></p>
      </div>
      <p class="bottom-tip">最高可提升至50,000.00</p>
    </header>
    <div class="upper-limit-content">
      <div class="scroller" ref="scroller">
        <div class="scroller-wrap" ref="scrollerWrap">
          <divider class="basic-title">基础提额（必填）</divider>
          <group class="basic-content" gutter="0">
            <cell v-for="(cell, index) in CellData" v-if="!cell.type" :key="index"
              :class="cell.className" :title="cell.title" :link="cell.route" is-link></cell>
          </group>
          <divider class="more-title">其他提额（至少完成2项）</divider>
          <group class="more-content" gutter="0">
            <cell v-for="(cell, index) in CellData" v-if="cell.type" :key="index"
              :class="cell.className" :title="cell.title" :link="cell.route" is-link></cell>
          </group>
        </div>
      </div>
      <transition name="router-fade" mode="in-out">
        <!-- <keep-alive> -->
          <router-view class="upperlimit-authen-container"></router-view>
        <!-- </keep-alive> -->
      </transition>
    </div>
    <footer class="upper-limit-footer">
      <div class="protocol-agree">
        <span class="icon-checker" :class="{'icon-box-checked': protocolChecked, 'icon-box-unchecked': !protocolChecked}" @click="checkProtocol"></span>
        <span class="text" @click="openPageProtocol">同意<span class="color-text-yellow">《提额授权协议》</span></span>
        <transition name="protocol-fade">
          <div class="page-protocol-wrap" v-show="protocolShow">
            <page-protocol @close="closePageProtocol" ref="pageProtocol"></page-protocol>
          </div>
        </transition>
      </div>
      <div class="btn-fetch-limit" @click="fetchLimit">获取额度</div>
    </footer>
  </div>
</template>

<script>
import ULHeader from '@/components/APageHeader/APageHeader'
import PageProtocol from '@/pages/Protocol/UpperLimit'
import { Group, Cell, Divider } from 'vux'

import BScroll from 'better-scroll'

import { createNamespacedHelpers, mapState } from 'vuex'
const namespace = 'authenticationCenter/upLimit'
const { mapGetters, mapActions } = createNamespacedHelpers(namespace)

// auth for uplimit cell data
const CellData = [
  {
    className: 'credit-investigation',
    title: '央行征信',
    route: '/me/auth/upperLimit/credit',
    type: 0 // 0 prefer to basic
  },
  {
    className: 'fund',
    title: '公积金',
    route: '/me/auth/upperLimit/housefund',
    type: 0 // 0 prefer to basic
  },
  {
    className: 'taobao',
    title: '淘宝认证',
    route: '/me/auth/upperLimit/taobao',
    type: 0 // 0 prefer to basic
  },
  {
    className: 'credit-bill',
    title: '信用账单',
    route: '/me/auth/upperLimit/creditbill',
    type: 0 // 0 prefer to basic
  },
  {
    className: 'jingdong',
    title: '京东认证',
    route: '/me/auth/upperLimit/jingdong',
    type: 1 // perfer to more
  },
  {
    className: 'diplomas',
    title: '学历认证',
    route: '/me/auth/upperLimit/diplomas',
    type: 1 // perfer to more
  },
  {
    className: 'ebank',
    title: '网银流水',
    route: '/me/auth/upperLimit/netbank',
    type: 1 // perfer to more
  },
  {
    className: 'social-security',
    title: '社保',
    route: '/me/auth/upperLimit/socialsecurity',
    type: 1 // perfer to more
  }
]

export default {
  name: 'pageUpperLimit',
  data () {
    return {
      CellData,
      limitCount: 0,
      protocolChecked: true,
      protocolShow: false,
      completeFlag: false
    }
  },
  mounted () {
    this.limitCount = !this.limit ? this.limitCount : this.limit
    this._initScroll()
  },
  computed: {
    ...mapState({
      limit: state => state.user.quota.available_quota
    }),
    ...mapGetters([
      'resMsg'
    ])
  },
  watch: {
    'resMsg' () {
      this._alertShow(this.resMsg.replace(/(\d+)/g, ''))
    }
  },
  methods: {
    ...mapActions([
      'upLimit'
    ]),
    _initScroll () {
      this.$nextTick(() => {
        if (!this.scroll) {
          this.scroll = new BScroll(this.$refs.scroller, { click: true })
        } else {
          this.scroll.refresh()
        }
      })
    },
    goBack () {
      this.$router.go(-1)
    },
    confirm () {
    },
    checkProtocol () {
      this.protocolChecked = !this.protocolChecked
    },
    openPageProtocol () {
      this.protocolShow = true
      this.$refs.pageProtocol.show()
    },
    closePageProtocol () {
      this.protocolShow = false
    },
    fetchLimit () {
      if (!this.protocolChecked) {
        this._alertShow('请认真阅读完提额授权协议后然后勾选！')
        return
      }

      // send request to server
      this.upLimit()
    },
    goto (event) {
      // if (!event._constructed) return
      // event.target will get the touched element, but event.currentTarget will get the element binded event before
      let target = event.currentTarget
      let clsName = target.className

      if (/diplomas/.test(clsName)) {
        this.$router.push('/authentication/upperLimit/diplomas')
      } else if (/taobao/.test(clsName)) {
        this.$router.push('/authentication/upperLimit/taobao')
      } else if (/jingdong/.test(clsName)) {
        this.$router.push('/authentication/upperLimit/jingdong')
      }
    }
  },
  components: {
    ULHeader,
    PageProtocol,
    Divider,
    Group,
    Cell
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus">
@import '../../../../../../../../common/stylus/variable.styl'
@import '../../../../../../../../common/stylus/mixin.styl'

.page-upper-limit
  .upper-limit-header
    position relative
    width 100%
    height 265px
    color $color-white
    background url('../../../../../../../../assets/imgs/uplimit-header-bg.jpg')
    background-size 100% 100%
    background-repeat no-repeat
    .header-title
      border-1px($color-border-grey-light, 'bottom')
    .header-content
      padding 18px 0 0 20px
      font-size: 0
      .text
        font-size 18px
      .value
        margin-top: 12px
        text-align center
        .text-bold
          line-height: 72px
          font-size 70px
          font-weight 400
    .bottom-tip
      width 100%
      height 32px
      margin-top: 24px
      text-align: center
      line-height: 32px
      font-size: 18px
      background: linear-gradient(to right, rgba(243, 152, 0, .3), rgba(255, 255, 255, .3), rgba(243, 152, 0, .3))
  .upper-limit-content
    .scroller
      position: fixed
      left: 0
      top: 265px
      bottom: 85px
      width: 100%
      overflow hidden
      .scroller-wrap
        background-color $color-white
        .vux-divider
          font-size: 16px
          color: $color-text-grey-higher
        .weui-cells
          .weui-cell
            height: 50px !important
    .upperlimit-authen-container
      position fixed
      top 0
      left 0
      bottom 0
      width 100%
      background $color-white
      overflow hidden
      z-index 10
      &.router-fade-enter-active, &.router-fade-leave-active
        transition all .6s ease
      &.router-fade-enter, &.router-fade-leave-to
        opacity 0
  .upper-limit-footer
    position fixed
    left 0
    bottom 0
    width 100%
    height: 85px
    .protocol-agree
      height 35px
      line-height 35px
      margin-top 8px
      padding-left 20px
      font-size 0
      border-1px($color-border-grey-light, 'top')
      .icon-checker
        vertical-align middle
        margin 10px 6px 0 0
        font-size 20px
        color $color-text-yellow
      .text
        display inline-block
        vertical-align top
        font-size 14px
        color $color-text-black
      .page-protocol-wrap
        position fixed
        left 0
        top 0
        bottom 0
        width 100%
        background $color-white
        overflow hidden
        z-index 99
        &.protocol-fade-enter-active, &.protocol-fade-leave-active
          transition all .6s ease
        &.protocol-fade-enter, &.protocol-fade-leave-to
          opacity 0
    .btn-fetch-limit
      height 50px
      line-height 50px
      text-align center
      font-size 18px
      font-weight 200
      letter-spacing 1px
      color $color-white
      background $color-yellow
      z-index 5
</style>
