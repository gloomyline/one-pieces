<!--
@Date:   2017-12-29T11:32:56+08:00
@Last modified time: 2017-12-29T16:13:08+08:00
-->
<template lang="html">
  <div>
    <m-header class="my-device-header" :not-has-confirm="true"
      not-go-back="" @go-back="goBack($event)"><span class="title" slot="title-text">我的设备</span></m-header>
    <group class="my-device-content" gutter="0">
      <cell class="current-device-title" title="当前设备"></cell>
      <cell class="current-device-value" :title="currentDevice"></cell>
      <cell class="other-device-title" title="其他设备"></cell>
      <cell class="other-device-value" :title="otherDevice"></cell>
    </group>
  </div>
</template>

<script>
import MHeader from '@/components/APageHeader/APageHeader'
import { Group, Cell } from 'vux'

export default {
  data () {
    return {
      currentDevice: 'PD1691 vivo X9 plus',
      otherDevice: '无'
    }
  },
  created () {
  },
  methods: {
    goBack (event) {
      this.$emit('close', event)
    }
  },
  components: {
    MHeader,
    Group,
    Cell
  }
}
</script>

<style lang="stylus">
@import '../../../../../../common/stylus/variable.styl'

.my-device
  .weui-cells
    .weui-cell
      &[class$='title']
        .vux-label
          color: $color-text-yellow
</style>
