<template>
  <el-row>
    <el-col>
      <el-button type="info" style="margin-bottom: 5px" @click.native="$router.back(-1)">返回上一页</el-button>
    </el-col>
    <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">用户基本信息</div></el-col>
    <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
       <el-col :span="3">用户名</el-col><el-col :span="9" v-text="basicInfo.username ? basicInfo.username:'未知'"></el-col>
       <el-col :span="3">昵称</el-col><el-col :span="9" v-text="basicInfo.nickName ? basicInfo.nickName:'未知'"></el-col>
    </el-col>
    <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
      <el-col :span="3">性别</el-col><el-col :span="9" v-text="basicInfo.gender ? basicInfo.gender:'未知'"></el-col>
      <el-col :span="3">出生日期</el-col><el-col :span="9" v-text="basicInfo.birthday ? basicInfo.birthday:'未知'"></el-col>
    </el-col>
    <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
      <el-col :span="3">真实姓名</el-col><el-col :span="9" v-text="basicInfo.realName ? basicInfo.realName:'未知'"></el-col>
      <el-col :span="3">身份证</el-col><el-col :span="9" v-text="basicInfo.identityNo ? basicInfo.identityNo:'未知'"></el-col>
    </el-col>
    <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
      <el-col :span="3">认证渠道</el-col><el-col :span="9" v-text="basicInfo.identityChannel ? basicInfo.identityChannel:'未知'"></el-col>
      <el-col :span="3">是否实名认证</el-col><el-col :span="9" v-text="basicInfo.identityStatus ? basicInfo.identityStatus:'未知'"></el-col>
    </el-col>
    <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
      <el-col :span="3">登录邮箱</el-col><el-col :span="9" v-text="basicInfo.email ? basicInfo.email:'未知'"></el-col>
      <el-col :span="3">绑定手机</el-col><el-col :span="9" v-text="basicInfo.mobile ? basicInfo.mobile:'未知'"></el-col>
    </el-col>
    <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
      <el-col :span="3">会员等级</el-col><el-col :span="9" v-text="basicInfo.vipLevel ? basicInfo.vipLevel:'未知'"></el-col>
      <el-col :span="3">成长值</el-col><el-col :span="9" v-text="basicInfo.growthValue ? basicInfo.growthValue:'未知'"></el-col>
    </el-col>
    <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
      <el-col :span="3">信用积分</el-col><el-col :span="9" v-text="basicInfo.creditScore ? basicInfo.creditScore:'未知'"></el-col>
      <el-col :span="3">好评率</el-col><el-col :span="9" v-text="basicInfo.favorableRate ? basicInfo.favorableRate:'未知'"></el-col>
    </el-col>
    <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
      <el-col :span="3">安全等级</el-col><el-col :span="9" v-text="basicInfo.securityLevel ? basicInfo.securityLevel:'未知'"></el-col>
    </el-col>

    <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">绑定的支付宝信息</div></el-col>
    <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
        <el-col :span="3">支付账户名</el-col><el-col :span="9" v-text="alipayInfo.username ? alipayInfo.username:'未知'"></el-col>
        <el-col :span="3">绑定邮箱</el-col><el-col :span="9" v-text="alipayInfo.email ? alipayInfo.email:'未知'"></el-col>
    </el-col>
    <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
        <el-col :span="3">绑定手机</el-col><el-col :span="9" v-text="alipayInfo.mobile ? alipayInfo.mobile:'未知'"></el-col>
        <el-col :span="3">实名认证姓名</el-col><el-col :span="9" v-text="alipayInfo.realName ? alipayInfo.realName:'未知'"></el-col>
    </el-col>
    <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
        <el-col :span="3">支付认证身份证号</el-col><el-col :span="9" v-text="alipayInfo.identityNo ? alipayInfo.identityNo:'未知'"></el-col>
        <el-col :span="3">实名认证状态</el-col><el-col :span="9" v-text="alipayInfo.identityStatus ? alipayInfo.identityStatus:'未知'"></el-col>
    </el-col>
    <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
        <el-col :span="3">账户余额</el-col><el-col :span="9" v-text="alipayInfo.accBal ? alipayInfo.accBal:'未知'"></el-col>
        <el-col :span="3">余额宝余额</el-col><el-col :span="9" v-text="alipayInfo.yuebaoBal ? alipayInfo.yuebaoBal:'未知'"></el-col>
    </el-col>
    <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
        <el-col :span="3">余额宝历史累计收益</el-col><el-col :span="9" v-text="alipayInfo.yuebaoHisIncome ? alipayInfo.yuebaoHisIncome:'未知'"></el-col>
        <el-col :span="3">花呗消费额度</el-col><el-col :span="9" v-text="alipayInfo.huabeiLimit ? alipayInfo.huabeiLimit:'未知'"></el-col>
    </el-col>
    <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
        <el-col :span="3">花呗可用额度</el-col><el-col :span="9" v-text="alipayInfo.huabeiAvailableLimit ? alipayInfo.huabeiAvailableLimit:'未知'"></el-col>
    </el-col>

    <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">收货地址</div></el-col>
    <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px;padding-right:0px;"  v-for="(addr, index) in addresses">
        <el-col :span="3">{{ addr.name|notNullFilter }}</el-col>
        <el-col :span="21" style="margin-top: -10px;" v-if="addr.name">
            <el-col :span="24" style="border-bottom: #bcbcbc solid 1px">
                <el-col :span="3">收货地址</el-col><el-col :span="9" v-text="addr.address ? addr.address:'无'"></el-col>
                 <el-col :span="3">收货联系手机号或电话</el-col><el-col :span="9" v-text="addr.mobile ? addr.mobile:'无'"></el-col>
            </el-col>
            <el-col :span="24" style="">
                <el-col :span="3">邮编</el-col><el-col :span="9" v-text="addr.zipCode ? addr.zipCode:'无'"></el-col>
                 <el-col :span="3">是否为默认收货地址</el-col><el-col :span="9" v-text="addr.defaultAddr ? addr.defaultAddr:'无'"></el-col>
            </el-col>
        </el-col>
    </el-col>
    <el-col :span="24" style="border-bottom: #bcbcbc solid 1px" v-if="!addresses[0]"><el-col :span="3">无</el-col></el-col>
    <el-col :span="24" class="hasMore">
      <el-button v-show="addresses.hasMore" @click.prevent="handleHasMoreAddrInfo">浏览更多...</el-button>
    </el-col>  


    <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">订单信息</div></el-col>
    <el-col :span="24" style="border-bottom: #bcbcbc solid 1px;padding-right:0px;"  v-for="(order, index) in orderDetails">
        <el-col :span="3">{{ order.orderId|notNullFilter }}</el-col>
        <el-col :span="21" style="margin-top: -8px;" v-if="order.orderId">
            <el-col :span="24" style="">
                <el-col :span="3">订单号</el-col><el-col :span="9" v-text="order.orderId ? order.orderId:'无'"></el-col>
                <el-col :span="3">订单时间</el-col><el-col :span="9" v-text="order.orderTime ? order.orderTime:'无'"></el-col>
            </el-col>
            <el-col :span="24" style="margin-top: 10px;border-top: #bcbcbc solid 1px">
                <el-col :span="3">订单金额</el-col><el-col :span="9" v-text="order.orderAmt ? order.orderAmt:'无'"></el-col>
                <el-col :span="3">订单状态</el-col><el-col :span="9" v-text="order.orderStatus ? order.orderStatus:'无'"></el-col>
            </el-col>
            <el-col :span="24" style="margin-top: 10px;border-top: #bcbcbc solid 1px;padding-right:0px;" v-if="order.items">
                 <el-col :span="3">商品信息</el-col>
                 <el-col :span="21" style="margin-top: -18px;">
                    <div v-for="(item, index) in order.items">
                      <el-col :span="24" style="margin-top: 10px;" v-if="index==0">
                           <el-col :span="3">商品名称</el-col><el-col :span="21" v-text="item.itemName ? item.itemName:'无'"></el-col>
                      </el-col>
                      <el-col :span="24" style="margin-top: 10px;border-top: #bcbcbc solid 1px" v-if="index>0">
                           <el-col :span="3">商品名称</el-col><el-col :span="21" v-text="item.itemName ? item.itemName:'无'"></el-col>
                      </el-col>
                      <el-col :span="24" style="margin-top: 10px;border-top: #bcbcbc solid 1px">
                          <el-col :span="3">商品ID</el-col><el-col :span="9" v-text="item.itemId ? item.itemId:'无'"></el-col>
                          <el-col :span="3">商品 Url</el-col><el-col :span="9" v-text="item.itemUrl ? item.itemUrl:'无'"></el-col>
                      </el-col>
                      <el-col :span="24" style="margin-top: 10px;border-top: #bcbcbc solid 1px">
                           <el-col :span="3">商品单价</el-col><el-col :span="9" v-text="item.itemPrice ? item.itemPrice:'无'"></el-col>
                           <el-col :span="3">商品数量</el-col><el-col :span="9" v-text="item.itemQuantity ? item.itemQuantity:'无'"></el-col>
                      </el-col>
                    </div>
                 </el-col>
            </el-col>
            <el-col :span="24" style="margin-top: 10px;border-top: #bcbcbc solid 1px;padding-right:0px;" v-if="order.logisticsInfo">
                 <el-col :span="3">物流信息</el-col>
                 <el-col :span="21" style="margin-top: -10px;">
                    <el-col :span="24" style="">
                        <el-col :span="3">运送方式</el-col><el-col :span="9" v-text="order.logisticsInfo.deliverType ? order.logisticsInfo.deliverType:'无'"></el-col>
                         <el-col :span="3">物流公司</el-col><el-col :span="9" v-text="order.logisticsInfo.deliverCompany ? order.logisticsInfo.deliverCompany:'无'"></el-col>
                    </el-col>
                    <el-col :span="24" style="margin-top:10px;border-top: #bcbcbc solid 1px">
                        <el-col :span="3">送货单号{{ ind }}</el-col><el-col :span="9" v-text="order.logisticsInfo.deliverId ? order.logisticsInfo.deliverId:'无'"></el-col>
                         <el-col :span="3">收货人姓名</el-col><el-col :span="9" v-text="order.logisticsInfo.receivePersonName ? order.logisticsInfo.receivePersonName:'无'"></el-col>
                    </el-col>
                    <el-col :span="24" style="margin-top:10px;border-top: #bcbcbc solid 1px">
                        <el-col :span="3">收货人联系电话</el-col><el-col :span="9" v-text="order.logisticsInfo.receivePersonMobile ? order.logisticsInfo.receivePersonMobile:'无'"></el-col>
                        <el-col :span="3">收货地址</el-col><el-col :span="9" v-text="order.logisticsInfo.receiveAddress ? order.logisticsInfo.receiveAddress:'无'"></el-col>
                    </el-col>
                 </el-col>
            </el-col>
        </el-col>
    </el-col>
    <el-col :span="24" style="border-bottom: #bcbcbc solid 1px" v-if="!orderDetails[0]"><el-col :span="3">无</el-col></el-col>
    <el-col :span="24" class="hasMore">
      <el-button v-show="orderDetails.hasMore" @click.prevent="handleHasMoreOrderInfo">浏览更多...</el-button>
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
      tb_order_offset: 0,
      tb_order_limit: 10,
      tb_addr_offset: 0,
      tb_addr_limit: 10,
      basicInfo: {
        username: '',
        nickName: '',
        gender: '',
        birthday: '',
        realName: '',
        identityNo: '',
        identityChannel: '',
        identityStatus: '',
        email: '',
        mobile: '',
        vipLevel: '',
        growthValue: '',
        creditScore: '',
        favorableRate: '',
        securityLevel: '',
      },
      alipayInfo: {
        username: '',
        email: '',
        mobile: '',
        realName: '',
        identityNo: '',
        identityStatus: '',
        accBal: '',
        yuebaoBal: '',
        yuebaoHisIncome: '',
        huabeiLimit: '',
        huabeiAvailableLimit: '',
      },
      addresses: [{ name: '', address: '', mobile: '', zipCode: '', defaultAddr: '', hasMore: false }],
      orderDetails: [{
        hasMore: false,
        orderId: '',
        orderTime: '',
        orderAmt: '',
        orderStatus: '',
        items: [{
          itemId: '',
          itemName: '',
          itemUrl: '',
          itemPrice: '',
          itemQuantity: '',
        }],
        logisticsInfo: {
          deliverType: '',
          deliverCompany: '',
          deliverId: '',
          receivePersonName: '',
          receivePersonMobile: '',
          receiveAddress: '',
        },
      }],
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
            this.alipayInfo = response.results.alipayInfo;
            this.addresses = response.results.addresses;
            this.orderDetails = response.results.orderDetails;
            this.addresses.hasMore = response.results.tb_addr_has_more;
            this.orderDetails.hasMore = response.results.tb_order_has_more;
          }
        }).catch(reportErrorMessage(this));
    },
    handleHasMoreAddrInfo() {
      const old = this.addresses;
      const params = {
        tb_addr_limit: this.tb_addr_limit,
        tb_addr_offset: this.tb_addr_limit + this.tb_addr_offset,
      };
      this.$http.get(`${apiBase}limu-info/${this.id}`, { params })
        .then(getJsonAndCheckSuccess)
        .then((response) => {
          if (response.results) {
            this.tb_addr_limit = params.tb_addr_limit;
            this.tb_addr_offset = params.tb_addr_offset;
            this.addresses = old.concat(response.results.addresses);
            this.addresses.hasMore = response.results.tb_addr_has_more;
          }
        }).catch(reportErrorMessage(this));
    },
    handleHasMoreOrderInfo() {
      const old = this.orderDetails;
      const params = {
        tb_order_limit: this.tb_order_limit,
        tb_order_offset: this.tb_order_limit + this.tb_order_offset,
      };
      this.$http.get(`${apiBase}limu-info/${this.id}`, { params })
       .then(getJsonAndCheckSuccess)
       .then((response) => {
         if (response.results) {
           this.tb_order_limit = params.tb_order_limit;
           this.tb_order_offset = params.tb_order_offset;
           this.orderDetails = old.concat(response.results.orderDetails);
           this.orderDetails.hasMore = response.results.tb_order_has_more;
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