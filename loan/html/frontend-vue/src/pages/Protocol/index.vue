<template>
  <div class="page-protocol">
    <p-header @go-back="goBack" not-go-back><span slot="title-text">{{ protocolTitle }}</span><span slot="btn-confirm"></span></p-header>  
    <scroller class="scroller" lock-x scrollbar-y height="-55" ref="scroller">
      <div class="protocol-content">
        <div class="digest">
          <p class="digest-item" v-for="(digest, index) in digests" :key="'digest_' + index">{{digest}}</p>
        </div>
        <div class="details">
          <section class="detail-item" :class="'detail-' + index" v-for="(detail, index) in details" :key="'detail_' + index">
            <h3 class="detail-title">{{detail.title}}</h3>
            <div class="detail-content">
              <div class="deitail-content-item" v-for="(item, index) in detail.content">
                <div class="detail-has-children" v-if="item.title">
                  <p class="child-title">{{item.title}}</p>
                  <ul class="child-item" v-for="(child, index) in item.content">{{child}}</ul>
                </div>
                <p class="detail-not-has-children" v-else>{{item}}</p>
              </div>
            </div>
          </section>
        </div>
        <div class="sign">{{sign}}</div>
      </div>
    </scroller>      
  </div>
</template>

<script>
import PHeader from '@/components/APageHeader/APageHeader'
import { Scroller } from 'vux'

import protocol from '@/assets/datas/userRegisterAndService'
import loanContract from '@/assets/datas/loanContract'

export default {
  name: 'pageProtocol',
  data () {
    return {
      protocol,
      protocolTitle: '用户注册服务协议'
    }
  },
  props: {
    protocolType: {
      type: String,
      default: 'register'
    }
  },
  mounted () {
    if (this.protocolType !== 'register') {
      this.protocol = loanContract
      this.protocolTitle = '借款合同'
    }
  },
  computed: {
    digests () {
      return this.protocol.digest
    },
    details () {
      return this.protocol.details
    },
    sign () {
      return this.protocol.sign
    }
  },
  methods: {
    show () {
      this.$nextTick(() => {
        this.$refs.scroller.reset()
      })
    },
    // isArray (obj) { // adjust preferenced the Object could be tranversed or not
    //   return Array.isArray()
    // },
    goBack (event) {
      this.$emit('close', event)
    }
  },
  components: {
    PHeader,
    Scroller
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus" scoped>
.page-protocol
  font-size 14px
  .scroller
    .protocol-content
      padding 2em 1.2em 3em
      .digest
        font-size 1.2em
        font-weight 400
        .digest-item
          margin-bottom 1em
          line-height 1.6em
          text-indent 2em
      .details
        .detail-item
          margin-bottom 1em
          .detail-title
            margin-bottom .8em
            padding-left 1.8em
          .detail-content
            text-indent 2em
            line-height 1.6em
            .deitail-content-item
              margin-bottom 1em
      .sign
        font-size 1.6em
        font-weight bolder
</style>