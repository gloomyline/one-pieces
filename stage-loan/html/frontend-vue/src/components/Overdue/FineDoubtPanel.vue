<template>
  <div class="fine-doubt-panel">
    <div class="fine-box">
      <p class="fine-equlation">当前逾期罚金=您当期应还本金 X 已逾期天数 X 费率{{ user.overdue_rate }}%</p>
      <x-button class="btn-close" type="primary" action-type="button" @click.native="close">我知道了</x-button>
    </div>
  </div>
</template>

<script>
import { XButton } from 'vux'

import { createNamespacedHelpers } from 'vuex'
const namespace = 'home'
const { mapGetters } = createNamespacedHelpers(namespace)

export default {
  name: 'fineDoubtPanel',
  data () {
    return {}
  },
  mounted () {},
  computed: {
    ...mapGetters(['user'])
  },
  methods: {
    close (event) {
      this.$emit('close', event)
    }
  },
  components: {
    XButton
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus" scoped>
@import '../../common/stylus/variable.styl'

.fine-doubt-panel
  width 100%
  height 100%
  background rgba(0, 0, 0, .7)
  .fine-box
    position absolute
    left 50%
    top 50%
    width 275px
    height 225px
    margin -112px 0 0 -137px
    background url('./bg_fine_doubt_panel.png')
    background-size 275px 225px
    background-repeat no-repeat
    .fine-equlation
      width 80%
      margin 120px auto 0
      text-align left
      font-size 13px
      font-weight 400
      color $color-text-black
    .btn-close
      width 200px
      height 35px
      margin-top 24px
      font-size 13px
      color $color-text-white
      letter-spacing 2px
      background-color $color-red-lighter
      box-shadow 2px 4px 2px $color-red-shadow
      &:active
        background-color $color-red-shadow
</style>