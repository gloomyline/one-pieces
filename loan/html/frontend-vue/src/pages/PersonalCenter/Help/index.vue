<template>
  <div class="page-help">
    <h-header><span slot="title-text">帮助中心</span><span slot="btn-confirm"></span></h-header>
    <div class="help-content">
      <tab class="help-tab" active-color="#2391fb" default-color="#777" bar-active-color="#2391fb" v-model="currentIndex">
        <tab-item class="help-tab-item" v-for="(item, index) in tabs" :key="`tab_${index}`" :selected="item === selected">{{item}}</tab-item>
      </tab>
      <swiper class="help-swiper" v-model="currentIndex" :show-dots=false height="830px">
        <swiper-item class="help-swiper-item lend">
          <div class="img-wrap">
            <img src="./img_help_lend.png" width="375" height="830" alt="">
          </div>
        </swiper-item>
        <swiper-item class="help-swiper-item upper">
          <div class="img-wrap">
            <img src="./img_help_upper.png" alt="" width="375" height="830">
          </div>
        </swiper-item>
        <swiper-item class="help-swiper-item refund">
          <div class="img-wrap">
            <img src="./img_help_refund1.png" alt="" width="375" height="830">
          </div>
        </swiper-item>
      </swiper>
    </div>
  </div>
</template>

<script>
import HHeader from '@/components/APageHeader/APageHeader'
import { Tab, TabItem, Swiper, SwiperItem } from 'vux'

export default {
  name: 'pageHelp',
  data () {
    return {
      currentIndex: 1,
      tabs: ['借款攻略', '如何提额', '还款指南'],
      selected: '如何提额'
    }
  },
  mounted () {},
  computed: {},
  methods: {},
  components: {
    HHeader,
    Tab,
    TabItem,
    Swiper,
    SwiperItem
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus" scoped>
</style>