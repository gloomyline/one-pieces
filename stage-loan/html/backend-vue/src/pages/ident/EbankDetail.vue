<template>
  <el-row>
    <el-col>
      <el-button type="info" style="margin-bottom: 5px" @click.native="$router.back(-1)">返回上一页</el-button>
    </el-col>

    <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">网银流水信息</div></el-col>
    <el-col :span="24"  class="ebankInfo">
      <el-col :span="3">身份证号</el-col><el-col :span="9" v-text="identityNo ? identityNo:'未知'"></el-col>
      <el-col :span="3">姓名</el-col><el-col :span="9" v-text="realName ? realName:'未知'"></el-col>
    </el-col>
    <el-col :span="24"  class="ebankInfo">
      <el-col :span="3">手机号</el-col><el-col :span="9" v-text="mobile ? mobile:'未知'"></el-col>
      <el-col :span="3">银行</el-col><el-col :span="9">{{ bankCode|bankCodeFilter }}</el-col>
    </el-col>
    <div  v-for="(card, index) in cards">  
        <el-col :span="24"  class="ebankInfo">
            <el-col :span="3">卡号</el-col><el-col :span="9" v-text="card.cardNo ? card.cardNo:'无'"></el-col>
            <el-col :span="3">账号类型</el-col><el-col :span="9" v-text="card.accType ? card.accType:'无'"></el-col>
        </el-col>
        <el-col :span="24" class="ebankInfo">
            <el-col :span="3">币种</el-col><el-col :span="9">{{ card.currency|currencyFilter }}</el-col>
            <el-col :span="3">可用余额</el-col><el-col :span="9" v-text="card.availableBalance ? card.availableBalance:'未知'"></el-col>
        </el-col>
        <el-col :span="24"  class="ebankInfo">
            <el-col :span="3">开户日期</el-col><el-col :span="9" v-text="card.openDate ? card.openDate:'未知'"></el-col>
        </el-col>
        <el-col :span="24" class="billInfo ebankInfo">
            <el-col :span="3">账单信息</el-col>
            <el-col :span="21"  class="local_content bill_content">
              <div  v-for="(bill, index) in card.bills" class="bills">
                <el-col :span="24" style="padding-top:15px;border-bottom: #bcbcbc solid 1px" v-if="index == 0">
                    <el-col :span="3">账单日期</el-col><el-col :span="9" v-text="bill.trdDate ? bill.trdDate:'未知'"></el-col>
                    <el-col :span="3">交易对方</el-col><el-col :span="9" v-text="bill.counterparty ? bill.counterparty:'未知'"></el-col>
                </el-col>
                <el-col :span="24" style="padding-top:15px;border-bottom: #bcbcbc solid 1px;border-top: #bcbcbc solid 1px" v-if="index > 0">
                    <el-col :span="3">账单日期</el-col><el-col :span="9" v-text="bill.trdDate ? bill.trdDate:'未知'"></el-col>
                    <el-col :span="3">交易对方</el-col><el-col :span="9" v-text="bill.counterparty ? bill.counterparty:'未知'"></el-col>
                </el-col>
                <el-col :span="24" style="margin: 7px 0 -5px;border-bottom: #bcbcbc solid 1px">
                    <el-col :span="3">交易金额</el-col><el-col :span="9" v-text="bill.amt ? bill.amt:'未知'"></el-col>
                    <el-col :span="3">当前余额</el-col><el-col :span="9" v-text="bill.curBal ? bill.curBal:'未知'"></el-col>
                </el-col>
                <el-col :span="24" style="margin: 7px 0 -5px;border-bottom: #bcbcbc solid 1px">
                    <el-col :span="3">交易币种</el-col><el-col :span="9">{{ bill.currency|currencyFilter }}</el-col>
                    <el-col :span="3">交易类型</el-col><el-col :span="9" v-text="bill.trdType ? bill.trdType:'未知'"></el-col>
                </el-col>
                <el-col :span="24" style="margin-top: 7px;">
                    <el-col :span="3">交易摘要</el-col><el-col :span="9" v-text="bill.remark ? bill.remark:'未知'"></el-col>
                    <el-col :span="3">交易附言</el-col><el-col :span="9" v-text="bill.summary ? bill.summary:'未知'"></el-col>
                </el-col>
              </div>
            </el-col>
        </el-col>
    </div>
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
      identityNo: '',
      realName: '',
      mobile: '',
      bankCode: '',
      cards: [{
        cardNo: '',
        accType: '',
        currency: '',
        balance: '',
        availableBalance: '',
        openDate: '',
        bills: [{
          trdDate: '',
          counterparty: '',
          amt: '',
          curBal: '',
          currency: '',
          trdType: '',
          remark: '',
          summary: '',
        }],
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
            this.identityNo = response.results.identityNo;
            this.realName = response.results.realName;
            this.mobile = response.results.mobile;
            this.bankCode = response.results.bankCode;
            this.cards = response.results.cards;
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
    bankCodeFilter(v) {
      switch (v) {
        case 'ICBC': return '工商银行';
        case 'BOC': return '中国银行';
        case 'CCB': return '建设银行';
        case 'ABC': return '农业银行';
        case 'BCM': return '交通银行';
        case 'CMB': return '招商银行';
        case 'CIB': return '兴业银行';
        case 'CGB': return '广发银行';
        case 'SPDB': return '浦发银行';
        case 'PAB': return '平安银行';
        case 'BOSC': return '上海银行';
        case 'CITIC': return '中信银行';
        case 'HXB': return '华夏银行';
        case 'CMBC': return '民生银行';
        case 'CEB': return '光大银行';
        case 'CITIBANK': return '花旗银行';
        case 'PSBC': return '邮储银行';
        default: return v;
      }
    },
    currencyFilter(v) {
      switch (v) {
        case 'CNY': return '人民币';
        case 'USD': return '美元';
        default: return '未知';
      }
    },
  },
};
</script>
<style>
  .local_content {
    margin-top:-10px;
  }
  .bill_content div.bills, .detail_content div.details {
    overflow:hidden;
  }
  .bill_content div.bills:nth-child(odd){
    background:#FAFAFA;
  }
  .bill_content div.bills:nth-child(even){
    background:#EEF1F6;
  }
  .detail_content div.details:nth-child(odd){
    background:#EEF1F6;
  }
  .detail_content div.details:nth-child(even){
    background:#FAFAFA;
  }
  .el-col-3 {
     word-wrap: break-word;
  }
  .ebankInfo {
     margin-top: 10px;
     border-bottom: #bcbcbc solid 1px;
  }
  .billInfo {
    padding-right: 0px;
  }
</style>