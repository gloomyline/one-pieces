<template>
  <div class="que-and-ans">
    <group class="content" gutter="0">
      <cell class="title" :title="title">
        <span class="icon-question-mark" slot="icon"></span>
      </cell>
      <section v-if="list.length > 1" class="item-section" v-for="(item, index) in list">
        <h3 class="item-title">{{item.title}}</h3>
        <div class="item-content">
          <div class="item-content-wrap" v-if="item.content && item.content.length > 1">
            <p v-for="(child, index) in item.content" :key="`child_${index}`">
              <span class="label">{{child.label}}</span>
              <span class="value">{{child.value}}</span>
            </p>
          </div>
          <div class="item-content-wrap" v-else>{{item.content[0]}}</div>
        </div>
      </section>
      <section class="section" v-else>{{list[0]}}</section>
    </group>
  </div>
</template>

<script>
import { Group, Cell } from 'vux'

export default {
  name: 'queAndAns',
  data () {
    return {
    }
  },
  mounted () {},
  props: {
    title: {
      type: String,
      default: '忘记服务密码怎么办？'
    },
    list: {
      type: Array,
      default () {
        return [
          {
            title: '移动用户',
            content: [
              {
                label: '方法一：',
                value: '手机拨打10086，转“人工客服”'
              },
              {
                label: '方法二：',
                value: '手机登陆http://www.10086.cn选择登陆网上营业厅->忘记密码'
              }
            ]
          },
          {
            title: '联通用户',
            content: [
              {
                label: '方法一：',
                value: '手机拨打10010，转“人工客服”'
              },
              {
                label: '方法二：',
                value: '手机登陆http://wap.10010.cn选择我的->登录->忘记密码'
              }
            ]
          },
          {
            title: '电信用户',
            content: [
              {
                label: '方法一：',
                value: '手机拨打10000，转“人工客服”'
              },
              {
                label: '方法二：',
                value: '手机登陆http://wapzt.189.cn选择登录->忘记密码'
              }
            ]
          }
        ]
      }
    }
  },
  computed: {},
  methods: {},
  components: {
    Group,
    Cell
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus">
@import '../../common/stylus/mixin.styl'
@import '../../common/stylus/variable.styl'

.que-and-ans
  .title
    &.weui-cell
      height 40px !important
      border-bottom 1px solid $color-grey-higher1
      .weui-cell__hd
        margin-right 12px
        .icon-question-mark
          margin-top 4px
          inline-icon(15px, 15px)
          bg-img('./icon_question_mark')
      .vux-cell-bd
        font-size 15px
        font-weight 400
        color $color-text-blue
  .section
    height 60px
    padding 10px 20px 10px 40px
    font-size 14px
    color $color-text-black
  .item-section
    height 105px
    padding 10px 60px 10px 40px
    border-bottom 1px solid $color-grey-higher1
    .item-title
      font-size 14px
      color $color-text-black
    .item-content
      font-size 14px
      color $color-text-black
      p
        display flex
        font-size 0
        .label
          flex 0 0 56px
          font-size 14px
        .value
          flex 1
          font-size 14px
</style>