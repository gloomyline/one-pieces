<template>
  <el-row>
    <el-col>
      <el-button type="info" style="margin-bottom: 5px" @click.native="$router.back(-1)">返回上一页</el-button>
    </el-col>
    <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">基本信息</div></el-col>
    <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
       <el-col :span="3">会员名</el-col><el-col :span="9" v-text="basicInfo.nickName ? basicInfo.nickName:'未知'"></el-col>
       <el-col :span="3">会员等级</el-col><el-col :span="9" v-text="basicInfo.vipLevel ? basicInfo.vipLevel:'未知'"></el-col>
    </el-col>
    <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
      <el-col :span="3">手机号</el-col><el-col :span="9" v-text="basicInfo.mobileNo ? basicInfo.mobileNo:'未知'"></el-col>
      <el-col :span="3">邮箱</el-col><el-col :span="9" v-text="basicInfo.email ? basicInfo.email:'未知'"></el-col>
    </el-col>
    <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
      <el-col :span="3">真实姓名</el-col><el-col :span="9" v-text="basicInfo.realName ? basicInfo.realName:'未知'"></el-col>
      <el-col :span="3">证件号码</el-col><el-col :span="9" v-text="basicInfo.idCard ? basicInfo.idCard:'未知'"></el-col>
    </el-col>
    <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
      <el-col :span="3">成长值</el-col><el-col :span="9" v-text="basicInfo.growthValue ? basicInfo.growthValue:'未知'"></el-col>
      <el-col :span="3">安全等级</el-col><el-col :span="9" v-text="basicInfo.securityLevel ? basicInfo.securityLevel:'未知'"></el-col>
    </el-col>

    <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">绑定银行信息</div></el-col>
    <el-col :span="24" style="border-bottom: #bcbcbc solid 1px">
      <div  v-for="(bank, index) in bankInfo">  
        <el-col :span="24" style="margin: 7px 0 -5px;border-bottom: #bcbcbc solid 1px">
            <el-col :span="3">姓名</el-col><el-col :span="9" v-text="bank.name ? bank.name:'未知'"></el-col>
            <el-col :span="3">银行卡</el-col><el-col :span="9" v-text="bank.bankCardID ? bank.bankCardID:'未知'"></el-col>
        </el-col>
        <el-col :span="24" style="margin: 7px 0 -5px;">
            <el-col :span="3">银行卡类型</el-col><el-col :span="9" v-text="bank.cardType ? bank.cardType:'未知'"></el-col>
            <el-col :span="3">电话</el-col><el-col :span="9" v-text="bank.tel ? bank.tel:'未知'"></el-col>
        </el-col>
      </div>
    </el-col>

    <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">白条信息</div></el-col>
    <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
        <el-col :span="3">总额度</el-col><el-col :span="9" v-text="baiTiaoInfo.creditlimit ? baiTiaoInfo.creditlimit:'未知'"></el-col>
        <el-col :span="3">可用额度</el-col><el-col :span="9" v-text="baiTiaoInfo.availablelimit ? baiTiaoInfo.availablelimit:'未知'"></el-col>
    </el-col>
    <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
        <el-col :span="3">是否开通</el-col><el-col :span="9" v-text="baiTiaoInfo.isOpen ? baiTiaoInfo.isOpen:'未知'"></el-col>
        <el-col :span="3">月还歀</el-col><el-col :span="9" v-text="baiTiaoInfo.monthloan ? baiTiaoInfo.monthloan:'未知'"></el-col>
    </el-col>
    <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
        <el-col :span="3">白条消费</el-col><el-col :span="9" v-text="baiTiaoInfo.biaoTiaoConSum ? baiTiaoInfo.biaoTiaoConSum:'未知'"></el-col>
        <el-col :span="3">小白信用</el-col><el-col :span="9" v-text="baiTiaoInfo.xiaoBaiCreditValue ? baiTiaoInfo.xiaoBaiCreditValue:'未知'"></el-col>
    </el-col>

    <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">地址信息</div></el-col>
    <el-col :span="24" style="margin: 6px 0 -5px;border-bottom: #bcbcbc solid 1px;padding-right:0px;"  v-for="(addr, index) in addressInfo">
        <el-col :span="3">{{ addr.linkman|notNullFilter }}</el-col>
        <el-col :span="21" style="margin-top: -10px;" v-if="addr.linkman">
            <el-col :span="24" style="">
                <el-col :span="3">收货地址</el-col><el-col :span="9" v-text="addr.address ? addr.address:'无'"></el-col>
                 <el-col :span="3">电话</el-col><el-col :span="9" v-text="addr.tel ? addr.tel:'无'"></el-col>
            </el-col>
        </el-col>
    </el-col>
    <el-col :span="24" style="border-bottom: #bcbcbc solid 1px" v-if="!addressInfo[0]"><el-col :span="3">无</el-col></el-col>
    <el-col :span="24" class="hasMore">
      <el-button v-show="addressInfo.hasMore" @click.prevent="handleHasMoreAddrInfo">浏览更多...</el-button>
    </el-col>  


    <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">订单信息</div></el-col>
    <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px;padding-right:0px;"  v-for="(order, index) in orderDetail">
        <el-col :span="3">{{ order.orderDate|notNullFilter }}</el-col>
        <el-col :span="21" style="margin-top: -10px;" v-if="order.orderDate">
            <el-col :span="24" style="border-bottom: #bcbcbc solid 1px">
                <el-col :span="3">商品名称</el-col><el-col :span="9" v-text="order.goodsName ? order.goodsName:'无'"></el-col>
                <el-col :span="3">收货人地址</el-col><el-col :span="9" v-text="order.consigneeAddr ? order.consigneeAddr:'无'"></el-col>
            </el-col>
            <el-col :span="24" style="margin:5px 0 5px;border-bottom: #bcbcbc solid 1px">
                <el-col :span="3">订单时间</el-col><el-col :span="9" v-text="order.orderDate ? order.orderDate:'无'"></el-col>
                <el-col :span="3">订单金额</el-col><el-col :span="9" v-text="order.orderMoney ? order.orderMoney:'无'"></el-col>
            </el-col>
            <el-col :span="24" style="border-bottom: #bcbcbc solid 1px">
                <el-col :span="3">收货人</el-col><el-col :span="9" v-text="order.consigneePerson ? order.consigneePerson:'无'"></el-col>
                <el-col :span="3">收货电话</el-col><el-col :span="9" v-text="order.tel ? order.tel:'无'"></el-col>
            </el-col>
            <el-col :span="24" style="">
                <el-col :span="3">订单状态</el-col><el-col :span="9" v-text="order.orderStatus ? order.orderStatus:'无'"></el-col>
                <el-col :span="3">支付类型</el-col><el-col :span="9" v-text="order.payType ? order.payType:'无'"></el-col>
            </el-col>
        </el-col>
    </el-col>
    <el-col :span="24" style="border-bottom: #bcbcbc solid 1px" v-if="!orderDetail[0]"><el-col :span="3">无</el-col></el-col>
    <el-col :span="24" class="hasMore">
      <el-button v-show="orderDetail.hasMore" @click.prevent="handleHasMoreOrderInfo">浏览更多...</el-button>
    </el-col> 
