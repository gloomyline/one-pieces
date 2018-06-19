<!--
@Date:   2018-01-02T14:30:29+08:00
@Last modified time: 2018-01-30T10:32:51+08:00
-->
<template>
  <div class="page-upper-limit-authen">
    <a-header @confirm="confirm">
      <span slot="title-text">{{ title }}</span>
      <span slot="btn-confirm" v-show="!authResultShowed">提交</span>
    </a-header>
    <div class="img-wrap">
      <img :src="img" width="100%" height="100%">
    </div>
    <group class="input-area" gutter="0" v-if="!isTaobao">
      <!-- <x-address class="bankcard" title="银行" :list="bankList" v-model="bank" v-if="authenticateType === 'netbank'" value-text-align="left" raw-value></x-address> -->
      <cell class="bank" title="银行" value="中国银行" value-align="left" v-if="authenticateType === 'netbank'"></cell>
      <popup-picker class="mall-picker" title="所在城市" :data="cities" v-model="currentCity" show-name v-if="authenticateType === 'housefund' || authenticateType === 'socialsecurity'"></popup-picker>
      <x-input class="acc" :class="accClassMap" :title="labelAcc" :placeholder="placeholder" v-model="acc"></x-input>
      <x-input class="pwd" :class="pwdClassMap" :type="pwdType" :title="labelPwd" placeholder="请输入密码" v-model="pwd" v-if="labelPwd">
        <div class="icon-eye-wrap" slot="right" @click="toggleShowPwd"><span class="icon-eye" :class="{'icon-eye-show': isPwdHide, 'icon-eye-hide': !isPwdHide}"></span></div>
      </x-input>
      <x-input class="credit-code" title="身份证验证码" placeholder="请输入身份证验证码" v-model="identityCode"
        v-if="authenticateType === 'credit'"></x-input>
    </group>
    <!-- 央行征信 -->
    <div class="credit-tip" v-if="authenticateType === 'credit'">
      <div class="link-open-how"><i class="icon-doubt"></i><span class="text">怎样进行征信认证？</span></div>
    </div>
    <!-- 京东认证 -->
    <div class="jingdong-auth">
      <transition name="loading-fade">
        <div class="loading-wrap" v-show="jingdong.isLoading">
          <loading  ref="loading"></loading>
        </div>
      </transition>
      <transition name="popup">
        <div class="popup-wrap" v-if="jingdong.isVerificationInputShow">
          <group class="popup-box" gutter="0">
            <h3 class="popup-title">请输入京东发送的手机短信验证码</h3>
            <x-input class="sms-code-input" title="短信验证码" v-model="jdVerification"></x-input>
            <XButton class="btn-confirm-smscode btn-yellow" type="primary" action-type="button" @click.native="verificationConfirm">确认</XButton>
          </group>
        </div>
      </transition>
    </div>
    <!-- 淘宝认证 -->
    <div class="taobao-auth">
      <transition name="qr-fade">
        <div class="qr-popup-wrap" v-show="qr.isShow">
          <div class="qr-popup-box">
            <div class="qr-img-wrap">
              <img class="qr-img" :src="qr.img" alt="">
            </div>
            <p class="save-instruction">长按图片可保存图片</p>
            <x-button class="bnt-taobao-authen-continue btn-yellow" @click.native="scanQrConfirm">我已授权登录{{ authorization }}</x-button>
            <div class="btn-close" @click="closeQrPopup">
              <i class="icon-cross_1"></i>
            </div>
          </div>
        </div>
      </transition>
      <ul class="qr-use-instructions" v-if="isTaobao && !taobao.isTaobaoAuthenticatedSuccess">
        <li class="instruction">请点击<span class="blue bold">提交</span>获取二维码</li>
        <li class="instruction">请打开手机淘宝APP扫码登录</li>
        <li class="instruction">点击<span class="blue">主界面</span>-<span class="blue">左上角</span>-<span class="blue">扫一扫</span>功能</li>
        <li class="instruction">扫描获取的二维码按进行登录</li>
        <li class="instruction">或者保存二维码至相册，<span class="blue">选择图片登录</span></li>
      </ul>
      <div class="auth-again" v-else-if="isTaobao && taobao.isTaobaoAuthenticatedSuccess">
        <p class="tip-auth-success">恭喜您，认证成功！</p>
        <x-button class="btn-autn-again btn-yellow" type="primary" action-type="button">重新认证</x-button>
      </div>
    </div>
    <transition name="auth-success-fade">
      <div class="page-auth-results" v-show="authResultShowed">
        <div class="auth-success"  v-if="authSuccessResult">
          <div class="img-wrap">
            <img :src="authSuccessImg" width="100" height="100">
          </div>
          <p class="success desc">恭喜您，认证成功啦！</p>
        </div>
        <div class="auth-failed" v-else>
          <div class="img-wrap">
            <img :src="authFailImg" width="100" height="100">
          </div>
          <p class="fail desc">很可惜，请重新认证！</p>
        </div>
        <x-button class="btn-auth-again btn-yellow" @click.native="authAgain">重新认证</x-button>
      </div>
    </transition>
  </div>
