<!--
@Date:   2017-12-14T17:05:37+08:00
@Last modified time: 2018-01-02T10:36:46+08:00
-->



<template>
  <div class="page-feedback">
    <f-header @confirm="submit" not-go-back @go-back="goBack">
      <span slot="title-text">问题反馈</span>
      <span slot="btn-confirm">提交</span>
    </f-header>
    <group class="question-type" title="请选择你要反馈的问题类型：">
      <checker class="type-box" :radio-required=true v-model="qType"  default-item-class="type-item" selected-item-class="actived">
        <checker-item v-for="(item, index) in types" :value="index" :key="`type_${index}`">{{item}}</checker-item>
      </checker>
    </group>
    <group class="question-content">
      <x-textarea class="question" v-model="qContent" placeholder="您好，请描述您的问题？" :max="150" :height="135"></x-textarea>
    </group>
  </div>
</template>

<script>
import FHeader from '@/components/APageHeader/APageHeader'
import { Group, Checker, CheckerItem, XTextarea } from 'vux'

import { createNamespacedHelpers } from 'vuex'
const namespace = 'personalCenter/feedback'
const { mapGetters, mapActions } = createNamespacedHelpers(namespace)

export default {
  name: 'pageFeedback',
  data () {
    return {
      types: ['信用不足', '资料填写', '借款', '还款', '功能建议', '其他']
    }
  },
  mounted () {},
  computed: {
    ...mapGetters([
      'questionType', 'content', 'resMsg'
    ]),
    qType: {
      get () {
        return this.questionType
      },
      set (num) {
        this.$store.commit(`${namespace}/UPDATE_QUESTION_TYPE`, { questionType: num })
      }
    },
    qContent: {
      get () {
        return this.content
      },
      set (str) {
        this.$store.commit(`${namespace}/UPDATE_QUESTION_CONTENT`, { content: str })
      }
    },
    localValidate () { // 本地校验问题反馈填写是否合法
      return this.questionType || (this.questionType === 0) && this.qContent.trim()
    }
  },
  watch: {
    'resMsg' () {
      this.alertShow(this.resMsg.replace(/(\d)+/g, ''), () => {
        this.reset()
      })
    }
  },
  methods: {
    ...mapActions([
      'feedback'
    ]),
    goBack () {
      this.$emit('close')
    },
    alertShow (msg, cb) {
      this.$vux.alert.show({
        content: msg,
        onHide: function () {
          if (this.timer) clearTimeout(this.timer)
          cb && cb()
        }.bind(this)
      })
      this.timer = setTimeout(() => {
        this.$vux.alert.hide()
        cb && cb()
      }, 3000)
    },
    reset () { // 重置表单单选和问题填写框
      this.qType = 0
      this.qContent = ''
      this.$router.go(-1)
    },
    submit () {
      if (!this.localValidate) {
        this.alertShow('请认真填写后再做提交')
        return
      }

      // send request to server submit
      this.feedback()
    }
  },
  components: {
    FHeader,
    Group,
    Checker,
    CheckerItem,
    XTextarea
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus">
@import '../../../../../../common/stylus/variable.styl'

.page-feedback
  position: fixed
  left: 0
  top: 0
  bottom: 0
  width: 100%
  background-color: $color-white-high
  .weui-cells__title
      height 32px
      line-height 32px
      padding-left 17px
      font-size 14px
  .question-type
    .weui-cells
      &:before
        display none
    .weui-cells__title
      color $color-text-black
    .weui-cells
      .type-box
        display flex
        flex-flow row wrap
        justify-content space-around
        width 100%
        height 95px
        padding-top 12px
        .type-item
          flex 0 0 100px
          width 100px
          height 30px
          line-height 30px
          text-align center
          font-size 14px
          color $color-text-yellow
          border 1px solid $color-yellow
          border-radius 15px
          &.actived
            color $color-text-white
            background-color $color-yellow
  .question-content
    .weui-cells
      &:before
        display none
    .weui-cells__title
      color $color-text-grey-higher
    .question
      height 175px !important
      .weui-textarea
        font-size 14px
        color $color-text-black
      .weui-textarea-counter
        font-size 14px
        color $color-text-black
</style>
