<!--
@Date:   2018-01-02T14:30:29+08:00
@Last modified time: 2018-01-22T10:47:51+08:00
-->



<template>
  <div class="page-personal-info">
    <p-header @confirm="confirm">
      <span slot="title-text">个人信息</span>
      <span slot="btn-confirm">提交</span>
    </p-header>
    <group class="input-area" gutter="0">
      <x-address class="address" title="现居住地" v-model="address"
        :list="addressData" value-text-align="left" raw-value>
      </x-address>
      <x-input class="particular-address" title="详细地址" v-model="particularAddress" placeholder="请输入详细地址"></x-input>
      <x-address class="time" title="居住时长" v-model="time"
        :list="timeList" value-text-align="left" raw-value>
      </x-address>
      <cell class="job" :class="{actived: userInfo.is_work_auth === 1}" title="工作信息" :value="userInfo.is_work_auth | statusFormatter" is-link link="/me/auth/personal/job"></cell>
      <cell class="relationship" :class="{actived: userInfo.is_relation_auth === 1}" title="人际关系" :value="userInfo.is_relation_auth | statusFormatter" is-link link="/me/auth/personal/relationship"></cell>
    </group>
    <transition name="personal-router-fade" mode="out-in">
      <keep-alive>
        <router-view class="personal-container"></router-view>
      </keep-alive>
    </transition>
  </div>
</template>

<script>
import PHeader from '@/components/APageHeader/APageHeader'
import { Group, XAddress, ChinaAddressV3Data, Cell, XInput, Picker, Value2nameFilter as value2name } from 'vux'
import timeListData from '@/assets/datas/timeListForPersonal.json'

import { createNamespacedHelpers } from 'vuex'
const { mapGetters, mapActions } = createNamespacedHelpers('authenticationCenter/personalInfo')

export default {
  name: 'pagePersonalInfo',
  data () {
    return {
      addressData: ChinaAddressV3Data,
      timeList: timeListData
    }
  },
  created () {
    this.fetchUserInfo()
  },
  mounted () {
  },
  computed: {
    ...mapGetters([
      'userInfo', 'isUserInfoConfirmed', 'confirmPerMsg'
    ]),
    address: {
      get () {
        let area = this.$store.state.authenticationCenter.personalInfo.userInfo.live_area
        return area.length === 0 ? ['福建省', '厦门市', '湖里区'] : area
      },
      set (area) {
        this.$store.commit('authenticationCenter/personalInfo/UPDATE_USER_INFO_ADDRESS', {area})
      }
    },
    particularAddress: {
      get () {
        return this.$store.state.authenticationCenter.personalInfo.userInfo.live_addr
      },
      set (address) {
        this.$store.commit('authenticationCenter/personalInfo/UPDATE_USER_INFO_PARTICULARADDRESS', {address})
      }
    },
    time: {
      get () {
        let time = this.$store.state.authenticationCenter.personalInfo.userInfo.live_time
        return time.length === 0 ? ['一到三个月'] : time
      },
      set (time) {
        this.$store.commit('authenticationCenter/personalInfo/UPDATE_USER_INFO_TIME', {time})
      }
    }
  },
  watch: {
    'confirmPerMsg' () {
      this._alertShow(this.confirmPerMsg.replace(/\d+/g, ''), () => {
        this.$router.push('/me/auth')
      })
    }
  },
  methods: {
    ...mapActions([
      'fetchUserInfo', 'saveUsreInfo'
    ]),
    localValidate () {
      if (this.particularAddress === '') {
        this._alertShow('详细居住地址为必填项')
        return false
      }

      if (this.userInfo.is_work_auth !== 1) {
        this._alertShow('工作信息为必填项')
        return false
      }

      if (this.userInfo.is_relation_auth !== 1) {
        this._alertShow('关系信息为必填项')
        return false
      }

      return true
    },
    confirm () {
      // 本地校验
      if (!this.localValidate()) return
      // convert names to values
      let area = value2name(this.address, this.addressData).split(' ')
      let time = this.timeList.find(ele => {
        return ele.value === this.time.join('')
      }).name.split(' ')
      let postData = {
        live_area: area,
        live_addr: this.particularAddress,
        live_time: time
      }
      // 请求服务端提交
      this.saveUsreInfo(postData)
    }
  },
  filters: {
    statusFormatter (enumFlag) {
      return enumFlag === 1 ? '已填写' : '未填写'
    }
  },
  components: {
    PHeader,
    Group,
    XAddress,
    Cell,
    XInput,
    Picker
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus">
@import '../../../../../../../../common/stylus/variable.styl'

  .page-personal-info
    .input-area
      .weui-cell
        height 55px
        .weui-label, .vux-label /* 标题样式 */
          font-size 15px
          color $color-text-black
      .address, .time
        .vux-popup-picker-select-box
          padding-left 35px
          .vux-popup-picker-value
            font-size 15px
            color $color-text-black
      .particular-address
        .weui-cell__bd
          padding-left 17px
          font-size 15px
          color $color-text-black
      .job, .relationship
        .weui-cell__ft
          font-size 12px
        &.actived
          .weui-cell__ft
            color $color-text-yellow
    .personal-container
      position fixed
      top 0
      left 0
      bottom 0
      width 100%
      background #fff
      &.personal-router-fade-enter-active, &.personal-router-fade-leave-active
        transition all .6s ease
      &.personal-router-fade-enter, &.personal-router-fade-leave-to
        opacity 0
</style>
