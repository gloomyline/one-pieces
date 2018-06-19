<template>
    <el-row>
        <el-col>
            <el-button type="info" style="margin-bottom: 5px" @click.native="$router.back(-1)">返回上一页</el-button>
        </el-col>
        <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">基本信息</div></el-col>
        <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
            <el-col :span="3">报告编号</el-col><el-col :span="9" v-text="Credit.basicInfo.no ? Credit.basicInfo.no:'未知'"></el-col>
            <el-col :span="3">查询时间</el-col><el-col :span="9" v-text="Credit.basicInfo.sTime ? Credit.basicInfo.sTime:'未知'"></el-col>
        </el-col>
        <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
            <el-col :span="3">报告时间</el-col><el-col :span="9" v-text="Credit.basicInfo.time ? Credit.basicInfo.time:'未知'"></el-col>
            <el-col :span="3">姓名</el-col><el-col :span="9" v-text="Credit.basicInfo.name ? Credit.basicInfo.name:'未知'"></el-col>
        </el-col>
        <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
            <el-col :span="3">证件类型</el-col><el-col :span="9" v-text="Credit.basicInfo.cardType ? Credit.basicInfo.cardType:'未知'"></el-col>
            <el-col :span="3">证件号码</el-col><el-col :span="9" v-text="Credit.basicInfo.cardNo ? Credit.basicInfo.cardNo:'未知'"></el-col>
        </el-col>
        <!--借贷记录-->
         <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">信贷记录</div></el-col>
         <el-col style="padding: 0">
             <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px" >
                 <el-col :span="3">描述</el-col><el-col :span="21" v-text="Credit.creditRecord.descrip ? Credit.creditRecord.descrip:'未知'"></el-col>
             </el-col>
             <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
                 <el-col :span="3">借贷记录概要</el-col>
                 <el-col :span="21" style="background-color: #e9ebf4" align="center">
                     <el-col :span="24" style="border-bottom: 1px solid #bcbcbc">
                        <el-col :span="8"><el-button type="text" icon="el-icon-warning"  @click.native="showTipDialog('type')">信用维度</el-button></el-col>
                        <el-col :span="8"><el-button type="text" icon="el-icon-warning"  @click.native="showTipDialog('var')">信用度量</el-button></el-col>
                        <el-col :span="8"><el-button type="text">具体数量</el-button></el-col>
                     </el-col>
                     <el-col :span="24" v-for="item in Credit.creditRecord.summary" v-if="Credit.creditRecord.summary" style="border-bottom: 1px solid #bcbcbc">
                         <el-col :span="8" v-text="item.type" ></el-col>
                         <el-col :span="8" v-text="item.var"></el-col>
                         <el-col :span="8" v-text="item.count"></el-col>
                     </el-col>
                     <el-col v-if="!Credit.creditRecord.summary">暂无记录</el-col>
                 </el-col>
             </el-col>
         </el-col>
        <!--信贷记录详情-->
        <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">信贷记录详情</div></el-col>
        <el-col style="padding: 0; border-bottom: 1px solid #bcbcbc" v-for="(item, index) in Credit.creditRecord.detail">
            <el-col :span="3"><el-col v-text="'#000' + index+1"></el-col></el-col>
            <el-col :span="21">
                <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px; line-height: 57px" >
                    <el-col :span="3"><el-button type="text" icon="el-icon-warning"  @click.native="showTipDialog('detailType')">类别</el-button></el-col>
                    <el-col :span="21" v-text="item.type ? item.type:'未知'"></el-col>
                </el-col>
                <el-col :span="24" >
                    <el-col :span="24" v-text="item.headTitle" align="left"></el-col>
                    <el-col :span="24" >
                        <el-col v-for="(v, i) in item.item" v-text="i + 1 + '、' + v" align="left" style="border-top: #bcbcbc solid 1px"></el-col>
                    </el-col>
                </el-col>
            </el-col>
        </el-col>
        <el-col :span="24">信贷记录备注：</el-col>
        <el-col :span="24" v-for="(item, index) in Credit.creditRecord.comment" v-text="index + 1 + '、' + item"></el-col>

        <!--公共记录-->
        <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">公共记录</div></el-col>
        <el-col style="padding: 0">
            <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px" >
                <el-col :span="3">描述</el-col><el-col :span="21" v-text="Credit.publicRecord.descrip ? Credit.publicRecord.descrip:'未知'"></el-col>
            </el-col>
            <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
                <el-col :span="3">欠费记录</el-col>
                <el-col :span="21" style="background-color: #e9ebf4" align="center">
                    <el-table :data="Credit.publicRecord.taxArrearsRecords" border style="width: 100%">
                        <el-table-column prop="taxOffice" label="主管税务机关"></el-table-column>
                        <el-table-column prop="statisticalTime" label="欠税统计时间"></el-table-column>
                        <el-table-column prop="amt" label="欠税金额"></el-table-column>
                        <el-table-column prop="taxpayerNo" label="纳税人识别号"></el-table-column>
                    </el-table>
                </el-col>
            </el-col>
            <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
                <el-col :span="3">民事判决记录</el-col>
                <el-col :span="21" style="background-color: #e9ebf4" align="center">
                    <el-table :data="Credit.publicRecord.civilJudgmentRecords" border style="width: 100%">
                        <el-table-column prop="executiveCourt" label="立案法院" width="200"></el-table-column>
                        <el-table-column prop="caseNo" label="案号" width="200"></el-table-column>
                        <el-table-column prop="caseCause" label="案由" width="100"></el-table-column>
                        <el-table-column prop="filingTime" label="立案时间" width="100"></el-table-column>
                        <el-table-column prop="actionObject" label="诉讼标的"></el-table-column>
                        <el-table-column prop="actionObjectAmt" label="诉讼标的金额"></el-table-column>
                        <el-table-column prop="closedWay" label="结案方式"></el-table-column>
                        <el-table-column prop="judgmentResult" label="调节/判决结果" width="200"></el-table-column>
                        <el-table-column prop="judgmentEffectTime" label="调节/判决生效时间"></el-table-column>
                    </el-table>
                </el-col>
            </el-col>
            <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
                <el-col :span="3">强制执行记录</el-col>
                <el-col :span="21" style="background-color: #e9ebf4" align="center">
                    <el-table :data="Credit.publicRecord.enforcementRecords" border style="width: 100%">
                        <el-table-column prop="executiveCourt" label="执行法院" width="200"></el-table-column>
                        <el-table-column prop="caseNo" label="案号" width="200"></el-table-column>
                        <el-table-column prop="caseCause" label="执行案由" width="100"></el-table-column>
                        <el-table-column prop="filingTime" label="立案时间" width="100"></el-table-column>
                        <el-table-column prop="actionObject" label="申请执行标的" width="120"></el-table-column>
                        <el-table-column prop="actionObjectAmt" label="申请执行标的金额" width="140"></el-table-column>
                        <el-table-column prop="closedWay" label="结案方式"></el-table-column>
                        <el-table-column prop="caseStatus" label="案件状态"></el-table-column>
                        <el-table-column prop="executedObject" label="已执行标的" width="100"></el-table-column>
                        <el-table-column prop="executedObjectAmt" label="已执行标的金额" width="140"></el-table-column>
                    </el-table>
                </el-col>
            </el-col>
            <!--行政处罚记录-->
            <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
                <el-col :span="3">行政处罚记录</el-col>
                <el-col :span="21" style="background-color: #e9ebf4" align="center">
                    <el-table :data="Credit.publicRecord.administrativePenaltyRecords" border style="width: 100%">
                        <el-table-column prop="penaltyOffice" label="处罚机构" width="200"></el-table-column>
                        <el-table-column prop="basicNo" label="文书编号" width="150"></el-table-column>
                        <el-table-column prop="penaltyContent" label="处罚内容"></el-table-column>
                        <el-table-column prop="penaltyAmt" label="处罚金额" width="120"></el-table-column>
                        <el-table-column prop="penaltyEffectTime" label="处罚生效时间"></el-table-column>
                        <el-table-column prop="isReview" label="是否执行行政复议"></el-table-column>
                        <el-table-column prop="reviewResult" label="行政复议结果"></el-table-column>
                        <el-table-column prop="penaltyEndTime" label="处罚截止时间"></el-table-column>
                    </el-table>
                </el-col>
            </el-col>
            <!--电信欠费信息-->
            <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
                <el-col :span="3">电信欠费信息</el-col>
                <el-col :span="21" style="background-color: #e9ebf4" align="center">
                    <el-table :data="Credit.publicRecord.telecomArrearsRecords" border style="width: 100%">
                        <el-table-column prop="telecomOperators" label="电信运营商" width="200"></el-table-column>
                        <el-table-column prop="bizType" label="业务类型"></el-table-column>
                        <el-table-column prop="accDate" label="记账年月"></el-table-column>
                        <el-table-column prop="bizOpenDate" label="业务开通时间"></el-table-column>
                        <el-table-column prop="amt" label="欠费金额"></el-table-column>
                    </el-table>
                </el-col>
            </el-col>
            <!--查询记录-->
            <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">查询记录</div></el-col>
            <el-col style="padding: 0">
                <el-col :span="24" style="margin-top: 10px" >
                    <el-col :span="3">描述</el-col><el-col :span="21" v-text="Credit.searchRecord.descrip ? Credit.searchRecord.descrip:'未知'"></el-col>
                </el-col>

                    <el-table border :data="Credit.searchRecord.searchRecordDet" style="width: 100%">
                        <el-table-column type="index" width="50"></el-table-column>
                        <el-table-column prop="type" label="查询类别" min-width="200"></el-table-column>
                        <el-table-column type="expand" label="查询详情" width="200">
                            <template slot-scope="props">
                                <el-table :data="props.row.item" border  style="width: 100%">
                                    <el-table-column prop="no" label="编号" width="80"></el-table-column>
                                    <el-table-column prop="time" label="时间" width="180"></el-table-column>
                                    <el-table-column prop="user" label="查询用户"></el-table-column>
                                    <el-table-column prop="reason" label="查询原因" ></el-table-column>
                                </el-table>
                            </template>
                        </el-table-column>
                    </el-table>

            </el-col>
        </el-col>

        <el-dialog  title="小贴士" :visible.sync="tipsVisible"  width="30%">
            <el-row>
                <el-col :span="24"><div class='tipsCaption' v-text="msg"></div></el-col>
            </el-row>
            <span slot="footer" class="dialog-footer">
        <el-button type="primary" @click="tipsVisible = false">确 定</el-button>
      </span>
        </el-dialog>
    </el-row>
