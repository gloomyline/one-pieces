<!--
@Date:   2018-01-02T14:30:29+08:00
@Last modified time: 2018-01-02T15:40:24+08:00
-->
<template>
  <div class="page-sesame">
    <p-s-header><span slot="title-text">芝麻信用授权</span><span slot="btn-confirm"></span></p-s-header>
    <div class="content" v-if="!isAuthSuccess">
      <div class="logo-wrap">
        <img :src="logo" alt="芝麻信用" width="110" height="110">
      </div>
      <scroller class="scroller" lock-x height="280px">
        <div class="desc">
          <h3 class="subhead">{{ desc.subhead }}</h3>
          <section class="desc-wrap">
            <ul class="basic-list list" v-if="desc.basic">
              <li class="basic-item item" v-for="(item, index) in desc.basic">{{ item }}</li>
            </ul>
            <h4 class="notice">{{ desc.notice }}</h4>
            <h4 class="subnotice">{{ desc.subnotice }}</h4>
            <ul class="other-list list" v-if="desc.other">
              <li class="other-item item" v-for="(item, index) in desc.other">{{ item }}</li>
            </ul>
          </section>
        </div>
      </scroller>
      <x-button class="btn-agree" type="primary" action-type="button" @click.native="agreeAuthen">同意授权</x-button>
    </div>
    <div class="content" v-else>
      <div class="img-wrap">
        <img :src="imgSuccess" alt="授权成功" width="170" height="170">
      </div>
    </div>
  </div>
</template>

<script>
import PSHeader from '@/components/APageHeader/APageHeader'
import { Scroller, XButton } from 'vux'

import descData from '@/assets/datas/sesameCreditDesc'

import { mapState } from 'vuex'
import { setSesameAuth, getSesameAuth } from '@/common/js/localStorage'

export default {
  name: 'pageSesame',
  data () {
    return {
      logo: require('./logo_sesame.png'),
      imgSuccess: require('./img_sesame_authen_success.png'),
      desc: descData,
      isAuthSuccess: false
    }
  },
  created () {
    let sesameAuth = getSesameAuth(this.user)
    this.isAuthSuccess = sesameAuth
  },
  mounted () {},
  computed: {
    ...mapState({
      user: state => state.home.user.mobile
    })
  },
  methods: {
    agreeAuthen () {
      this.$store.commit('SEND_HTTP_REQUEST')
      setTimeout(() => {
        this.$store.commit('RESPONSE_SUCCESS')
        this.$store.commit('authenticationCenter/UPDATE_AUTHENTICATION_CENTER_BY_NAME', {
          authenStatusName: 'sesame',
          status: 1
        })
        setSesameAuth(this.user)
        this.isAuthSuccess = true
      }, 800)
    }
  },
  components: {
    PSHeader,
    Scroller,
    XButton
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus">
@import '../../../../../../../../common/stylus/variable.styl'

.page-sesame
  .content
    .logo-wrap
      margin-top 24px
      text-align center
    .scroller
      margin-top 18px
      .desc
        color $color-text-black
        .subhead
          text-align center
          font-size 16px
          font-weight 700
        .desc-wrap
          margin-top 22px
          padding 0 40px 0 40px
          .list
            .item
              line-height 24px
              font-size 14px
          .notice, .subnotice
            line-height 28px
            font-size 14px
            font-weight 600
    .img-wrap
      margin-top 120px
      text-align center
  .btn-agree
    width 300px
    height 40px
    margin 32px auto 0 auto
    font-size 16px
    color $color-text-white
    border-radius 20px 20px
    border-color $color-text-green-higher
    background-color $color-text-green-higher
    &:active
      border-color $color-text-green-higher
      background-color $color-text-green-higher
</style>
