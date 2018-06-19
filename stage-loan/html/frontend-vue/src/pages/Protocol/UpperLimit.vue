<template>
  <div class="page-upper-limit-protocol">
    <u-p-header @go-back="goBack" not-go-back>
      <span slot="title-text">提额授权协议</span>
      <span slot="btn-confirm"></span>
    </u-p-header>
    <scroller class="scroller" ref="scroller" lock-x scrollbar-y height="-55">
      <div class="protocol-content">
        <h3 class="title">{{protocol.title}}</h3>
        <div class="digest">{{protocol.digest}}</div>
        <div class="details">
          <section class="detail-item" v-for="(detail, index) in protocol.details" :key="'detail_' + index">
            <div class="detail-has-children" v-if="detail.title">
              <p class="child-title">{{detail.title}}</p>
              <ul class="child-list">
                <li class="child-item" v-for="(item, index) in detail.content" :key="'child_' + index">{{item}}</li>
              </ul>
            </div>
            <p class="details-has-not-children" v-else>{{detail}}</p>
          </section>
        </div>
        <div class="declaration">
          <h4 class="declaration-title">{{protocol.declaration.title}}</h4>
          <p class="declaration-content">{{protocol.declaration.content}}</p>
        </div>
        <div class="notice">
          <h4 class="notice-title">{{protocol.notice.title}}</h4>
          <p class="notice-content">{{protocol.notice.content}}</p>
        </div>
      </div>
    </scroller>
  </div>
</template>

<script>
import UPHeader from '@/components/APageHeader/APageHeader'
import { Scroller } from 'vux'

import protocol from '@/assets/datas/upperLimitProtocol'

export default {
  name: 'app',
  data () {
    return {
      protocol
    }
  },
  mounted () {},
  computed: {},
  methods: {
    goBack (event) {
      this.$emit('close', event)
    },
    show () {
      this.$nextTick(() => {
        this.$refs.scroller.reset()
      })
    }
  },
  components: {
    UPHeader,
    Scroller
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus" scoped>
@import '../../common/stylus/variable.styl'

.page-upper-limit-protocol
  font-size 14px
  color $color-black
  .protocol-content
    padding 1.8em 1.2em 0
    line-height 1.8em
    .title
      font-size 1.2em
      margin-bottom 1em
    .digest
      margin-bottom .8em
    .details
      // padding-left 2em
      padding 0 1em
      font-size .9em
      .detail-item
        margin-bottom .8em
        .child-list
          text-indent 0
          padding-left 1em
    .declaration
      // padding-left 2em
      padding 0 1em
      margin-bottom .8em
    .notice
      // padding-left 2em
      padding 0 1em
      margin-bottom .8em
</style>