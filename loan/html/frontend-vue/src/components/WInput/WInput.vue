<template>
  <div class="w-input" :class="wType">
    <div class="w-input-wrap border-1px" :class="{actived: isFocus}">
      <span class="w-input-icon">
        <span class="icon-mobile-book" v-if="wType === 'account'"></span>
        <span class="icon-lock" v-else-if="wType === 'password'"></span>
        <span class="icon-msg-verification" v-else-if="wType === 'verification'"></span>
      </span>
      <input class="w-input-area" ref="input" :placeholder="placeholder" v-model="currentValue" type="password" v-if="isType === 'password' && pwdType === 'password'">
      <input class="w-input-area" ref="input" :placeholder="placeholder" v-model="currentValue" type="text" v-else>
      <span class="w-input-clear-icon" v-show="clearShow" @click="clear">
        <span class="icon-cross_1"></span>
      </span>
      <div class="icon-eye-wrap" v-if="isType === 'password'" @click="togglePwdShow">
        <span class="icon-eye" :class="{'icon-eye-show': !pwdShow, 'icon-eye-hide': pwdShow}"></span>
      </div>
    </div>
    <div class="w-input-validation" v-if="!!isType">
      <p class="validation-error" v-show="!isValid">{{validationError}}</p>
    </div>
  </div>
</template>

<script>
import { isPhoneNumber, isPassword } from '@/common/js/utils'

export default {
  name: 'wInput',
  data () {
    return {
      isFocus: false,
      isInitValid: true,
      currentValue: '',
      pValid: false,
      validationError: '',
      pwdShow: false,
      pwdType: 'password'
    }
  },
  props: {
    value: {
      type: [String, Number]
    },
    wType: {
      type: String,
      default: 'account'
    },
    placeholder: {
      type: String,
      default: '这里填写默认文本'
    },
    isType: {
      type: String,
      default: ''
    }
  },
  watch: {
    'currentValue' () {
      this.$emit('input', this.currentValue)
    }
  },
  mounted () {
    // if (this.isType === 'password') {
    //   this.$refs.input.type = 'password'
    //   this.$refs.input.maxLength = 15
    // } else {
    //   this.isType === 'text'
    // }
  },
  computed: {
    isValid () {
      let curVal = this.currentValue
      if (this.isInitValid) return true

      if (this.isType === 'account') {
        let _result = isPhoneNumber(curVal)
        if (_result) return _result
        else this.validationError = '请输入正确的手机号'
      } else if (this.isType === 'password') {
        let _result = isPassword(curVal)
        if (_result.isValid) return _result.isValid
        else this.validationError = _result.errMsg
      } else if (this.isType === 'verification') {
        if (!curVal) this.validationError = '请输入短信验证码'
        else return true
      }
    },
    clearShow () {
      if (this.currentValue) return true
      return false
    }
  },
  methods: {
    clear () {
      this.currentValue = ''
    },
    onFocus (event) {
      this.isFocus = true
      this.isInitValid = false
      this.$refs.input.focus()
    },
    loseFocus () {
      this.isFocus = false
    },
    togglePwdShow () {
      this.pwdShow = !this.pwdShow
      if (this.pwdShow) {
        this.pwdType = 'text'
      } else {
        this.pwdType = 'password'
      }
    }
  },
  components: {}
}
</script>

<style lang="stylus" ref="stylesheet/stylus" scoped>
@import '../../common/stylus/variable.styl'
@import '../../common/stylus/mixin.styl'

.w-input
  width 100%
  .w-input-validation
    height 26px
    .validation-error
      // width 250px
      width 80%
      margin 0 auto
      line-height 26px
      padding-left 24px
      font-size 12px
      color $color-text-error
  .w-input-wrap
    // width 250px
    width 80%
    height 40px
    margin 0 auto
    padding 7px 0 7px 0
    font-size 0
    border 1px solid $color-grey
    border-radius 20px 20px
    &.actived
      border 1px solid $color-blue
      box-shadow 3px 5px 5px $color-blue-light
      .w-input-icon
        color $color-text-blue
    .w-input-icon
      margin-left 24px
      font-size 20px
      color $color-text-grey-higher
    .w-input-area
      display inline-block
      vertical-align top
      width 64%
      height 20px
      margin-left 10px
      font-size 16px
      color $color-text-black
      input-placeholder($color-text-grey)
    .icon-eye-wrap
      display inline-block
      vertical-align top
      margin-top -6px
      padding 4px
      .icon-eye
        font-size 24px
        color $color-text-blue
    .w-input-clear-icon
      font-size 20px
      color $color-text-blue
  &.verification
    width 60%
    .w-input-wrap
      width 100%
      .w-input-area
        width 70px
</style>