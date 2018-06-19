<template>
  <div class="page-record">
    <r-header @go-back="goBack" not-go-back>
      <span slot="title-text">借还记录</span>
      <div slot="btn-confirm"></div>
    </r-header>
    <div class="record-content">
      <tab class="record-tab" v-model="currentIndex" :animate="false">
        <tab-item class="record-item"
        :class="{ 'actived': item === selected, 'lend': index === 0, 'refund': index === 1 }"
        :selected="item === selected"
        v-for="(item, index) in tabs" :key="`item_${index}`"
        @on-item-click="select(item)">{{item}}</tab-item>
      </tab>
      <transition class="router-fade">
        <!-- <keep-alive> -->
          <router-view class="record-container"></router-view>
        <!-- </keep-alive> -->
      </transition>
    </div>
  </div>
</template>

<script>
import RHeader from '@/components/APageHeader/APageHeader'
import { Tab, TabItem } from 'vux'
import Lend from '@/components/Record/Lend'
import Refund from '@/components/Record/Refund'

import { createNamespacedHelpers } from 'vuex'
const namespace = 'personalCenter/record'
const { mapGetters, mapActions } = createNamespacedHelpers(namespace)

export default {
  name: 'pageRecord',
  data () {
    return {
      currentIndex: 0,
      tabs: ['借款记录', '还款记录'],
      selected: '借款记录'
    }
  },
  mounted () {},
  computed: {
    ...mapGetters([
    ])
  },
  methods: {
    ...mapActions([
    ]),
    select (item) {
      this.selected = item
      if (item === this.tabs[0]) {
        this.$router.push('/personal/record/lend')
      } else {
        this.$router.push('/personal/record/refund')
      }
    },
    goBack () {
      this.$router.push('/')
    }
  },
  components: {
    RHeader,
    Tab,
    TabItem,
    Lend,
    Refund
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus">
@import '../../../common/stylus/variable.styl'

.page-record
  .record-content
    .record-tab
      width 70%
      margin 15px auto 10px
      padding-top: 30px
      border 2px solid $color-blue
      border-radius 20px
      .vux-tab-container
        height: 30px
        .vux-tab
          height: 100%
          border-radius: 18px
          .record-item
            line-height 32px
            font-size 15px
            color $color-text-blue
            border none
            &.actived
              color $color-text-white
              background-color $color-blue
            &.lend
              border-radius 18px 0 0 18px
            &.refund
              border-radius 0 18px 18px 0
</style>