</template>

<script>
import AHeader from '@/components/APageHeader/APageHeader'
import Loading from '@/components/Loading/Loading'
import { Group, XInput, PopupPicker, XButton, XAddress, Cell } from 'vux'

import { createNamespacedHelpers } from 'vuex'
const namespace = 'authenticationCenter/upLimit'
const { mapGetters, mapActions } = createNamespacedHelpers(namespace)

import bankList from '@/assets/datas/bankcard.json'
import housefundCities from '@/assets/static-configs/limuHouseFundAreas_config'
import socialCities from '@/assets/static-configs/limuSocialSecurityAreas_config'

const map = [
  {
    title: '央行征信',
    labelAcc: '登录名',
    labelPwd: '密码',
    img: require('@/assets/imgs/investigation-header-bg.jpg')
  },
  {
    title: '公积金',
    labelAcc: '账号',
    labelPwd: '密码',
    img: require('@/assets/imgs/house-fund-header-bg.jpg')
  },
  {
    title: '淘宝认证',
    labelAcc: '淘宝账号',
    labelPwd: '淘宝密码',
    img: require('@/assets/imgs/taobao-header-bg.jpg')
  },
  {
    title: '信用卡账单',
    labelAcc: '邮箱',
    labelPwd: '密码',
    img: require('@/assets/imgs/credit-bill-header-bg.jpg')
  },
  {
    title: '京东认证',
    labelAcc: '京东账号',
    labelPwd: '京东密码',
    img: require('@/assets/imgs/jingdong-header-bg.jpg')
  },
  {
    title: '学历认证',
    labelAcc: '学信网账号',
    labelPwd: '学信网密码',
    img: require('@/assets/imgs/diplomas-header-bg.jpg')
  },
  {
    title: '网银流水',
    labelAcc: '账号',
    labelPwd: '密码',
    img: require('@/assets/imgs/ebank-header-bg.jpg')
  },
  {
    title: '社保',
    labelAcc: '账号',
    labelPwd: '密码',
    img: require('@/assets/imgs/social-security-header-bg.jpg')
  }
]
const UplimitTypeDict = {
  'credit': 0,
  'housefund': 1,
  'taobao': 2,
  'creditbill': 3,
  'jingdong': 4,
  'diplomas': 5,
  'netbank': 6,
  'socialsecurity': 7
}

