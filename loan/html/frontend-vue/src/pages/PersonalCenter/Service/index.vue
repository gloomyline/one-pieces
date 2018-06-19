<template>
  <div class="page-service">
    <div class="container">
      <div class="box">
        <div class="service-phone">客服热线：010-55556666</div>
        <div class="btns">
          <div class="btn-call">
            <a href="tel:010-55556666">拨打</a>
          </div>
          <div class="btn-cancel">
            <a @click.prevent="cancel">取消</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'service',
  data () {
    return {}
  },
  mounted () {},
  computed: {},
  methods: {
    cancel (event) {
      this.$emit('close', event)
    }
  },
  components: {}
}
</script>

<style lang="stylus" ref="stylesheet/stylus">
@import '../../../common/stylus/variable.styl'

.page-service
  .container
    .box
      position absolute
      left 50%
      top 50%
      width 275px
      height 155px
      margin -80px 0 0 -140px
      border-radius 10px
      background $color-white
      text-align center
      .service-phone
        position relative
        height 110px
        line-height 110px
        font-size 16px  
        color $color-text-black
        &:after
          content ' '
          position absolute
          left 0
          bottom 0
          right 0
          height 1px
          border-bottom 1px solid $color-grey
      .btns
        display flex
        height 45px
        .btn-call
          flex 1
          a
            display block
            height 100%
            width 100%
            color $color-text-yellow
            &:active
              color-text-blue
        .btn-cancel
          flex 1
          color $color-text-black
          border-left 1px solid $color-grey          
</style>