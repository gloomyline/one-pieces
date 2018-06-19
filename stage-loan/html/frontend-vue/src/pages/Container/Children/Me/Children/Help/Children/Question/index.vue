<!--
@Date:   2018-01-02T11:03:22+08:00
@Last modified time: 2018-01-10T16:54:18+08:00
-->
<template lang="html">
  <div class="page-common-question">
    <q-header class="que-header" :not-has-confirm="true">
      <span class="title" slot="title-text">常见问题</span>
    </q-header>
    <group class="que-content" gutter="0">
      <cell class="times-apply" title="分期申请" is-link
        :arrow-direction="isTimesShow? 'up' : 'down'"
        @click.native="clickHandle($event)"></cell>
      <ol class="times-apply-details list" :class="isTimesShow? 'animate': ''">
        <li class="times-item item">前往合作的商户，确认您所需要消费的项目及金额；</li>
        <li class="times-item item">扫一扫专属二维码；</li>
        <li class="times-item item">填写个人资料和分期需求，提交并进入审核；</li>
        <li class="times-item item">审核通过后，我们将拨打您本人电话确认相关信息；</li>
        <li class="times-item item">确认成功，立即放款。</li>
      </ol>
      <cell class="bill-refund" title="账单还款" is-link
        :arrow-direction="isBillShow? 'up' : 'down'"
        @click.native="clickHandle($event)"></cell>
      <ol class="bill-refund-details list" :class="isBillShow? 'animate' : ''">
        <li class="bill-item item">点击【我的】，进入【我要还款】，就可查看您的还款信息，并进行还款操作；</li>
        <li class="bill-item item">提前结清的话，目前只支持提前全部结清，您需要支付提前结清手续费，手续费收取已约定为准</li>
        <li class="bill-item item">逾期还款需要您缴纳一定的罚息和违约金，长期逾期会对您造成非常恶劣的影响，除向您收取罚息及违约金外，还会采取合法催收措施，同时也会对您的信用记录造成负面影响，失信名单将作为社会资源，对升学、就业、信贷乃至生产经营产生影响，请您务必予以重视，及时还款。</li>
      </ol>
      <cell class="account-and-safety" title="账号与安全" is-link
        :arrow-direction="isSafetyShow? 'up' : 'down'"
        @click.native="clickHandle($event)"></cell>
      <ol class="safety-details list" :class="isSafetyShow? 'animate' : ''">
        <li class="safety-item item">手机号运营商认证过程中，收不到动态验证码短信的原因？如收不到短信验证码，您需要检查一下手机是否欠费；如未欠费您可以登入相应的运营商官网进行激活认证（仅需登入即可），中国移动;中国联通;中国电信；</li>
        <li class="safety-item item">为什么会绑卡失败？绑卡验证失败一般是因为您填写的信息与银行预留的信息不符，请您检查填写的姓名、卡号、身份证号、银行预留的手机号是否与银行预留一致，或换卡再试。</li>
      </ol>
      <cell class="apply-refund" title="退款" is-link
        :arrow-direction="isRefundShow? 'up' : 'down'"
        @click.native="clickHandle($event)"></cell>
      <ol class="refund-details list" :class="isRefundShow? 'animate' : ''">
        <li class="refund-item item">退款的流程？如需退款请您尽快与商户进行协商，商户确认退款后，会自行发起退款流程。您仅需要查看订单状态（状态为退款完成则视为处理结束）；</li>
        <li class="refund-item item">退款处理需要多久？从商户发起退款后预计7个工作日内完成退款处理。</li>
      </ol>
    </group>
  </div>
</template>

<script>
import QHeader from '@/components/APageHeader/APageHeader'
import { Group, Cell } from 'vux'

export default {
  data () {
    return {
      isTimesShow: false,
      isBillShow: false,
      isSafetyShow: false,
      isRefundShow: false
    }
  },
  created () {
    this.showArr = ['isTimesShow', 'isBillShow', 'isSafetyShow', 'isRefundShow']
    this.targetArr = ['times-apply', 'bill-refund', 'account-and-safety', 'apply-refund']
  },
  methods: {
    clickHandle (event) {
      let target = event.currentTarget
      for (let i = 0; i < this.targetArr.length; ++i) {
        let item = this.targetArr[i]
        if ((new RegExp(item)).test(target.className)) {
          this[this.showArr[i]] = !this[this.showArr[i]]
        } else {
          this[this.showArr[i]] = false
        }
      }
    }
  },
  components: {
    QHeader,
    Group,
    Cell
  }
}
</script>

<style lang="stylus">
@import '../../../../../../../../common/stylus/mixin.styl'
@import '../../../../../../../../common/stylus/variable.styl'

.page-common-question
  .que-header
    border-1px($color-border-grey-light, 'bottom')
  .que-content
    .list
      max-height: 0
      padding-left: 32px
      padding-right: 16px
      overflow: hidden
      transition: max-height .8s cubic-bezier(0, 1, 0, 1) -.1s
      &.animate
        max-height: 9999px
        transition-timing-function: cubic-bezier(.5, 0, 1, 0)
        transition-delay: 0
      .item
        list-style-type: decimal
</style>
