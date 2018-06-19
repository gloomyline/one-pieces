<!--
@Date:   2018-01-02T14:30:29+08:00
@Last modified time: 2018-01-02T14:45:26+08:00
-->



<template>
  <div class="page-job">
    <j-header @confirm="confirm">
      <span slot="title-text">工作信息</span>
      <span slot="btn-confirm">确认</span>
    </j-header>
    <group class="input-area" gutter="0">
      <x-input class="input-item industry" v-model="industry" title="从事行业" placeholder="如：建筑业"></x-input>
      <x-input class="input-item poistion" v-model="position" title="工作岗位" placeholder="如：建筑师"></x-input>
      <x-input class="input-item company" v-model="company" title="单位名称" placeholder="如：中国铁建公司"></x-input>
      <x-address class="input-item company-address" title="单位所在地" v-model="address" :list="addressData" raw-value value-text-align="left"></x-address>
      <x-input class="input-item particular-address" v-model="particularAddress" title="详细信息" placeholder="如：湖里区高新技术园新捷创1007号"></x-input>
      <x-input class="input-item company-phone" v-model="companyPhone" title="单位电话" placeholder="(选填)如：010-66194545"></x-input>
    </group>
    <section class="tips">
      <div class="title">
        <div class="line"></div>
        <div class="text">温馨提示</div>
        <div class="line"></div>
      </div>
      <ol class="tip-list">
        <li class="tip-item">保证隐私安全，不会与您所在单位联系</li>
        <li class="tip-item">请根据您实际情况如实填写</li>
      </ol>
    </section>
  </div>
</template>

<script>
import JHeader from '@/components/APageHeader/APageHeader'
import { Group, XInput, XAddress, ChinaAddressV3Data, Value2nameFilter as value2name } from 'vux'

import { createNamespacedHelpers } from 'vuex'
const namespace = 'authenticationCenter/personalInfo'
const { mapGetters, mapActions } = createNamespacedHelpers(namespace)

export default {
  name: 'pageJob',
  data () {
    return {
      addressData: ChinaAddressV3Data
    }
  },
  created () {
    this.fetchJobInfo()
  },
  mounted () {},
  computed: {
    ...mapGetters([
      'jobInfo', 'confirmMsg'
    ]),
    industry: {
      get () {
        return this.jobInfo.industry
      },
      set (industry) {
        this.$store.commit(`${namespace}/UPDATE_JOB_INFO_INDUSTRY`, {industry})
      }
    },
    position: {
      get () {
        return this.jobInfo.position
      },
      set (position) {
        this.$store.commit(`${namespace}/UPDATE_JOB_INFO_POSITION`, {position})
      }
    },
    company: {
      get () {
        return this.jobInfo.company_name
      },
      set (company) {
        this.$store.commit(`${namespace}/UPDATE_JOB_INFO_COMPANY`, {company})
      }
    },
    address: {
      get () {
        let address = this.$store.state.authenticationCenter.personalInfo.jobInfo.company_area
        return address.length === 0 ? ['福建省', '厦门市', '湖里区'] : address
      },
      set (address) {
        this.$store.commit(`${namespace}/UPDATE_JOB_INFO_ADDRESS`, {address})
      }
    },
    particularAddress: {
      get () {
        return this.jobInfo.company_addr
      },
      set (address) {
        this.$store.commit(`${namespace}/UPDATE_JOB_INFO_PARTICULARADDRESS`, {address})
      }
    },
    companyPhone: {
      get () {
        return this.jobInfo.company_tel
      },
      set (telephone) {
        this.$store.commit(`${namespace}/UPDATE_JOB_INFO_TELEPHONE`, {telephone})
      }
    }
  },
  watch: {
    'confirmMsg' () {
      this._alertShow(this.confirmMsg.replace(/\d+/g, ''), () => {
        this.$router.go(-1)
      })
    }
  },
  methods: {
    ...mapActions([
      'fetchJobInfo', 'saveJobInfo'
    ]),
    localValidate () {
      let arrayMap = [
        {
          name: 'industry',
          msg: '从事行业为必填项'
        },
        {
          name: 'position',
          msg: '工作岗位为必填项'
        },
        {
          name: 'company_name',
          msg: '单位名称为必填项'
        },
        {
          name: 'company_addr',
          msg: '详细信息为必填项'
        }
      ]
      for (let i = 0; i < arrayMap.length; ++i) {
        let item = arrayMap[i]
        if (this.jobInfo[item.name].replace(/(\s+)/, '') === '') {
          this._alertShow(item.msg)
          return false
        }
      }
      return true
    },
    confirm () {
      if (!this.localValidate()) return
      let postData = {
        industry: this.industry,
        position: this.position,
        company_name: this.company,
        company_area: value2name(this.address, ChinaAddressV3Data).split(' '),
        company_addr: this.particularAddress,
        company_tel: this.companyPhone
      }
      // console.log('11', postData)
      this.saveJobInfo(postData)
    }
  },
  components: {
    JHeader,
    Group,
    XInput,
    XAddress
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus">
@import '../../../../../../../../../../common/stylus/mixin.styl'
@import '../../../../../../../../../../common/stylus/variable.styl'

.page-job
  .input-area
    .weui-cell
      height 55px
      .weui-cell__bd
        padding-left 10px
        font-size 15px
        color $color-text-black
      .vux-popup-picker-select-box
        padding-left 10px
        font-size 15px
        color $color-text-black
  .tips
    width 80%
    margin 50px auto 0
    font-size 13px
    color $color-text-grey-higher
    .title
      display flex
      text-align center
      .line
        flex 1
        position relative
        top -8px
        border-bottom 1px solid $color-grey-higher1
      .text
        padding 0 10px
    .tip-list
      width 80%
      margin 8px auto 0
      .tip-item
        line-height 1.6em
</style>