</el-row>
</template>
<script>
import apiBase from '../../apiBase';
import { getJsonAndCheckSuccess, reportErrorMessage } from '../../ApiUtil';

export default {
  name: 'basicDetail',
  data() {
    return {
      id: this.$route.params.id,
      jd_order_offset: 0,
      jd_order_limit: 10,
      jd_addr_offset: 0,
      jd_addr_limit: 10,
      basicInfo: {
        nickName: '',
        vipLevel: '',
        mobileNo: '',
        email: '',
        realName: '',
        idCard: '',
        growthValue: '',
        securityLevel: '',
      },
      bankInfo: [{
        name: '',
        identityNo: '',
        gender: '',
        age: '',
        mobile: '',
        regTime: '',
        nativeAddress: '',
      }],
      baiTiaoInfo: {
        creditlimit: '',
        availablelimit: '',
        isOpen: '',
        monthloan: '',
        biaoTiaoConSum: '',
        xiaoBaiCreditValue: '',
      },
      addressInfo: [{ address: '', linkman: '', tel: '', hasMore: false }],
      orderDetail: [{ goodsName: '', consigneeAddr: '', orderDate: '', orderMoney: '', consigneePerson: '', tel: '', orderStatus: '', payType: '', hasMore: false }],
    };
  },
  watch: {
    active() {
      this.getData();
    },
  },
  mounted() {
    this.getData();
  },
  methods: {
    getData() {
      this.$http.get(`${apiBase}limu-info/${this.id}`)
        .then(getJsonAndCheckSuccess)
        .then((response) => {
          if (response.results) {
            this.basicInfo = response.results.basicInfo;
            this.bankInfo = response.results.bankInfo;
            this.baiTiaoInfo = response.results.baiTiaoInfo;
            this.addressInfo = response.results.addressInfo;
            this.orderDetail = response.results.orderDetail;
            this.addressInfo.hasMore = response.results.jd_addr_has_more;
            this.orderDetail.hasMore = response.results.jd_order_has_more;
          }
        }).catch(reportErrorMessage(this));
    },
    handleHasMoreAddrInfo() {
      const old = this.addressInfo;
      const params = {
        jd_addr_limit: this.jd_addr_limit,
        jd_addr_offset: this.jd_addr_limit + this.jd_addr_offset,
      };
      this.$http.get(`${apiBase}limu-info/${this.id}`, { params })
        .then(getJsonAndCheckSuccess)
        .then((response) => {
          if (response.results) {
            this.jd_addr_limit = params.jd_addr_limit;
            this.jd_addr_offset = params.jd_addr_offset;
            this.addressInfo = old.concat(response.results.addressInfo);
            this.addressInfo.hasMore = response.results.jd_addr_has_more;
          }
        }).catch(reportErrorMessage(this));
    },
    handleHasMoreOrderInfo() {
      const old = this.orderDetail;
      const params = {
        jd_order_limit: this.jd_order_limit,
        jd_order_offset: this.jd_order_limit + this.jd_order_offset,
      };
      this.$http.get(`${apiBase}limu-info/${this.id}`, { params })
       .then(getJsonAndCheckSuccess)
       .then((response) => {
         if (response.results) {
           this.jd_order_limit = params.jd_order_limit;
           this.jd_order_offset = params.jd_order_offset;
           this.orderDetail = old.concat(response.results.orderDetail);
           this.orderDetail.hasMore = response.results.jd_order_has_more;
         }
       }).catch(reportErrorMessage(this));
    },
  },
  filters: {
    notNullFilter(v) {
      if (v) {
        return v;
      }
      return '无';
    },
  },
};
</script>