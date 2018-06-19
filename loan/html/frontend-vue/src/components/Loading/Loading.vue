<template>
  <div class="loading">
    <div class="loading-box">
      <div class="loading-circle-outside-wrap" ref="loadingOutside">
        <img src="./icon_loading_circle_outside.png" alt="">
      </div>
      <div class="loading-circle-inside-wrap">
        <img src="./icon_loading_circle_inside.png" alt="">
      </div>
    </div>
    <div class="loading-content">
      <p class="loading-percent">{{loadingPercent}}<span class="per-cent">%</span></p>
    </div>
    <p class="desc-text" v-show="isInquery">查询中</p>
  </div>
</template>

<script>
import TWEEN from '@tweenjs/tween.js'

export default {
  name: 'loading',
  data () {
    return {
      loadingPercent: 0,
      isInquery: false
    }
  },
  props: {
    processTime: {
      type: Number,
      default: 300
    }
  },
  mounted () {
  },
  computed: {},
  methods: {
    _playTweenCircle () {
      let elLoadingOutside = this.$refs.loadingOutside
      this.tweenCircle = new TWEEN.Tween({rotateDeg: 0})
                            .to({rotateDeg: 360}, 2000)
                            .easing(TWEEN.Easing.Linear.None)
                            .onUpdate(function () {
                              elLoadingOutside.style.setProperty('transform', `rotate(${this.rotateDeg}deg)`)
                            })
                            .repeat(Infinity)
                            .start()
      this._optimizeAnimation()
    },
    _playTweenNumber () {
      let tweenNum = {number: 0}
      this.tweenNumber = new TWEEN.Tween(tweenNum)
                                  .to({number: 99}, 1000 * this.processTime)
                                  .easing(TWEEN.Easing.Linear.None)
                                  .onUpdate(() => {
                                    this.loadingPercent = Math.floor(tweenNum.number)
                                  })
                                  .start()
    },
    _optimizeAnimation () {
      let animate = function () {
        if (TWEEN.update()) {
          requestAnimationFrame(animate)
        }
      }
      animate()
    },
    start () {
      this.loadingPercent = 0
      this._playTweenCircle()
      this._playTweenNumber()
      this.isInquery = true
    },
    stop () {
      this.tweenCircle && this.tweenCircle.stop()
      this.tweenNumber && this.tweenNumber.stop()
      this.loadingPercent = 100
    }
  },
  components: {}
}
</script>

<style lang="stylus" ref="stylesheet/stylus" scoped>
@import '../../common/stylus/variable.styl'

.loading
  width 160px
  height 160px
  padding 5px
  .loading-box
    width 100%
    height 100%
    position relative
    .loading-circle-outside-wrap
      position absolute
      top 50%
      left 50%
      width 150px
      height 150px
      margin-top -75px
      margin-left -75px
      img
        width 150px
        height 150px
    .loading-circle-inside-wrap
      position absolute
      top 50%
      left 50%
      width 120px
      height 120px
      margin-top -60px
      margin-left -60px
      img
        width 120px
        height 120px
  .loading-content
    width 100%
    height 100%
    transform translateY(-100%)
    .loading-percent
      width 100%
      height 100%
      line-height 150px
      text-align center
      font-size 50px
      color $color-text-blue
      .per-cent
        font-size 25px
  .desc-text
    text-align center
    font-size 18px
    color $color-text-blue
    transform translateY(-135px)
</style>