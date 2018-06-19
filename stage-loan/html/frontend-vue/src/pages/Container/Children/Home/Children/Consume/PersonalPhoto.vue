<!--
@Date:   2018-01-24T16:33:32+08:00
@Last modified time: 2018-02-02T15:10:26+08:00
-->
<template lang="html">
  <div class="page-personal-photo">
    <p-p-header class="personal-photo-header" @go-back="goBack($event)" not-has-confirm not-go-back>
      <span class="title" slot="title-text">亲签照</span>
    </p-p-header>
    <div class="personal-photo-content">
      <div class="img-example-box">
        <img-inputer class="photo-example" :imgSrc="img"
          readonly nhe noMask></img-inputer>
        <h3 class="example-photo-tip">图例</h3>
      </div>
      <div class="img-upload-box">
        <div class="icon-wrap" v-show="iconShow"><i class="icon-camera"></i></div>
        <img-inputer class="photo-inputer" v-model="photoVal" :capture="false"
          accept="image/*" :imgSrc="photoUrl" placeholder="点击选择图片" nhe noMask></img-inputer>
        <p class="tip">请借款人上传<span class="color-text-yellow">横向</span>拍摄的左手手持身份证，</br>右手持合约，以商家logo为背景的亲签照</p>
      </div>
    </div>
    <footer class="personal-photo-footer">
      <x-button class="btn-submit-photo" type="primary" action-type="button" @click.native="submit">提交</x-button>
    </footer>
  </div>
</template>

<script>
import PPHeader from '@/components/APageHeader/APageHeader'
import ImgInputer from 'vue-img-inputer'
import { XButton } from 'vux'

import { createNamespacedHelpers } from 'vuex'
const namespace = 'container/home/shop/personalPhoto'
const { mapGetters, mapActions } = createNamespacedHelpers(namespace)

export default {
  data () {
    return {
      img: require('@/assets/imgs/personal-photo-example.jpg'),
      photoBase64String: '',
      iconShow: true
    }
  },
  async created () {
    await this.fetchPhoto()
    this.iconShow = !this.photoUrl
    // create Canvas in order to compress photo
    let canvas = this.canvas = document.createElement('canvas')
    this.canvasContext = canvas.getContext('2d')
    // bind file reader to vm instance
    this.fileReader = new FileReader()
    // bind a null Image instance to vm instance
    this.image = new Image()
    this.bindImageEvents()
  },
  mounted () {
  },
  watch: {
    // 'msg' () {
    //   this._alertShow(this.msg.replace(/\d+$/, ''))
    // }
  },
  computed: {
    ...mapGetters([
      'photo',
      'photoUrl'
      // 'msg'
    ]),
    photoVal: {
      get () {
        return this.photo
      },
      set (photo) {
        this.iconShow = false
        this.$store.commit(`${namespace}/UPDATE_FOR_PERSONAL_PHOTO`, {
          key: 'photo',
          val: photo
        })
      }
    }
  },
  methods: {
    ...mapActions([
      'fetchPhoto'
      // 'submitPhoto'
    ]),
    goBack (e) {
      this.$emit('close', e)
    },
    compressImgByCanvas (width, height) {
      this.canvas.width = width
      this.canvas.height = height
      // clear canvas
      this.canvasContext.clearRect(0, 0, width, height)
      // compress img
      this.canvasContext.drawImage(this.image, 0, 0, width, height)
      // return the imgBase64String
      return this.canvas.toDataURL(this.photo.type)
    },
    bindImageEvents () {
      this.fileReader.onloadend = (e) => {
        this.image.src = e.target.result
      }
      this.image.onload = (e) => {
        let img = e.target
        let originWidth = img.width
        let originHeight = img.height
        // max img size limit
        let maxWidth = 250
        let maxHeight = 185
        // destination img size
        let targetWidth = originWidth
        let targetHeight = originHeight
        if (originWidth > maxWidth || originHeight > maxHeight) { // img size bigger than 280x185 limit
          if (originWidth / originHeight > maxWidth / maxHeight) { // widthlier
            targetWidth = maxWidth
            targetHeight = Math.round(maxWidth * (originHeight / originWidth))
          } else {
            targetHeight = maxHeight
            targetWidth = Math.round(maxHeight * (originWidth / originHeight))
          }
        }
        let base64String = this.compressImgByCanvas(targetWidth, targetHeight)
        let photoNameArr = this.photo.name.split('.')
        let subfix = photoNameArr[photoNameArr.length - 1]
        this.$store.commit(`${namespace}/SELECTED_PERSONAL_PHOTO`, {
          file: base64String,
          type: subfix
        })
        this.$store.commit(`${namespace}/TOGGLE_PHOTO_CONFIRMED`)
      }
    },
    submit () {
      if (!this.photoUrl) {
        this.fileReader.readAsDataURL(this.photo)
      } else {
        this.$store.commit(`${namespace}/TOGGLE_PHOTO_CONFIRMED`)
      }
    }
  },
  components: {
    XButton,
    ImgInputer,
    PPHeader
  }
}
</script>

<style lang="stylus">
@import '../../../../../../common/stylus/mixin.styl'
@import '../../../../../../common/stylus/variable.styl'

.page-personal-photo
  .personal-photo-header
  .personal-photo-content
    .img-example-box
      .photo-example
        display: block
        width: 250px
        height: 185px
        margin: 12px auto 0
        img
          width: 100%
          height: 100%
      .example-photo-tip
        width: 70px
        height: 25px
        line-height: 25px
        text-align: center
        margin: 6px auto 8px
        font-size: 18px
        color: $color-text-yellow
        border: .5px solid $color-yellow
        border-radius: 12px 12px
    .img-upload-box
      position: relative
      .icon-wrap
        absolute: left 50% top 50%
        margin: -72px 0 0 -32px
        font-size: 64px
        pointer-events: none /* not capture any click or touch event, just let the event strike through */
        z-index: 10
      .photo-inputer
        display: block
        width: 250px
        height: 185px
        margin: 0 auto
        background-color: $color-grey-light
        .iconfont
          display: none
        .img-inputer__placeholder
          font-size: 16px
          color: $color-text-grey-higher
        .img-inputer__preview-box
          img
            width: 100%
            height: 100%
      .tip
        margin-top: 18px
        text-align: center
        font-size: 16px
        color: $color-text-black
  .personal-photo-footer
    .btn-submit-photo
      margin-top: 16px
      width: 340px
</style>
