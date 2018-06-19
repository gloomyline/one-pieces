<template>
  <div class="page-relationship">
    <r-header @confirm="confirm">
      <span slot="title-text">人际关系</span>
      <span slot="btn-confirm">提交</span>
    </r-header>
    <group class="relationship-content" gutter="0">
      <x-address class="relation" title="与本人关系" v-model="firstPeopleRelation"
        :list="relationList" value-text-align="left" raw-value>
      </x-address>
      <x-input class="name" title="姓名" v-model="firstPeopleName" placeholder="请填写联系人姓名">
        <div class="btn-open-address-list" slot="right" @click="openAddressList"><span class="icon-address-book"></span></div>
      </x-input>
      <x-input class="phone" title="电话" v-model="firstPeoplePhone" placeholder="请填写联系人手机号"></x-input>
    </group>
    <group class="relationship-content" gutter="0">
      <x-address class="relation" title="与本人关系" v-model="secondPeopleRelation"
        :list="relationList" value-text-align="left" raw-value>
      </x-address>
      <x-input class="name" title="姓名" v-model="secondPeopleName" placeholder="请填写联系人姓名">
        <div class="btn-open-address-list" slot="right" @click="openAddressList"><span class="icon-address-book"></span></div>
      </x-input>
      <x-input class="phone" title="电话" v-model="secondPeoplePhone" placeholder="请填写联系人手机号"></x-input>
    </group>
    <!-- <x-button class="btn-add" type="primary" action-type="button" @click.native="addRelation">添加</x-button> -->
    <section class="warm-prompt">
      <div class="title">
        <span class="line"></span><span class="text">温馨提示</span></span><span class="line"></span>
      </div>
      <ol class="content">
        <li class="item">不会电话审核，请放心填写</li>
        <li class="item">信息需真实有效，否则将放款失败</li>
      </ol>
    </section>
  </div>
</template>

<script>
import RHeader from '@/components/APageHeader/APageHeader'
import { Group, XAddress, XInput, XButton } from 'vux'

import { isChineseName, isPhoneNumber } from '@/common/js/utils'
import relationList from '@/assets/datas/relationship.json'

import { createNamespacedHelpers } from 'vuex'
const namespace = 'authenticationCenter/personalInfo'
const { mapGetters, mapActions } = createNamespacedHelpers(namespace)