export default {
  name: 'pageAuthen',
  data () {
    return {
      title: '',
      labelAcc: '',
      labelPwd: '',
      img: '',
      cities: [],
      bankList: [],
      bank: ['中国银行'],
      authenticateType: '',
      isPwdHide: true,
      isTaobao: false,
      authCount: 0,
      authSuccessImg: require('./icon_auth_success.png'),
      authFailImg: require('./icon_auth_failed.png')
    }
  },
  created () {
    this._initData()
    this._initBankListData()
  },
  mounted () {
  },
  destroyed () {
    this.$store.commit(`${namespace}/TOGGLE_RESULT_SHOW`, { isShow: false })
  },
  computed: {
    ...mapGetters([
      'form', 'resMsg', 'isBankcardValid', 'bankcardInvalidMsg', 'credit', 'jingdong', 'taobao', 'diploma', 'creditbill', 'ebank', 'authResultShowed', 'qr', 'common'
    ]),
    accClassMap () {
      return {
        'credit': this.authenticateType === 'credit'
      }
    },
    pwdClassMap () {
      return {
        'credit': this.authenticateType === 'credit'
      }
    },
    placeholder () {
      return `请输入${this.labelAcc}`
    },
    pwdType () {
      return this.isPwdHide ? 'password' : 'text'
    },
    acc: {
      get () {
        return this.form[this.authenticateType].acc
      },
      set (val) {
        this.$store.commit(`${namespace}/UPDATE_AUTHENTICATE_ACC`, { key: this.authenticateType, acc: val })
      }
    },
    pwd: {
      get () {
        return this.form[this.authenticateType].pwd
      },
      set (val) {
        this.$store.commit(`${namespace}/UPDATE_AUTHENTICATE_PWD`, { key: this.authenticateType, pwd: val })
      }
    },
    identityCode: {
      get () {
        return this.form.credit.identityCode
      },
      set (val) {
        this.$store.commit(`${namespace}/UPDATE_AUTHENTICATE_CREDIT_IDENTITY_CODE`, { identityCode: val })
      }
    },
    currentCity: {
      get () {
        return [this.form[this.authenticateType].city]
      },
      set (cityArr) {
        this.$store.commit(`${namespace}/UPDATE_SELECTED_CITY`, { type: this.authenticateType, city: cityArr.join('') })
      }
    },
    verification: {
      get () {
        return this.form['taobao'].verification
      },
      set (val) {
        this.$store.commit(`${namespace}/UPDATE_AUTHENTICATE_TAOBAO_VERIFICATION`, { verification: val })
      }
    },
    jdVerification: {
      get () {
        return this.jingdong.verification
      },
      set (val) {
        this.$store.commit(`${namespace}/UPDATE_AUTHENTICATE_JINDONG_VERIFICATION`, { verification: val })
      }
    },
    authorization () {
      return this.authenticateType === 'taobao' ? '淘宝' : 'QQ'
    },
    jingdongIsLoading () {
      return this.jingdong.isLoading
    },
    jingdongResErr () {
      return this.jingdong.err
    },
    authSuccessResult () {
      if (this.jingdong.authState === 1 || this.taobao.authState === 1 || this.diploma.authState === 1 || this.creditbill.authState === 1 || this.ebank.authState === 1) {
        return true
      } else if (this.common.isAuthSuccess) {
        return true
      } else {
        return false
      }
    }
  },
  watch: {
    'resMsg' () {
      this._alertShow(this.resMsg.replace(/\d+/g, ''), () => {
        this.$router.go(-1)
      })
    },
    'bankcardInvalidMsg' () {
      this._alertShow(this.bankcardInvalidMsg.replace(/\d+/g, ''))
    },
    'jingdongIsLoading' (newVal, oldVal) {
      if (newVal) { // set the loading is showing or not
        this.$refs.loading.start()
      } else {
        this.$refs.loading.stop()
      }
    },
    'jingdongResErr' () {
      this._alertShow(this.jingdongResErr.replace(/\d+/g, ''))
    }
  },
  methods: {
    ...mapActions([
      'authenticate', 'fetchAuthenticated', 'authentiacteTaobaoContinue', 'authenticateCreditBillContinue', 'authenticateJingdongVerification'
    ]),
    _initData () {
      let type = this.authenticateType = this.$route.params.typeId
      if (type === 'taobao') {
        this.isTaobao = true
      } else {
        this.isTaobao = false
      }

      // update state local
      let needAuthObj = map[UplimitTypeDict[type]]
      this.title = needAuthObj.title
      this.labelAcc = needAuthObj.labelAcc
      this.labelPwd = needAuthObj.labelPwd ? needAuthObj.labelPwd : null
      this.img = needAuthObj.img
      this.cities = type === 'housefund' ? [housefundCities] : [socialCities]

      // update state from sever
      let authType
      switch (type) {
        case 'jingdong':
          authType = 'jd'
          break
        case 'diplomas':
          authType = 'education'
          break
        case 'creditbill':
          authType = 'bill'
          break
        case 'netbank':
          authType = 'ebank'
          break
        default:
          authType = type
          break
      }

      this.fetchAuthenticated(authType)
    },
    _initBankListData () {
      let newBankList = []
      bankList.forEach((item, index) => {
        let newItem = {}
        newItem.name = item.name
        newItem.value = String(item.id)
        newBankList.push(newItem)
      })
      this.bankList = newBankList
    },
    toggleShowPwd () {
      this.isPwdHide = !this.isPwdHide
    },
    toggleLoginForTaobao () {
      this.isLoginByPwdForTaobao = !this.isLoginByPwdForTaobao
    },
    closeQrPopup () {
      this.$store.commit(`${namespace}/CLOSE_AUTHENTICATE_POPUP_QR`)
    },
    authAgain () {
      this.$store.commit(`${namespace}/TOGGLE_RESULT_SHOW`, { isShow: false })
    },
    scanQrConfirm () {
      if (this.authenticateType === 'taobao') {
        this.authorizateTaobao()
      } else if (this.authenticateType === 'creditbill') {
        this.authorizateQQ()
      }
    },
    authorizateQQ () {
      this.authenticateCreditBillContinue()
      this.$store.commit(`${namespace}/CLOSE_AUTHENTICATE_POPUP_QR`)
    },
    authorizateTaobao () {
      this.authentiacteTaobaoContinue()
      this.$store.commit(`${namespace}/CLOSE_AUTHENTICATE_POPUP_QR`)
    },
    confirm () {
      this.authenticate(this.authenticateType)
    },
    verificationConfirm () {
      this.authenticateJingdongVerification()
    }
  },
  components: {
    AHeader,
    Loading,
    Group,
    PopupPicker,
    XInput,
    XButton,
    XAddress,
    Cell
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus">
@import '../../../../../../../../../common/stylus/mixin.styl'
@import '../../../../../../../../../common/stylus/variable.styl'

.page-upper-limit-authen
  .img-wrap
    width: 100%
    height: 150px
  .input-area
    .weui-cells
      .weui-cell
        &.credit
          &.acc
            .weui-cell__bd
              padding-left: 48px
          &.pwd
            .weui-cell__bd
              padding-left: 64px
        .weui-cell__ft
          .icon-eye
            font-size: 20px
            color: $color-text-yellow
    .bank
      .weui-cell__ft
        padding-left 16px
    .pwd
      .icon-eye-wrap
        display inline-block
        padding 8px
        .icon-eye
          font-size 24px
          color $color-text-blue
  .credit-tip
    margin-top: 32px
    text-align: center
    color: $color-text-yellow
    .icon-doubt
      vertical-align: middle
      font-size: 20px
    .text
      margin-left: 8px
      vertical-align: middle
      font-size: 16px
  .jingdong-auth
    .loading-wrap
      position fixed
      left 0
      top 55px
      bottom 0
      width 100%
      background-color $color-white
      z-index 33
      overflow hidden
      &.loading-fade-enter-active, &.loading-fade-leave-active
        transition all .6s ease
      &.loading-fade-enter, &.loading-fade-leave-to
        opacity 0
      .loading
        margin 90px auto 0
    .popup-wrap
      position fixed
      left 0
      top 0
      bottom 0
      width 100%
      background transparent
      z-index 44
      overflow hidden
      &.popup-enter-acitve, &.popup-leave-active
        transition all .6s ease
      &.popup-enter, &.popup-leave-to
        opacity 0
      .popup-box
        width 90%
        margin 160px auto 0
        box-shadow 3px 5px 5px rgba(0, 0, 0, .4)
        .weui-cells
          padding 15px 0
          .popup-title
            line-height 32px
            text-align center
            font-size 18px
            color $color-text-black
          .btn-confirm-smscode
            width 120px !important
            font-size 16px !important
            letter-spacing 2px !important
  .taobao-auth
    .qr-popup-wrap
      position fixed
      left 0
      top 0
      bottom 0
      width 100%
      background transparent
      z-index 44
      overflow hidden
      &.qr-fade-enter-acitve, &.qr-fade-leave-active
        transition all .6s ease
      &.qr-fade-enter, &.qr-fade-leave-to
        opacity 0
      .qr-popup-box
        position relative
        width 72%
        margin 160px auto 0
        text-align center
        padding-bottom 10px
        background $color-white
        box-shadow 3px 5px 5px rgba(0, 0, 0, .4)
        .qr-img-wrap
          margin-bottom 12px
        .save-instruction
          line-height 1.6em
          font-size 16px
        .bnt-taobao-authen-continue
          width 180px !important
          margin 10px auto
        .btn-close
          position absolute
          right 0
          top 0
          padding 4px
          font-size 24px
          color $color-text-blue
    .qr-use-instructions
      width 78%
      margin 32px auto 0
      .instruction
        line-height 1.6em
        font-size 16px
        color $color-text-black
        .blue
          color $color-text-blue
        .bold
          font-weight bold
  .page-auth-results
    position fixed
    left 0
    top 55px
    bottom 0
    width 100%
    background $color-white
    &.auth-success-fade-enter-active, &.auth-success-fade-leave-active
      transition all .6s ease
    &.auth-success-fade-enter, &.auth-success-fade-leave-to
      opacity 0
    .auth-success, .auth-failed
      text-align center
      font-size 18px
      color $color-text-black
      .desc
        line-height 1.6em
    .btn-auth-again
      margin-top 12px
</style>
