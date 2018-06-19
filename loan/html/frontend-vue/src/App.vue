<template>
  <div id="app">
    <div v-transfer-dom>
      <loading :show="isLoading" :text="loadingText"></loading>
    </div>
    <transition name="router-fade" mode="out-in">
      <!-- <keep-alive> -->
      <router-view></router-view>
      <!-- </keep-alive> -->
    </transition>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import { Loading, TransferDomDirective as TransferDom } from 'vux'

export default {
  name: 'app',
  data () {
    return {
      loadingText: 'loading'
    }
  },
  created () {
  },
  computed: {
    ...mapGetters([
      'isLoading'
    ])
  },
  directives: {
    TransferDom
  },
  components: {
    Loading
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus" scoped>
#app 
  font-family 'Avenir', Helvetica, Arial, sans-serif
  -webkit-font-smoothing antialiased
  -moz-osx-font-smoothing grayscale
  color #2c3e50
  .router-fade-enter-active, .router-fade-leave-active 
    transition opacity .2s
  .router-fade-enter, .router-fade-leave-active 
    opacity 0
</style>