export default {
  name: 'pageRelationship',
  data () {
    return {
      people: {
        relation: ['父母'],
        name: '',
        phone: ''
      },
      relationList,
      addedRelation: []
    }
  },
  created () {
    this.fetchRelationInfo()
  },
  mounted () {},
  computed: {
    ...mapGetters([
      'relationInfo', 'confirmMsg'
    ]),
    firstPeopleRelation: {
      get () {
        let relation = this.relationInfo.linkman_relation_fir
        return relation.length === 0 ? ['父母'] : relation
      },
      set (relation) {
        this.$store.commit(`${namespace}/UPDATE_RELATION_INFO_BY_KEY`, {key: 'linkman_relation_fir', value: relation})
      }
    },
    firstPeopleName: {
      get () {
        return this.relationInfo.linkman_name_fir
      },
      set (name) {
        this.$store.commit(`${namespace}/UPDATE_RELATION_INFO_BY_KEY`, {key: 'linkman_name_fir', value: name})
      }
    },
    firstPeoplePhone: {
      get () {
        return this.relationInfo.linkman_tel_fir
      },
      set (phone) {
        this.$store.commit(`${namespace}/UPDATE_RELATION_INFO_BY_KEY`, {key: 'linkman_tel_fir', value: phone})
      }
    },
    secondPeopleRelation: {
      get () {
        let relation = this.relationInfo.linkman_relation_sec
        return relation.length === 0 ? ['朋友'] : relation
      },
      set (relation) {
        this.$store.commit(`${namespace}/UPDATE_RELATION_INFO_BY_KEY`, {key: 'linkman_relation_sec', value: relation})
      }
    },
    secondPeopleName: {
      get () {
        return this.relationInfo.linkman_name_sec
      },
      set (name) {
        this.$store.commit(`${namespace}/UPDATE_RELATION_INFO_BY_KEY`, {key: 'linkman_name_sec', value: name})
      }
    },
    secondPeoplePhone: {
      get () {
        return this.relationInfo.linkman_tel_sec
      },
      set (phone) {
        this.$store.commit(`${namespace}/UPDATE_RELATION_INFO_BY_KEY`, {key: 'linkman_tel_sec', value: phone})
      }
    }
  },
  watch: {
    'confirmMsg' () {
      this.alertShow(this.confirmMsg.replace(/\d+/g, ''), () => {
        this.$router.go(-1)
      })
    }
  },
  methods: {
    ...mapActions([
      'fetchRelationInfo', 'saveRelationInfo'
    ]),
    confirm () {
      if (!this.validateInfos()) return
      let postData = {
        linkman_relation_fir: this.firstPeopleRelation,
        linkman_name_fir: this.firstPeopleName,
        linkman_tel_fir: this.firstPeoplePhone,
        linkman_relation_sec: this.secondPeopleRelation,
        linkman_name_sec: this.secondPeopleName,
        linkman_tel_sec: this.secondPeoplePhone
      }
      this.saveRelationInfo(postData)
    },
    alertShow (errMsg, cb) {
      this.$vux.alert.show({
        content: errMsg,
        onHide: () => {
          if (this.timer) clearTimeout(this.timer)
          typeof cb === 'function' && cb()
        }
      })
      this.timer = setTimeout(() => {
        this.$vux.alert.hide()
      }, 3000)
    },
    validateInfos () {
      if (!this.firstPeopleName.trim() || !isChineseName(this.firstPeopleName)) {
        this.alertShow('请输入正确的第一位联系人姓名')
        return false
      }

      if (!this.firstPeoplePhone.trim() || !isPhoneNumber(this.firstPeoplePhone)) {
        this.alertShow('请输入正确的第一位联系人手机号')
        return false
      }

      if (!this.secondPeopleName.trim() || !isChineseName(this.secondPeopleName)) {
        this.alertShow('请输入正确的第二位联系人姓名')
        return false
      }

      if (!this.secondPeoplePhone.trim() || !isPhoneNumber(this.secondPeoplePhone)) {
        this.alertShow('请输入正确的第二位联系人手机号')
        return false
      }

      return true
    },
    openAddressList () {
      // 使用wechat js-sdk 获取手机通讯录
      console.log('address list opened')
    }
  },
  components: {
    RHeader,
    Group,
    XAddress,
    XInput,
    XButton
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus">
@import '../../../../../../common/stylus/mixin.styl'
@import '../../../../../../common/stylus/variable.styl'

.page-relationship
  background $color-white-high
  .relationship-content
    .weui-cell
      height 55px
      font-size 15px
      color $color-text-black
      .weui-cell__bd
        padding-left 58px
        font-size 15px
        color $color-text-black
    .relation
      .vux-popup-picker-select-box
        padding-left 25px
        .vux-popup-picker-value
          font-size 15px
          color $color-text-black
    .name
      .btn-open-address-list
        display inline-block
        vertical-align middle
        padding 4px
        .icon-address-book
          font-size 24px
          color $color-text-yellow
  .btn-add
    width 150px
    height 35px
    margin 21px auto 0 auto
    font-size 15px
    letter-spacing 2px
    color $color-text-white
    border-radius 17px 17px
    background-color $color-blue
    &:active
      color $color-text-white-high !important
      background-color $color-blue !important
      box-shawdow 2px 4px 2px inset $color-grey
  .warm-prompt
    width 80%
    margin 30px auto 0 auto
    .title
      display flex
      .line
        flex 1
        position relative
        top -8px
        border-bottom 1px solid $color-grey-higher1
      .text
        flex 1
        text-align center
        font-size 16px
        color $color-text-grey
    .content
      margin-top 15px
      padding-left 24px
      line-height 1.6em
      font-size 13px
      color $color-text-grey-higher
</style>