</template>
<script>
  import apiBase from '../../apiBase';
  import { getJsonAndCheckSuccess, reportErrorMessage } from '../../ApiUtil';

  export default {
    name: 'CreditDetail',
    data() {
      return {
        id: this.$route.params.id,
        Credit: {
          basicInfo: { // 基本信息
            no: '', // 报告编号
            sTime: '', // 查询时间
            time: '', // 报告时间
            name: '', // 姓名
            cardType: '', // 证件类型
            cardNo: '',
            maritalStatus: '',
          },
          creditRecord: { // 借贷记录
            descrip: '',
            summary: [], // 借贷记录概要
            detail: [], // 借贷记录详情
            comment: [], // 借贷记录备注
          },
          publicRecord: {
            desrip: '',
            taxArrearsRecords: [], // 欠费记录
            civilJudgmentRecords: [], // 民事判决记录
            enforcementRecords: [], // 强制执行记录
            administrativePenaltyRecords: [], // 行政处罚记录
            telecomArrearsRecords: [], // 电信欠费信息
          },
          searchRecord: { // 查询记录
            desrip: '', // 查询记录描述
            searchRecordDet: [], // 查询详情
          },
        },
        tipsVisible: false,
        msg: '',
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
              this.Credit = response.results;
            }
          }).catch(reportErrorMessage(this));
      },
      insuranceTypeFormatter(row) {
        switch (row.insuranceType) {
          case '1' : return '养老';
          case '2' : return '医疗';
          case '3' : return '失业';
          case '4' : return '工伤';
          case '5' : return '生育';
          default: return '';
        }
      },
      showTipDialog(v) {
        this.tipsVisible = true;
        if (v === 'type') {
          this.msg = '信用维度【资产处置信息、保证人代偿信息、信用卡、购房贷款、其他贷款】';
        } else if (v === 'var') {
          this.msg = '信用度量【账户数、未结清/未销户账户数、发生过逾期的账户数、发生过逾期的账户数、发生过 90天以上逾期的账户数、为他人担保笔数、笔数其中笔数为资产处理与担保人代偿的笔数';
        } else if (v === 'detailType') {
          this.msg = '类别【资产处置信息、保证人代偿信息、信用卡、购房贷款、其他贷款、为他人担保信息】';
        }
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
<style type="text/css">
    table {
        /*border-spacing: 0;*/
        border-collapse: separate;
    }
</style>