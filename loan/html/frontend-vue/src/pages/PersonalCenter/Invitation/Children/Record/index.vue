<template>
  <div class="page-invitation-record">
    <i-header>
      <span slot="title-text">邀请记录</span>
      <span slot="btn-confirm"></span>
    </i-header>
    <div class="content" v-if="list && list.length !== 0">
      <h2 class="title">已邀请注册<span class="people-count">{{people}}</span>人</h2>
      <div class="scroller" ref="scroller">
        <ul class="record-list">
          <li class="record-item" v-for="(item, index) in list">
            <div class="left">
              <p class="phone">
                <span class="label">手机号码：</span>
                <span class="value">{{item.phone}}</span>
              </p>
              <p class="register-date">
                <span class="label">注册时间：</span>
                <span class="value">{{item.registerDate}}</span></p>
            </div>
            <div class="right">
              <p class="lend-count">
                <span class="label">借款金额：</span>
                <span class="value">{{item.lendCount}}</span>
              </p>
              <p class="lend-date">
                <span class="label">借款时间</span>
                <span class="value">{{item.lendDate}}</span>
              </p>
            </div>
          </li>
        </ul>
      </div>
    </div>
    <div class="no-record-content" v-else>
      <div class="img-wrap">
        <img src="../../../../../assets/imgs/img_no_data.png" width="200" height="200">
      </div>
      <p class="tip-text">暂无邀请记录</p>
    </div>
  </div>
</template>

<script>
import IHeader from '@/components/APageHeader/APageHeader'
import BScroll from 'better-scroll'

const listItem = {
  phone: 18855556660,
  lendCount: 1000,
  registerDate: '2017-08-01',
  lendDate: '2017-08-01'
}

export default {
  name: 'pageInvitationRecord',
  data () {
    return {
      people: 1,
      list: []
    }
  },
  created () {
    let list = []
    for (let i = 0; i < 10; ++i) {
      list.push(listItem)
    }
    // this.list = list
    this._initScroll()
  },
  mounted () {},
  computed: {},
  methods: {
    _initScroll () {
      if (this.list.length === 0) return

      this.$nextTick(() => {
        if (!this.scroll) {
          this.scroll = new BScroll(this.$refs.scroller, { click: true })
        } else {
          this.scroll.refresh()
        }
      })
    }
  },
  components: {
    IHeader
  }
}
</script>

<style lang="stylus" ref="stylesheet/stylus" scoped>
@import '../../../../../common/stylus/variable.styl'

.page-invitation-record
  .content
    .title
      position relative
      line-height 60px
      text-align center
      font-size 15px
      font-weight 200
      color $color-text-yellow
      &:after
        position absolute
        content ''
        left 0
        bottom 0
        right 0
        height 1px
        border-bottom 1px solid $color-grey
      .people-count
        font-size 24px
    .scroller
      position absolute
      top 118px
      left 0
      bottom 0
      width 100%
      overflow hidden
      .record-list
        .record-item
          padding 20px 13px
          font-size 0
          border-bottom 1px solid $color-grey-higher1
          &:last-child
            border-bottom none
          .label
            font-size 13px
            color $color-text-grey-higher
          .value
            font-size 13px
            color $color-text-black
          .left
            display inline-block
            margin-right 36px
            font-size 13px
            .phone
              margin-bottom 15px
              .value
                color $color-text-yellow
          .right
            display inline-block
            font-size 13px
            .lend-count
              margin-bottom 15px
  .no-record-content
    text-align center  
    .img-wrap
      margin-top 80px
    .tip-text
      font-size 15px
      color $color-text-grey-higher
</style>