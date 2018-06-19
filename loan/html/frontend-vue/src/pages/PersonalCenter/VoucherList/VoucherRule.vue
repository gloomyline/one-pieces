<template>
  <div>
    <r-header @go-back="goBack" not-go-back><span slot="title-text">代金券</span><span slot="btn-confirm"></span></r-header>
    <div class="scroller" ref="scroller">
      <div class="rule-container">
        <ul class="rule-list">
          <li class="rule-item" v-for="(item, index) in rules">
            <h3 class="title">{{item.title}}</h3>
            <ol class="content" v-if="item.content[0].title">
              <li class="child-item" v-for="(child, index) in item.content">
                <h4 class="child-title"><span class="index">({{index + 1}})</span>&nbsp;&nbsp;{{child.title}}</h4>
                <ol class="child-content">
                  <li class="child-content-item" v-for="(childItem, index) in child.content">{{childItem}}</li>
                </ol>
              </li>
            </ol>
            <ol class="content" v-else>
              <li class="item" v-for="(child, index) in item.content">{{child}}</li>
            </ol>
          </li>
        </ul>
        <div class="warm-tip">
          <p class="tip">温馨提示：如您对使用规则或服务过程中有任何疑问或需要帮助，请及时与我们的客服联系。</p>
          <p class="wechat">微信公众号：wukonghujin（悟空贷）</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import RHeader from '@/components/APageHeader/APageHeader'

import voucherRuleData from '@/assets/datas/voucherRule.json'

import BScroll from 'better-scroll'

export default {
  name: 'VoucherRule',
  data () {
    return {
      rules: voucherRuleData
    }
  },
  mounted () {},
  computed: {},
  methods: {
    _initScroll () {
      this.$nextTick(() => {
        if (!this.scroll) {
          this.scroll = new BScroll(this.$refs.scroller, {})
        } else {
          this.scroll.refresh()
        }
      })
    },
    goBack (event) {
      this.$emit('close', event)
    },
    show () {
      this._initScroll()
    }
  },
  components: {
    RHeader
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus" scoped> 
@import '../../../common/stylus/variable.styl'

.page-voucher-rule
  .scroller 
    position absolute
    left 0
    top 55px
    bottom 0
    width 100%
    color $color-text-black
    overflow hidden
    .rule-list
      padding 0 20px
      border-bottom 1px solid $color-grey-higher1
      .rule-item
        border-bottom 1px solid $color-grey-higher1
        .title
          line-height 40px
          font-size 15px
          color $color-text-yellow
        .content
          padding-left 18px
          padding-bottom 15px
          line-height 1.6em
          .item
            font-size 14px
            list-style decimal outside none
          .child-item
            padding 15px 0
            border-bottom 1px solid $color-grey-higher1
            &:last-child
              border none
            .child-title
              line-height 24px
              font-size 14px
              .index
                position relative
                top -2px
            .child-content
              padding-left 18px
              .child-content-item
                list-style decimal outside none
                font-size 14px
    .warm-tip
      padding 20px
      line-height 1.6em
      font-size 15px
      color $color-text-yellow
</style>