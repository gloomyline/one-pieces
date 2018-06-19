<template>
  <div class="w-circle">
    <svg viewBox="0 0 100 100">
      <defs>
        <linearGradient id="blue_gradient" x1="0%" y1="0%" x2="100%" y2="0%">
          <stop offset="0%" style="stop-color:rgb(58,222,252);
          stop-opacity:1"/>
          <stop offset="100%" style="stop-color:rgb(55,147,255);
          stop-opacity:1"/>
        </linearGradient>
      </defs>
      <path :d="pathString" :stroke="trailColor" :stroke-width="trailWidth" :fill-opacity="0" /> 
      <path :d="pathString" stroke-linecap="strokeLinecap" stroke="url(#blue_gradient)" :stroke-width="strokeWidth" fill-opacity="0" :style="pathStyle" />
    </svg>
    <div class="w-circle-content">
      <slot></slot>
    </div>
  </div>
</template>

<script>
export default {
  name: 'wCircle',
  data () {
    return {}
  },
  mounted () {},
  props: {
    trailColor: {
      type: String,
      default: '#bddffc'
    },
    trailWidth: {
      type: Number,
      default: 12
    },
    strokeWidth: {
      type: Number,
      default: 12
    },
    percent: {
      type: Number,
      default: 0
    },
    strokeLinecap: {
      type: String,
      default: 'round'
    }
  },
  computed: {
    radius () {
      return 50 - this.strokeWidth / 2
    },
    pathString () {
      return `M 50,50 m ${this.radius},0
              a ${this.radius},${this.radius} 0 1 1 -${2 * this.radius}, 0
              a ${this.radius},${this.radius} 0 1 1 ${2 * this.radius}, 0`
    },
    len () {
      return Math.PI * 2 * this.radius
    },
    pathStyle () {
      return {
        'stroke-dasharray': `${this.len}px ${this.len}px`,
        'stroke-dashoffset': `${(100 - this.percent) / 100 * this.len}px`
        // 'transition': 'stroke-dashoffset .6s ease 0s, stroke .6s ease'
      }
    }
  },
  methods: {},
  components: {}
}
</script>

<style lang="stylus" ref="stylesheet/stylus" scoped>
.w-circle 
  position relative
  width 100%
  height 100%
  .w-circle-content 
    width 100%
    text-align center
    position absolute
    left 0
    top 50%
    transform translateY(-50%)
</style>