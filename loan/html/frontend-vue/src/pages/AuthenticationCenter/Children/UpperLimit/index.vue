<template>
  <div class="page-upper-limit">
    <header class="upper-limit-header">
      <div class="title-wrap">
        <h2 class="title">提升额度</h2>
        <div class="icon-wrap" @click="goBack">
          <span class="icon-return"></span>
        </div>
      </div>
      <div class="header-content">
        <p class="text">当前可用额度：</p>
        <p class="value"><span class="text-bold">{{ limitCount }}</span>元</p>
      </div>
      <p class="bottom-tip">额度提升最高可提升至5000元</p>
    </header>
    <div class="upper-limit-content">
      <div class="scroller" ref="scroller">
        <div class="scroller-wrap" ref="scrollerWrap">
        <section class="basic">
          <h3 class="title">基础认证</h3>
          <ul class="basic-list">
            <li class="basic-item diplomas" @click="goto($event)">
              <!-- <router-link to="/authentication/upperLimit/authen/sesame"> -->
                <div class="icon-wrap"><span class="icon icon-diplomas"></span></div>
                <p class="name-diplomas">学历认证</p>
              <!-- </router-link> -->
            </li>
            <li class="basic-item taobao" @click="goto($event)">
              <!-- <router-link to="/authentication/upperLimit/authen/taobao"> -->
                <div class="icon-wrap"><span class="icon icon-taobao"></span></div>
                <p class="name-taobao">淘宝认证</p>
              <!-- </router-link> -->
            </li>
          </ul>
        </section>
        <section class="more">
          <h3 class="title">更多认证<span class="color-text-tip">&nbsp;&nbsp;(至少完成2项)</span></h3>
          <ul class="more-list">
            <li class="more-item">
              <router-link to="/authentication/upperLimit/qq">
                <div class="icon-wrap"><span class="icon icon-qq"></span></div>
                <p class="name name-QQ">QQ认证</p>
              </router-link>
            </li>
            <li class="more-item jingdong" @click="goto($event)">
              <a><div class="icon-wrap"><span class="icon icon-jingdong"></span></div>
              <p class="name name-jingdong">京东认证</p></a>
            </li>
            <li class="more-item">
              <router-link to="/authentication/upperLimit/wechat">
                <div class="icon-wrap"><span class="icon icon-wechat"></span></div>
                <p class="name name-wechat">微信认证</p>
              </router-link>
            </li>
            <li class="more-item">
              <router-link to="/authentication/upperLimit/bankcard">
                <div class="icon-wrap"><span class="icon icon-credit"></span></div>
                <p class="name name-credit">常用信用卡</p>
              </router-link>
            </li>
            <li class="more-item">
              <router-link to="/authentication/upperLimit/creditbill">
                <div class="icon-wrap"><i class="icon icon-credit-bill"></i></div>
                <p class="name name-credit">信用卡账单</p>
              </router-link>
            </li>
            <li class="more-item">
              <router-link to="/authentication/upperLimit/netbank">
                <div class="icon-wrap"><i class="icon icon-net-bank"></i></div>
                <p class="name name-credit">网银流水</p>
              </router-link>
            </li>
          </ul>
        </section>
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
        <span class="icon-checker" :class="{'icon-check': protocolChecked, 'icon-not-check': !protocolChecked}" @click="checkProtocol"></span>
        <span class="text" @click="openPageProtocol">同意<span class="color-text-blue ">《提额授权协议》</span></span>
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
import PageProtocol from '@/pages/Protocol/UpperLimit'

import BScroll from 'better-scroll'

import { createNamespacedHelpers, mapState } from 'vuex'
const namespace = 'authenticationCenter/upLimit'
const { mapGetters, mapActions } = createNamespacedHelpers(namespace)

export default {
  name: 'pageUpperLimit',
  data () {
    return {
      limitCount: 0,
      protocolChecked: true,
      protocolShow: false,
      completeFlag: false
    }
  },
  mounted () {
    this.limitCount = !this.limit ? this.limitCount : this.limit
    // this._initScroll()
  },
  computed: {
    ...mapState({
      limit: state => state.home.user.quota
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
      this.$refs.scroller.style.height = '280px'
      this.$refs.scrollerWrap.style.height = '440px'
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
    PageProtocol
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus">
@import '../../../../common/stylus/variable.styl'
@import '../../../../common/stylus/mixin.styl'

.page-upper-limit
  .upper-limit-header
    position relative
    width 100%
    height 185px
    color $color-white
    background url('./bg_header.png')
    background-size 100% 100%
    background-repeat no-repeat
    .title-wrap
      position relative
      height 55px
      text-align center
      h2
        line-height 55px
        font-size 24px  
        letter-spacing 2px
      .icon-wrap
        position absolute
        left 14px
        top 13px
        padding 4px
        .icon-return
          font-size 20px
    .header-content
      padding 8px 12px 28px 12px
      .text
        font-size 15px
      .value
        text-align center
        line-height 56px
        .text-bold
          font-size 50px
          font-weight 400
    .bottom-tip
      position absolute
      bottom 0
      left 0
      width 100%
      height 32px
      text-align center
      line-height 32px
      font-size 14px
      background-color rgba(255, 255, 255, .3)
  .upper-limit-content
    .scroller
      overflow hidden
      .scroller-wrap
        background-color $color-white
        .title
          height 35px
          line-height 35px
          padding-left 12px
          font-size 14px
          color $color-text-black
          border-bottom 1px solid $color-grey-higher1
          .color-text-tip
            font-size 12px
            font-weight 200
            letter-spacing 1px
        .basic
          border-bottom 1px solid $color-grey-higher1
          .basic-list      
            display flex
            height 80px
            padding 12px 0 17px 0
            list-style none
            .basic-item
              flex 1
              text-align center
              border-right 1px solid $color-grey-higher1
              &.taobao
                .icon-wrap
                  .icon-taobao
                    font-size 30px
                    color $color-text-yellow
                .name-taobao
                  margin-top 10px
                  line-height 20px
                  font-size 15px
                  color $color-text-black
              &.diplomas
                .icon-wrap
                  .icon-diplomas
                    font-size 30px
                    color $color-text-blue
                .name-diplomas
                  margin-top 10px
                  line-height 20px
                  font-size 15px
                  color $color-text-black
        .more
          .more-list
            display flex
            flex-wrap wrap
            height 160px
            .more-item
              flex 0 0 33%
              width 33%
              text-align center
              padding 12px 0 8px 0
              border-bottom 1px solid $color-grey-higher1
              a
                display block
                text-decoration none
                border-right 1px solid $color-grey-higher1
                .icon-wrap
                  margin-bottom 10px
                  .icon
                    font-size 30px
                    &.icon-credit
                      color $color-text-blue
                    &.icon-jingdong
                      color $color-text-red
                    &.icon-wechat
                      color $color-text-green
                    &.icon-qq
                      color $color-text-blue-lightter
                    &.icon-net-bank
                      color $color-text-red
                    &.icon-credit-bill
                      color $color-text-green
                .name
                  line-height 20px
                  font-size 15px
                  color $color-text-black
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
    .protocol-agree
      height 35px
      line-height 35px
      margin-top 8px
      padding-left 20px
      font-size 0
      .icon-checker
        vertical-align middle
        margin 10px 6px 0 0 
        font-size 20px
        color $color-text-blue
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
      position fixed
      left 0
      bottom 0
      width 100%
      height 50px
      line-height 50px
      text-align center
      font-size 18px
      font-weight 200
      letter-spacing 1px
      color $color-white
      background $color-blue
      z-index 5
</style>