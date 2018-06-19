<template>
    <el-row>
        <el-col>
            <el-button type="info" style="margin-bottom: 5px" @click.native="$router.back(-1)">返回上一页</el-button>
        </el-col>
        <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">基本信息</div></el-col>
        <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
            <el-col :span="3">公积金账号</el-col><el-col :span="9" v-text="houseFund.accNo ? houseFund.accNo:'未知'"></el-col>
            <el-col :span="3">姓名</el-col><el-col :span="9" v-text="houseFund.name ? houseFund.name:'未知'"></el-col>
        </el-col>
        <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
            <el-col :span="3">身份证号</el-col><el-col :span="9" v-text="houseFund.identityNo ? houseFund.identityNo:'未知'"></el-col>
            <el-col :span="3">单位名称</el-col><el-col :span="9" v-text="houseFund.corpName ? houseFund.corpName:'未知'"></el-col>
        </el-col>
        <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
            <el-col :span="3">账户余额</el-col><el-col :span="9" v-text="houseFund.bal ? houseFund.bal:'未知'"></el-col>
            <el-col :span="3">月缴存</el-col><el-col :span="9" v-text="houseFund.monthlyDeposit ? houseFund.monthlyDeposit:'未知'"></el-col>
        </el-col>
        <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
            <el-col :span="3">缴存基数</el-col><el-col :span="9" v-text="houseFund.baseDeposit ? houseFund.baseDeposit:'未知'"></el-col>
            <el-col :span="3">末次缴存年月</el-col><el-col :span="9" v-text="houseFund.lastDepostDate ? houseFund.lastDepostDate:'未知'"></el-col>
        </el-col>
        <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
            <el-col :span="3">单位缴存比例</el-col><el-col :span="9" v-text="houseFund.unitMonthlyScale ? houseFund.unitMonthlyScale:'未知'"></el-col>
            <el-col :span="3">个人缴存比例</el-col><el-col :span="9" v-text="houseFund.personMonthlyScale ? houseFund.personMonthlyScale:'未知'"></el-col>
        </el-col>
        <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
            <el-col :span="3">账号状态</el-col><el-col :span="9" v-text="houseFund.accStatus ? houseFund.accStatus:'未知'"></el-col>
            <el-col :span="3">开户日期</el-col><el-col :span="9" v-text="houseFund.openDate ? houseFund.openDate:'未知'"></el-col>
        </el-col>
        <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
            <el-col :span="3">地区</el-col><el-col :span="9" v-text="area"></el-col>
        </el-col>

        <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">贷款信息</div></el-col>
        <el-col style="padding: 0" v-if="houseFund.loanInfo">
            <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px" >
                <el-col :span="3">贷款账号</el-col><el-col :span="9" v-text="houseFund.loanInfo.loadAccNo ? houseFund.loanInfo.loadAccNo:'未知'"></el-col>
                <el-col :span="3">贷款期限(月)</el-col><el-col :span="9" v-text="houseFund.loanInfo.loadLimit ? houseFund.loanInfo.loadLimit:'未知'"></el-col>
            </el-col>
            <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
                <el-col :span="3">贷款总额</el-col><el-col :span="9" v-text="houseFund.loanInfo.loadAll ? houseFund.loanInfo.loadAll:'未知'"></el-col>
                <el-col :span="3">贷款余额</el-col><el-col :span="9" v-text="houseFund.loanInfo.loadBal ? houseFund.loanInfo.loadBal:'未知'"></el-col>
            </el-col>
            <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
                <el-col :span="3">还款方式</el-col><el-col :span="9" v-text="houseFund.loanInfo.paymentMethod ? houseFund.loanInfo.paymentMethod:'未知'"></el-col>
                <el-col :span="3">末次还款年月</el-col><el-col :span="9" v-text="houseFund.loanInfo.lastPaymentDate ? houseFund.loanInfo.lastPaymentDate:'未知'"></el-col>
            </el-col>
            <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
                <el-col :span="3">贷款状态</el-col><el-col :span="9" v-text="houseFund.loanInfo.loadStatus ? houseFund.loanInfo.loadStatus:'未知'"></el-col>
                <el-col :span="3">开户日期</el-col><el-col :span="9" v-text="houseFund.loanInfo.openDate ? houseFund.loanInfo.openDate:'未知'"></el-col>
            </el-col>
        </el-col>
        <el-col style="" v-if="!houseFund.loanInfo">暂无贷款信息</el-col>

        <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">明细信息</div></el-col>
        <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px;padding-right:0px;"  v-for="(item, index) in houseFund.details">
            <el-col :span="3">{{ item.accDate|notNullFilter }}</el-col>
            <el-col :span="21" style="margin-top: -10px;" v-if="item.accDate">
                <el-col :span="24" style="border-bottom: #bcbcbc solid 1px">
                    <el-col :span="3">单位名称</el-col><el-col :span="9" v-text="item.corpName ? item.corpName:'无'"></el-col>
                    <el-col :span="3">金额</el-col><el-col :span="9" v-text="item.amt ? item.amt:'无'"></el-col>
                </el-col>
                <el-col :span="24" style="margin:5px 0 5px;border-bottom: #bcbcbc solid 1px">
                    <el-col :span="3">余额</el-col><el-col :span="9" v-text="item.bal ? item.bal:'无'"></el-col>
                    <el-col :span="3">缴存基数</el-col><el-col :span="9" v-text="item.baseDeposit ? item.baseDeposit :'无'"></el-col>
                </el-col>
                <el-col :span="24" style="border-bottom: #bcbcbc solid 1px">
                    <el-col :span="3">缴费年月</el-col><el-col :span="9" v-text="item.payMonth ? item.payMonth:'无'"></el-col>
                    <el-col :span="3">业务描述</el-col><el-col :span="9" v-text="item.bizDesc ? item.bizDesc:'无'"></el-col>
                </el-col>
            </el-col>
        </el-col>
        <el-col :span="24" style="border-bottom: #bcbcbc solid 1px" v-if="!houseFund.details[0]"><el-col :span="3">无</el-col></el-col>
      <!--<el-col :span="24" class="hasMore">
           <el-button v-show="orderDetail.hasMore" @click.prevent="handleHasMoreOrderInfo">浏览更多...</el-button>
       </el-col>-->
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
        houseFund: {
          accNo: '',
          name: '',
          identityNo: '',
          corpName: '',
          bal: 0,
          monthlyDeposit: 0,
          baseDeposit: 0,
          lastDepostDate: 0,
          unitMonthlyScale: 0,
          personMonthlyScale: 0,
          accStatus: '',
          openDate: '',
          area: '',
          details: [],
          loanInfo: {
            loadAccNo: '',
            loadLimit: '',
            loadAll: '',
            loadBal: '',
            paymentMethod: '',
            lastPaymentDate: '',
            loadStatus: '',
            openDate: '',
          },
        },
        area: '',
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
              this.houseFund = response.results;
              this.areaFormatter(response.results.area);
            }
          }).catch(reportErrorMessage(this));
      },
    /*  handleHasMoreAddrInfo() {
        this.$http.get(`${apiBase}limu-info/${this.id}`, { params })
          .then(getJsonAndCheckSuccess)
          .then((response) => {
            if (response.results) {
              this.addressInfo = old.concat(response.results.addressInfo);
              this.addressInfo.hasMore = response.results.jd_addr_has_more;
            }
          }).catch(reportErrorMessage(this));
      },*/
     /* handleHasMoreOrderInfo() {
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
      },*/
      areaFormatter(v) {
        this.$http.get(`${apiBase}area/${v}`)
          .then(response => response.json())
          .then((json) => {
            if (json.status === 'SUCCESS') {
              this.area = json.results;
            }
          }).catch(() => {
            this.$message('系统错误');
          });
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