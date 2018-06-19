<template>
  <el-row>
    <el-col>
      <el-button type="info" style="margin-bottom: 5px" @click.native="$router.back(-1)">返回上一页</el-button>
    </el-col>

    <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">银行卡账单信息</div></el-col>
    <el-col :span="24">
      <div  v-for="(card, index) in cards">  
        <el-col :span="24" style="margin: 7px 0 -5px;border-bottom: #bcbcbc solid 1px;padding-right:0px;">
            <el-col :span="3" v-text="card.cardNo ? card.cardNo : '未知卡号'"></el-col>
            <el-col :span="21" class="local_content">
                <el-col :span="24" style="margin: 7px 0 -5px;border-bottom: #bcbcbc solid 1px">
                    <el-col :span="3">卡号</el-col><el-col :span="9" v-text="card.cardNo ? card.cardNo:'未知'"></el-col>
                    <el-col :span="3">姓名</el-col><el-col :span="9" v-text="card.realName ? card.realName:'未知'"></el-col>
                </el-col>
                <el-col :span="24" style="margin: 7px 0 -5px;border-bottom: #bcbcbc solid 1px">
                    <el-col :span="3">清算币种</el-col><el-col :span="9" v-text="card.currency ? card.currency:'未知'"></el-col>
                    <el-col :span="3">信用额度</el-col><el-col :span="9" v-text="card.creditLimit ? card.creditLimit:'未知'"></el-col>
                </el-col>

                <el-col :span="24" style="margin: 7px 0 -5px;border-bottom: #bcbcbc solid 1px">
                    <el-col :span="3">可取现额度</el-col><el-col :span="9" v-text="card.withdrawalLimit ? card.withdrawalLimit:'未知'"></el-col>
                    <el-col :span="3">所属银行</el-col><el-col :span="9" v-text="card.bankCode ? card.bankCode:'未知'"></el-col>
                </el-col>
                <el-col :span="24" class="billInfo" style="margin: 7px 0 -5px;">
                    <el-col :span="3">账单信息</el-col>
                    <el-col :span="21"  class="local_content bill_content">
                      <div  v-for="(bill, index) in card.bills" class="bills">
                        <el-col :span="24" style="padding-top:15px;border-bottom: #bcbcbc solid 1px" v-if="index == 0">
                            <el-col :span="3">账单月份</el-col><el-col :span="9" v-text="bill.statementMonth ? bill.statementMonth:'未知'"></el-col>
                            <el-col :span="3">账单开始日期</el-col><el-col :span="9" v-text="bill.statementStartDate ? bill.statementStartDate:'未知'"></el-col>
                        </el-col>
                        <el-col :span="24" style="padding-top:15px;border-bottom: #bcbcbc solid 1px;border-top: #bcbcbc solid 1px" v-if="index > 0">
                            <el-col :span="3">账单月份</el-col><el-col :span="9" v-text="bill.statementMonth ? bill.statementMonth:'未知'"></el-col>
                            <el-col :span="3">账单开始日期</el-col><el-col :span="9" v-text="bill.statementStartDate ? bill.statementStartDate:'未知'"></el-col>
                        </el-col>
                        <el-col :span="24" style="margin: 7px 0 -5px;border-bottom: #bcbcbc solid 1px">
                            <el-col :span="3">账单截止日期</el-col><el-col :span="9" v-text="bill.statementEndDate ? bill.statementEndDate:'未知'"></el-col>
                            <el-col :span="3">到期还款日</el-col><el-col :span="9" v-text="bill.paymentDueDate ? bill.paymentDueDate:'未知'"></el-col>
                        </el-col>
                        <el-col :span="24" style="margin: 7px 0 -5px;border-bottom: #bcbcbc solid 1px">
                            <el-col :span="3">总账信息</el-col>
                            <el-col :span="21" class="local_content">
                                <el-col :span="24" style="margin: 7px 0 -5px;border-bottom: #bcbcbc solid 1px">
                                    <el-col :span="3">本期应还金额</el-col><el-col :span="9" v-text="bill.generalLedgerInfo.curPaymentBal ? bill.generalLedgerInfo.curPaymentBal:'未知'"></el-col>
                                    <el-col :span="3">最低还款金额</el-col><el-col :span="9" v-text="bill.generalLedgerInfo.minPaymentBal ? bill.generalLedgerInfo.minPaymentBal:'未知'"></el-col>
                                </el-col>
                                <el-col :span="24" style="margin: 7px 0 -5px;">
                                    <el-col :span="3">信用额度</el-col><el-col :span="9" v-text="bill.generalLedgerInfo.creditLimit ? bill.generalLedgerInfo.creditLimit:'未知'"></el-col>
                                    <el-col :span="3">可取现金额</el-col><el-col :span="9" v-text="bill.generalLedgerInfo.withdrawalLimit ? bill.generalLedgerInfo.withdrawalLimit:'未知'"></el-col>
                                </el-col>
                            </el-col>
                        </el-col>
                        <el-col :span="24" style="margin: 7px 0 -5px;border-bottom: #bcbcbc solid 1px;padding-right:0px;">
                            <el-col :span="3">账户变动信息</el-col>
                            <el-col :span="21" class="local_content">
                              <div v-for="(accountChange, acindex) in bill.accountChangeInfos">
                                <el-col :span="24" style="margin: 7px 0 -5px;border-bottom: #bcbcbc solid 1px">
                                    <el-col :span="3">卡号</el-col><el-col :span="9" v-text="accountChange.cardNo ? accountChange.cardNo:'未知'"></el-col>
                                    <el-col :span="3">本期账单金额</el-col><el-col :span="9" v-text="accountChange.curStatementBal ? accountChange.curStatementBal:'未知'"></el-col>
                                </el-col>
                                <el-col :span="24" style="margin: 7px 0 -5px;border-bottom: #bcbcbc solid 1px">
                                    <el-col :span="3">本期消费金额</el-col><el-col :span="9" v-text="accountChange.curDebitsBal ? accountChange.curDebitsBal:'未知'"></el-col>
                                    <el-col :span="3">上期账单金额</el-col><el-col :span="9" v-text="accountChange.preStatementBal ? accountChange.preStatementBal:'未知'"></el-col>
                                </el-col>
                                <el-col :span="24" style="margin: 7px 0 -5px;border-bottom: #bcbcbc solid 1px">
                                    <el-col :span="3">上期已还款金额</el-col><el-col :span="9" v-text="accountChange.prePaymentBal ? accountChange.prePaymentBal:'未知'"></el-col>
                                    <el-col :span="3">本期调整金额</el-col><el-col :span="9" v-text="accountChange.curAdjustmentBal ? accountChange.curAdjustmentBal:'未知'"></el-col>
                                </el-col>
                                <el-col :span="24" style="margin: 7px 0 -5px;">
                                    <el-col :span="3">循环利息</el-col><el-col :span="9" v-text="accountChange.cycleInterest ? accountChange.cycleInterest:'未知'"></el-col>
                                </el-col>
                              </div>
                            </el-col>
                        </el-col>
                        <el-col :span="24" style="margin-top: 7px;border-bottom: #bcbcbc solid 1px;padding-right:0px;">
                            <el-col :span="3">账单明细</el-col>
                            <el-col :span="21" class="local_content detail_content">
                              <div class="details" v-for="(detail, detailIndex) in bill.details">
                                <el-col :span="24" style="" v-if="detailIndex == 0">
                                    <el-col :span="3">交易日期</el-col><el-col :span="9" v-text="detail.trdDate ? detail.trdDate:'未知'"></el-col>
                                    <el-col :span="3">记账日期</el-col><el-col :span="9" v-text="detail.accDate ? detail.accDate:'未知'"></el-col>
                                </el-col>
                                <el-col :span="24" style="border-top: #bcbcbc solid 1px" v-if="detailIndex > 0">
                                    <el-col :span="3">交易日期</el-col><el-col :span="9" v-text="detail.trdDate ? detail.trdDate:'未知'"></el-col>
                                    <el-col :span="3">记账日期</el-col><el-col :span="9" v-text="detail.accDate ? detail.accDate:'未知'"></el-col>
                                </el-col>
                                <el-col :span="24" style="margin-top: 7px;border-top: #bcbcbc solid 1px">
                                    <el-col :span="3">卡号尾数</el-col><el-col :span="9" v-text="detail.cardNo ? detail.cardNo:'未知'"></el-col>
                                    <el-col :span="3">交易金额</el-col><el-col :span="9" v-text="detail.amt ? detail.amt:'未知'"></el-col>
                                </el-col>
                                <el-col :span="24" style="margin-top: 7px;border-top: #bcbcbc solid 1px">
                                    <el-col :span="3">交易币种</el-col><el-col :span="9" v-text="detail.currency ? detail.currency:'未知'"></el-col>
                                    <el-col :span="3">入账金额</el-col><el-col :span="9" v-text="detail.accAmt ? detail.accAmt:'未知'"></el-col>
                                </el-col>
                                <el-col :span="24" style="margin-top: 7px;border-top: #bcbcbc solid 1px">
                                    <el-col :span="3">交易备注</el-col><el-col :span="9" v-text="detail.summary ? detail.summary:'未知'"></el-col>
                                </el-col>
                              </div>
                            </el-col>
                        </el-col>
                        <el-col :span="24" style="margin-top: 7px;">
                            <el-col :span="3">账单分期</el-col>
                            <el-col :span="21" class="local_content detail_content">
                              <div class="details" v-for="(installment, installmentIndex) in bill.installments">
                                <el-col :span="24" v-if="installmentIndex == 0">
                                    <el-col :span="3">卡号</el-col><el-col :span="9" v-text="installment.cardNo ? installment.cardNo:'未知'"></el-col>
                                    <el-col :span="3">分期类型</el-col><el-col :span="9" v-text="installment.instalmentType ? installment.instalmentType:'未知'"></el-col>
                                </el-col>
                                <el-col :span="24" style="border-top: #bcbcbc solid 1px" v-if="installmentIndex > 0">
                                    <el-col :span="3">卡号</el-col><el-col :span="9" v-text="installment.cardNo ? installment.cardNo:'未知'"></el-col>
                                    <el-col :span="3">分期类型</el-col><el-col :span="9" v-text="installment.instalmentType ? installment.instalmentType:'未知'"></el-col>
                                </el-col>
                                <el-col :span="24" style="margin-top: 7px;border-top: #bcbcbc solid 1px">
                                    <el-col :span="3">分期时间</el-col><el-col :span="9" v-text="installment.instalmentDate ? installment.instalmentDate:'未知'"></el-col>
                                    <el-col :span="3">分期总额</el-col><el-col :span="9" v-text="installment.instalmentBal ? installment.instalmentBal:'未知'"></el-col>
                                </el-col>
                                <el-col :span="24" style="margin-top: 7px;border-top: #bcbcbc solid 1px">
                                    <el-col :span="3">分期期数</el-col><el-col :span="9" v-text="installment.instalmentCount ? installment.instalmentCount:'未知'"></el-col>
                                    <el-col :span="3">剩余期数</el-col><el-col :span="9" v-text="installment.residualnstalmentCount ? installment.residualnstalmentCount:'未知'"></el-col>
                                </el-col>
                                <el-col :span="24" style="margin-top: 7px;border-top: #bcbcbc solid 1px">
                                    <el-col :span="3">本期分期金额</el-col><el-col :span="9" v-text="installment.curInstalmentAmt ? installment.curInstalmentAmt:'未知'"></el-col>
                                    <el-col :span="3">本期分期手续费</el-col><el-col :span="9" v-text="installment.curInstalmentFeeAmt ? installment.curInstalmentFeeAmt:'未知'"></el-col>
                                </el-col>
                                <el-col :span="24" style="margin-top: 7px;border-top: #bcbcbc solid 1px">
                                    <el-col :span="3">本期偿还额</el-col><el-col :span="9" v-text="installment.curRepaymentAmt ? installment.curRepaymentAmt:'未知'"></el-col>
                                    <el-col :span="3">剩余本金</el-col><el-col :span="9" v-text="installment.residualPrincipal ? installment.residualPrincipal:'未知'"></el-col>
                                </el-col>
                              </div>
                            </el-col>
                        </el-col>
                      </div>
                    </el-col>
                </el-col>
            </el-col>
        </el-col>
      </div>
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
      cards: [{
        cardNo: '',
        realName: '',
        currency: '',
        creditLimit: '',
        withdrawalLimit: '',
        bankCode: '',
        bills: [{
          statementMonth: '',
          statementStartDate: '',
          statementEndDate: '',
          paymentDueDate: '',
          generalLedgerInfo: [{
            curPaymentBal: '',
            minPaymentBal: '',
            creditLimit: '',
            withdrawalLimit: '',
          }],
          accountChangeInfos: [
            [{
              cardNo: '',
              curStatementBal: '',
              curDebitsBal: '',
              preStatementBal: '',
              prePaymentBal: '',
              curAdjustmentBal: '',
              cycleInterest: '',
            }],
          ],
          details: [
            [{
              trdDate: '',
              accDate: '',
              cardNo: '',
              amt: '',
              currency: '',
              accAmt: '',
              summary: '',
            }],
          ],
          installments: [
            [{
              cardNo: '',
              instalmentType: '',
              instalmentDate: '',
              instalmentBal: '',
              instalmentCount: '',
              residualnstalmentCount: '',
              curInstalmentAmt: '',
              curInstalmentFeeAmt: '',
              curRepaymentAmt: '',
              residualPrincipal: '',
            }],
          ],
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
</style>