<template>
  <div class="page-forget-pwd">
    <f-header>
      <span slot="title-text">忘记密码</span>
      <span slot="btn-confirm"></span>
    </f-header>
    <scroller class="scroller" ref="scroller" height="-55" lock-x>
      <div class="container">
        <que-and-ans class="section-item" v-for="(item, index) in descData" :key="`desc_${index}`" :title="item.title" :list="item.content"></que-and-ans>
      </div>
    </scroller>
  </div>
</template>

<script>
import FHeader from '@/components/APageHeader/APageHeader'
import { Scroller } from 'vux'
import QueAndAns from '@/components/QueAndAns/QueAndAns'

import ForgetServicePwdDesc from '@/assets/datas/ForgetServicePwdDesc.json'

export default {
  name: 'pageForgetPwd',
  data () {
    return {
      descData: ForgetServicePwdDesc
    }
  },
  mounted () {
  },
  activated () {
    let scroller = this.$refs.scroller
    if (!scroller._xscroll) return
    this.$refs.scroller.reset({top: 0})
  },
  computed: {},
  methods: {
  },
  components: {
    FHeader,
    Scroller,
    QueAndAns
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus">
@import '../../../../../common/stylus/variable.styl'

.page-forget-pwd
  position fixed
  top 0
  left 0
  bottom 0
  width 100%
  background $color-white
  .scroller
    .container
      .section-item
        margin-bottom 17px
        &:last-child
          margin-bottom 0
</style>