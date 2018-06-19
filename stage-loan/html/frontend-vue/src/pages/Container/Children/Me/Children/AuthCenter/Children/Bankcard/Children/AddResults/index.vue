<!--
@Date:   2018-01-02T14:30:29+08:00
@Last modified time: 2018-01-02T15:29:32+08:00
-->
<template>
  <div class="page-bankcard-add-results">
    <a-s-header not-go-back @go-back="goBack">
      <span slot="title-text">添加结果</span><span slot="btn-confirm"></span>
    </a-s-header>
    <div class="content">
      <div class="result-icon-wrap">
        <span class="icon-success icon" v-if="signResult"></span>
        <span class="icon-failed icon" v-else></span>
      </div>
      <p class="result-text">{{signResultText}}</p>
      <x-button class="btn-return-tip btn-blue" type="primary" action-type="button" @click.native="goBack">{{ btnText }}</x-button>
    </div>
  </div>
</template>

<script>
import ASHeader from '@/components/APageHeader/APageHeader'
import { XButton } from 'vux'

export default {
  name: 'pageBankcardAddResults',
  data () {
    return {
      signStatus: '',
      resultPopHideDelay: 3
    }
  },
  mounted () {
    this.signStatus = this.$route.query.status
    // this.autoReturn()
  },
  activated () {
    // this.autoReturn()
  },
  computed: {
    signResult () {
      return this.signStatus === '0000'
    },
    signResultText () {
      return this.signStatus === '0000' ? '恭喜，您的银行卡添加成功！' : '真可惜，您的银行卡添加失败！'
    },
    btnText () {
      return this.signResult ? '我知道了' : '重试一下'
    }
  },
  methods: {
    goBack () {
      this.$router.push('/authentication/bankcard')
    },
    autoReturn () {
      if (this.signStatus) {
        this.$vux.alert.show({
          content: `将在${this.resultPopHideDelay}s后返回银行卡列表页`,
          onHide: function () {
            this.timer && clearTimeout(this.timer)
            this.timer = null
            this.goBack()
          }.bind(this)
        })
        this.timer = setTimeout(() => {
          this.$vux.alert.hide()
          this.goBack()
        }, this.resultPopHideDelay * 1000)
      }
    }
  },
  components: {
    ASHeader,
    XButton
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus" scoped>
@import '../../../../../../../../../../common/stylus/mixin.styl'
@import '../../../../../../../../../../common/stylus/variable.styl'

.page-bankcard-add-results
  .content
    text-align center
    .result-icon-wrap
      padding-top 82px
      padding-bottom 32px
      .icon
        inline-icon(100px, 100px)
      .icon-success
        background-image url('./icon_add_success.png')
      .icon-failed
        background-image url('./icon_add_failed.png')
    .result-text
      font-size 18px
      font-weight 600
      color $color-text-black
    .btn-return-tip
      margin-top 32px
</style>
