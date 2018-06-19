<template>
    <el-row>
        <el-col>
            <el-button type="info" style="margin-bottom: 5px" @click.native="$router.back(-1)">返回上一页</el-button>
        </el-col>
        <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">基本信息</div></el-col>
        <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
            <el-col :span="3">姓名</el-col><el-col :span="9" v-text="socialSecurity.name ? socialSecurity.name:'未知'"></el-col>
            <el-col :span="3">身份证号</el-col><el-col :span="9" v-text="socialSecurity.identityNo ? socialSecurity.identityNo:'未知'"></el-col>
        </el-col>
        <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
            <el-col :span="3">单位名称</el-col><el-col :span="9" v-text="socialSecurity.corpName ? socialSecurity.corpName:'未知'"></el-col>
            <el-col :span="3">账号状态</el-col><el-col :span="9" v-text="socialSecurity.accStatus ? socialSecurity.accStatus:'未知'"></el-col>
        </el-col>
        <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
            <el-col :span="3">开户日期</el-col><el-col :span="9" v-text="socialSecurity.openDate ? socialSecurity.openDate:'未知'"></el-col>
            <el-col :span="3">地区</el-col><el-col :span="9" v-text="area"></el-col>
        </el-col>

       <!-- <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">贷款信息</div></el-col>
        <el-col style="padding: 0" v-if="socialSecurity.loanInfo">
            <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px" >
                <el-col :span="3">贷款账号</el-col><el-col :span="9" v-text="socialSecurity.loanInfo.loadAccNo ? socialSecurity.loanInfo.loadAccNo:'未知'"></el-col>
                <el-col :span="3">贷款期限(月)</el-col><el-col :span="9" v-text="socialSecurity.loanInfo.loadLimit ? socialSecurity.loanInfo.loadLimit:'未知'"></el-col>
            </el-col>
            <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
                <el-col :span="3">贷款总额</el-col><el-col :span="9" v-text="socialSecurity.loanInfo.loadAll ? socialSecurity.loanInfo.loadAll:'未知'"></el-col>
                <el-col :span="3">贷款余额</el-col><el-col :span="9" v-text="socialSecurity.loanInfo.loadBal ? socialSecurity.loanInfo.loadBal:'未知'"></el-col>
            </el-col>
            <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
                <el-col :span="3">还款方式</el-col><el-col :span="9" v-text="socialSecurity.loanInfo.paymentMethod ? socialSecurity.loanInfo.paymentMethod:'未知'"></el-col>
                <el-col :span="3">末次还款年月</el-col><el-col :span="9" v-text="socialSecurity.loanInfo.lastPaymentDate ? socialSecurity.loanInfo.lastPaymentDate:'未知'"></el-col>
            </el-col>
            <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
                <el-col :span="3">贷款状态</el-col><el-col :span="9" v-text="socialSecurity.loanInfo.loadStatus ? socialSecurity.loanInfo.loadStatus:'未知'"></el-col>
                <el-col :span="3">开户日期</el-col><el-col :span="9" v-text="socialSecurity.loanInfo.openDate ? socialSecurity.loanInfo.openDate:'未知'"></el-col>
            </el-col>
        </el-col>
        <el-col style="" v-if="!socialSecurity.loanInfo">暂无贷款信息</el-col>-->

        <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">社保险种信息</div></el-col>
        <el-table :data="socialSecurity.insurances" style="width: 100%">
            <el-table-column prop="insuranceType" label="险种类型" width="180">
                <template slot-scope="scope">
                    <span v-text="insuranceTypeFormatter(scope.row)"></span>
                </template>
            </el-table-column>
            <el-table-column prop="bal" label="余额" width="180">
            </el-table-column>
            <el-table-column prop="sumPayMonth" label="累计缴纳月数">
            </el-table-column>
            <el-table-column prop="dueToMonth" label="余额截止年月">
            </el-table-column>
            <el-table-column prop="accStatus" label="账号状态">
            </el-table-column>
        </el-table>

        <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">养老明细信息</div></el-col>
        <el-table :data="socialSecurity.pensionDetails" style="width: 100%">
           <!-- <el-table-column
                    type="index"
                    >
            </el-table-column>-->
            <el-table-column prop="accDate" label="日期" width="120">
            </el-table-column>
            <el-table-column prop="corpName" label="单位名称" width="220">
            </el-table-column>
            <el-table-column prop="amt" label="缴费金额">
            </el-table-column>
            <el-table-column prop="baseDeposit" label="缴存基数">
            </el-table-column>
            <el-table-column prop="payMonth" label="缴费年月">
            </el-table-column>
            <el-table-column prop="bizDesc" label="业务描述">
            </el-table-column>
        </el-table>
        <el-col :span="24" class="hasMore">
            <el-button v-show="pension_has_more" @click.prevent="handleHasMorePension">浏览更多...</el-button>
        </el-col>

        <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">医疗明细信息</div></el-col>
        <el-table :data="socialSecurity.medicareDetails" style="width: 100%">
            <el-table-column prop="accDate" label="日期" width="120">
            </el-table-column>
            <el-table-column prop="corpName" label="单位名称" width="220">
            </el-table-column>
            <el-table-column prop="amt" label="缴费金额">
            </el-table-column>
            <el-table-column prop="baseDeposit" label="缴存基数">
            </el-table-column>
            <el-table-column prop="payMonth" label="缴费年月">
            </el-table-column>
            <el-table-column prop="bizDesc" label="业务描述">
            </el-table-column>
        </el-table>
        <el-col :span="24" class="hasMore">
            <el-button v-show="medicare_has_more" @click.prevent="handleHasMoreMedicare">浏览更多...</el-button>
        </el-col>
        <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">失业明细信息</div></el-col>
        <el-table :data="socialSecurity.jobSecurityDetails" style="width: 100%">
            <el-table-column prop="accDate" label="日期" width="120">
            </el-table-column>
            <el-table-column prop="corpName" label="单位名称" width="220">
            </el-table-column>
            <el-table-column prop="amt" label="缴费金额">
            </el-table-column>
            <el-table-column prop="baseDeposit" label="缴存基数">
            </el-table-column>
            <el-table-column prop="payMonth" label="缴费年月">
            </el-table-column>
            <el-table-column prop="bizDesc" label="业务描述">
            </el-table-column>
        </el-table>
        <el-col :span="24" class="hasMore">
            <el-button v-show="jobSecurity_has_more" @click.prevent="handleHasMoreJobSecurity">浏览更多...</el-button>
        </el-col>
        <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">失业明细信息</div></el-col>
        <el-table :data="socialSecurity.employmentInjuryDetails" style="width: 100%">
            <el-table-column prop="accDate" label="日期" width="120">
            </el-table-column>
            <el-table-column prop="corpName" label="单位名称" width="220">
            </el-table-column>
            <el-table-column prop="amt" label="缴费金额">
            </el-table-column>
            <el-table-column prop="baseDeposit" label="缴存基数">
            </el-table-column>
            <el-table-column prop="payMonth" label="缴费年月">
            </el-table-column>
            <el-table-column prop="bizDesc" label="业务描述">
            </el-table-column>
        </el-table>
        <el-col :span="24" class="hasMore">
            <el-button v-show="employmentInjury_has_more" @click.prevent="handleHasMoreEmploymentInjury">浏览更多...</el-button>
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
        pensionOffset: 0,
        pensionLimit: 10,
        medicareOffset: 0,
        medicareLimit: 10,
        // 失业明细信息分页
        jobSecurityOffset: 0,
        jobSecurityLimit: 10,
        // 工伤明细信息分页
        employmentInjuryOffset: 0,
        employmentInjuryLimit: 10,
        // 生育明细信息分页
        maternityOffset: 0,
        maternityLimit: 10,
        socialSecurity: {
          name: '',
          identityNo: '',
          corpName: '',
          accStatus: '',
          openDate: '',
          area: '',
          insurances: [], // 险种信息
          pensionDetails: [], // 养老明细信息
          medicareDetails: [], // 医疗明细信息
          jobSecurityDetails: [], // 失业明细信息
          employmentInjuryDetails: [], // 工伤明细信息
          maternityDetails: [], // 生育明细信息
        },
        pension_has_more: false, // 养老明细信息
        medicare_has_more: false, // 医疗明细信息
        jobSecurity_has_more: false, // 失业明细信息
        employmentInjury_has_more: false, // 工伤明细信息
        maternity_has_more: false, // 生育明细信息
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
              this.socialSecurity = response.results;
              this.pension_has_more = response.results.pension_has_more;
              this.medicare_has_more = response.results.medicare_has_more;
              this.jobSecurity_has_more = response.results.jobSecurity_has_more;
              this.employmentInjury_has_more = response.results.employmentInjury_has_more;
              this.maternity_has_more = response.results.maternity_has_more;
              this.areaFormatter(response.results.area);
            }
          }).catch(reportErrorMessage(this));
      },
      handleHasMorePension() {
        const o = this.socialSecurity.pensionDetails;
        const params = {
          pension_limit: this.pensionLimit,
          pension_offset: this.pensionLimit + this.pensionOffset,
        };
        this.$http.get(`${apiBase}limu-info/${this.id}`, { params })
          .then(getJsonAndCheckSuccess)
          .then((response) => {
            if (response.results) {
              this.pensionLimit = params.pension_limit;
              this.pensionOffset = params.pension_offset;
              const a = o.concat(response.results.pensionDetails);
              this.socialSecurity.pensionDetails = a;
              this.pension_has_more = response.results.pension_has_more;
            }
          }).catch(reportErrorMessage(this));
      },
      handleHasMoreMedicare() {
        const o = this.socialSecurity.medicareDetails;
        const params = {
          medicare_limit: this.medicareLimit,
          medicare_offset: this.medicareLimit + this.medicareOffset,
        };
        this.$http.get(`${apiBase}limu-info/${this.id}`, { params })
          .then(getJsonAndCheckSuccess)
          .then((response) => {
            if (response.results) {
              this.medicareLimit = params.medicare_limit;
              this.medicareOffset = params.medicare_offset;
              const a = o.concat(response.results.medicareDetails);
              this.socialSecurity.medicareDetails = a;
              this.medicare_has_more = response.results.medicare_has_more;
            }
          }).catch(reportErrorMessage(this));
      },
      handleHasMoreJobSecurity() {
        const o = this.socialSecurity.jobSecurityDetails;
        const params = {
          jobSecurity_limit: this.jobSecurityLimit,
          jobSecurity_offset: this.jobSecurityLimit + this.jobSecurityOffset,
        };
        this.$http.get(`${apiBase}limu-info/${this.id}`, { params })
          .then(getJsonAndCheckSuccess)
          .then((response) => {
            if (response.results) {
              this.jobSecurityLimit = params.jobSecurity_limit;
              this.jobSecurityOffset = params.jobSecurity_offset;
              const a = o.concat(response.results.jobSecurityDetails);
              this.socialSecurity.jobSecurityDetails = a;
              this.jobSecurity_has_more = response.results.jobSecurity_has_more;
            }
          }).catch(reportErrorMessage(this));
      },
      handleHasMoreEmploymentInjury() {
        const o = this.socialSecurity.employmentInjuryDetails;
        const params = {
          employmentInjury_limit: this.employmentInjuryLimit,
          employmentInjury_offset: this.employmentInjuryLimit + this.employmentInjuryOffset,
        };
        this.$http.get(`${apiBase}limu-info/${this.id}`, { params })
          .then(getJsonAndCheckSuccess)
          .then((response) => {
            if (response.results) {
              this.employmentInjuryLimit = params.employmentInjury_limit;
              this.employmentInjuryOffset = params.employmentInjury_offset;
              const a = o.concat(response.results.employmentInjuryDetails);
              this.socialSecurity.employmentInjuryDetails = a;
              this.employmentInjury_has_more = response.results.employmentInjury_has_more;
            }
          }).catch(reportErrorMessage(this));
      },
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