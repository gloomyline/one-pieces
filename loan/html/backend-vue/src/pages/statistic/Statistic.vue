<template>
  <el-tabs v-model="activeName" @tab-click="handleClick">
    <el-tab-pane label="今日统计" name="first">
      <el-row :gutter="30">
        <el-col :span="6">
          <el-card class="box-card bg-yellow" style=" ">
            <h3>{{ resData.new_users }}</h3>
            <p>新用户</p>
          </el-card>
          <div class="grid-content bg-purple"></div>
        </el-col>
        <el-col :span="6">
          <el-card class="box-card bg-aqua">
            <h3>{{ resData.today_quota }}元/{{ resData.today_quota_count }}笔</h3>
            <p>今日申请总额</p>
          </el-card>
          <div class="grid-content bg-purple"></div>
        </el-col>
        <el-col :span="6">
          <el-card class="box-card bg-green">
            <h3>{{ resData.today_loan_total }}元/{{ resData.today_loan_total_count }}笔</h3>
            <p>今日放款总额</p>
          </el-card>
          <div class="grid-content bg-purple"></div>
        </el-col>
        <el-col :span="6">
          <el-card class="box-card bg-red">
            <h3>{{ resData.today_repayment }}元/{{ resData.today_repayment_count }}笔</h3>
            <p>今日还款总额</p>
          </el-card>
          <div class="grid-content bg-purple"></div>
        </el-col>
      </el-row>
      <el-row :gutter="30" style="margin-top: 20px">
        <el-col :span="6">
          <el-card class="box-card bg-blue" style=" ">
            <h3>{{ resData.today_refuse }}元/{{ resData.today_refuse_count }}笔</h3>
            <p>今日拒绝次数</p>
          </el-card>
          <div class="grid-content bg-purple"></div>
        </el-col>
        <el-col :span="6">
          <el-card class="box-card bg-teal">
            <h3>{{ resData.today_overdue }}元/{{ resData.today_overdue_count }}笔</h3>
            <p>今日逾期</p>
          </el-card>
          <div class="grid-content bg-purple"></div>
        </el-col>
        <el-col :span="6">
          <el-card class="box-card bg-purple">
            <h3>{{ resData.today_plan_repayment }}元/{{ resData.today_plan_repayment_count }}笔</h3>
            <p>今日待还总额</p>
          </el-card>
          <div class="grid-content bg-purple"></div>
        </el-col>
        <el-col :span="6">
          <el-card class="box-card bg-black">
            <h3>{{ passRate }}%</h3>
            <p>通过率</p>
          </el-card>
          <div class="grid-content bg-purple"></div>
        </el-col>
      </el-row>

      <el-row>
        <el-col :span="24" style="margin-top: 10px"><h3>代办事项</h3></el-col>
        <el-col :span="24" style="border-bottom: #bcbcbc solid 1px">
          <el-col :span="24">
           <h4>借款管理</h4>
          </el-col>
          <el-col :span="24">
            <el-col :span="3">待初审</el-col><el-col :span="3"><el-button type="text" @click.native="auditingJump">{{ resData.auditingCount }}</el-button></el-col>
            <el-col :span="3">待复审</el-col><el-col :span="3"><el-button type="text" @click.native="reviewingJump">{{ resData.reviewingCount }}</el-button></el-col>
          </el-col>
          <el-col :span="24">
            <el-col :span="3">额度待审</el-col><el-col :span="3"><el-button type="text" @click.native="quotaAuditJump">{{ resData.quota_apply_state_auditing_count}}</el-button></el-col>
            <el-col :span="3">逾期未还</el-col><el-col :span="3"><el-button type="text" @click.native="overdueJump">{{ resData.overdueCount }}</el-button></el-col>
          </el-col>
        </el-col>
      </el-row>
      <el-row>
        <el-col :span="24" style="border-bottom: #bcbcbc solid 1px">
          <el-col :span="24">
            <h4>资金推广</h4>
          </el-col>
          <el-col :span="24">
            <el-col :span="3">推广结算</el-col><el-col :span="3">暂无</el-col>
            <el-col :span="3">待放款</el-col><el-col :span="3"><el-button type="text" @click.native="quotaJump">{{ resData.quotaCount }}</el-button></el-col>
          </el-col>
        </el-col>
      </el-row>

      <el-row>
        <el-col :span="24" style="margin-top: 10px"><h3>借款|还款统计</h3></el-col>
        <el-col :span="24">
          <el-col :span="24">
            <el-col :span="24" style="color: #44b6ae;">
              <i class="fa fa-line-chart" aria-hidden="true"></i>一周借还款
            </el-col>
            <div class="small">
              <line-chart :chart-data="loan" :options="{responsive: false, maintainAspectRatio: false,scaleShowGridLines : true,}" :width="lineChartWidth" :height="lineChartHeight"></line-chart>
            </div>
            <div style="clear: both"></div>
          </el-col>
          <el-col :span="24" >
            <el-col :span="24" style="color: #44b6ae;">
              <i class="fa fa-line-chart" aria-hidden="true"></i>一周申请拒绝
            </el-col>
            <div class="small">
              <line-chart :chart-data="apply" :options="{responsive: false, maintainAspectRatio: false,scaleShowGridLines : true,}" :width="lineChartWidth" :height="lineChartHeight"></line-chart>
            </div>
            <div style="clear: both"></div>
          </el-col>
        </el-col>
      </el-row>
      <el-row>
        <el-col :span="24" style="margin-top: 10px"><h3>逾期提醒</h3></el-col>
        <el-col :span="24">
          <div v-for="item in resData.overdue_list" :key="item.id" class="text item" v-if="resData.overdue_list.length > 0">
            <el-button type="text" @click.native="handelOverdueJump(item.id)">
              {{ '借款编号：' + item.encoding + '， 借款日为' + item.created_at + ', 借款周期为 ' + item.period + '天, 本金为 ' + item.quota + '元， 姓名 ' + item.user_name + ', 电话为 ' + item.mobile  }}
            </el-button>
          </div>
          <div v-if="resData.overdue_list.length === 0">
            暂无
          </div>
        </el-col>
      </el-row>
    </el-tab-pane>
    <el-tab-pane label="累计统计" name="second">
      <el-row :gutter="30">
        <el-col :span="6">
          <el-card class="box-card bg-yellow" style=" ">
            <h3>{{ resSecData.users }}</h3>
            <p>注册用户</p>
          </el-card>
          <div class="grid-content bg-purple"></div>
        </el-col>
        <el-col :span="6">
          <el-card class="box-card bg-aqua">
            <h3>{{ resSecData.quota }}元/{{ resSecData.quota_count }}笔</h3>
            <p>申请总额</p>
          </el-card>
          <div class="grid-content bg-purple"></div>
        </el-col>
        <el-col :span="6">
          <el-card class="box-card bg-green">
            <h3>{{ resSecData.loan_total }}元/{{ resSecData.loan_total_count }}笔</h3>
            <p>放款总额</p>
          </el-card>
          <div class="grid-content bg-purple"></div>
        </el-col>
        <el-col :span="6">
          <el-card class="box-card bg-red">
            <h3>{{ resSecData.repayment }}元/{{ resSecData.repayment_count }}笔</h3>
            <p>累计还款总额</p>
          </el-card>
          <div class="grid-content bg-purple"></div>
        </el-col>
      </el-row>
      <el-row :gutter="30" style="margin-top: 20px">
        <el-col :span="6">
          <el-card class="box-card bg-blue" style=" ">
            <h3>{{ resSecData.refuse }}元/{{ resSecData.refuse_count }}笔</h3>
            <p>拒绝次数</p>
          </el-card>
          <div class="grid-content bg-purple"></div>
        </el-col>
        <el-col :span="6">
          <el-card class="box-card bg-teal">
            <h3>{{ resSecData.overdue }}元/{{ resSecData.overdue_count }}笔</h3>
            <p>逾期未还总额</p>
          </el-card>
          <div class="grid-content bg-purple"></div>
        </el-col>
        <el-col :span="6">
          <el-card class="box-card bg-purple">
            <h3>{{ resSecData.plan_repayment }}元/{{ resSecData.plan_repayment_count }}笔</h3>
            <p>待还款总额</p>
          </el-card>
          <div class="grid-content bg-purple"></div>
        </el-col>
        <el-col :span="6">
          <el-card class="box-card bg-black">
            <h3>{{ totalPassRate }}%</h3>
            <p>通过率</p>
          </el-card>
          <div class="grid-content bg-purple"></div>
        </el-col>
      </el-row>

      <el-row>
        <el-col :span="24" style="margin-top: 10px"><h3>代办事项</h3></el-col>
        <el-col :span="24" style="border-bottom: #bcbcbc solid 1px">
          <el-col :span="24">
            <h4>借款管理</h4>
          </el-col>
          <el-col :span="24">
            <el-col :span="3">待初审</el-col><el-col :span="3"><el-button type="text" @click.native="auditingJump">{{ resData.auditingCount }}</el-button></el-col>
            <el-col :span="3">待复审</el-col><el-col :span="3"><el-button type="text" @click.native="reviewingJump">{{ resData.reviewingCount }}</el-button></el-col>
          </el-col>
          <el-col :span="24">
            <el-col :span="3">额度待审</el-col><el-col :span="3"><el-button type="text" @click.native="quotaAuditJump">{{ resData.quota_apply_state_auditing_count}}</el-button></el-col>
            <el-col :span="3">逾期未还</el-col><el-col :span="3"><el-button type="text" @click.native="overdueJump">{{ resData.overdueCount }}</el-button></el-col>
          </el-col>
        </el-col>
      </el-row>
      <el-row>
        <el-col :span="24" style="border-bottom: #bcbcbc solid 1px">
          <el-col :span="24">
            <h4>资金推广</h4>
          </el-col>
          <el-col :span="24">
            <el-col :span="3">推广结算</el-col><el-col :span="3">暂无</el-col>
            <el-col :span="3">待放款</el-col><el-col :span="3"><el-button type="text" @click.native="quotaJump">{{ resData.quotaCount }}</el-button></el-col>
          </el-col>
        </el-col>
      </el-row>

      <el-row>
        <el-col :span="24" style="margin-top: 10px"><h3>借款|还款统计</h3></el-col>
        <el-col :span="24">
          <el-col :span="24" style="color: #44b6ae;">
            <i class="fa fa-line-chart" aria-hidden="true"></i>借还款
          </el-col>
          <div class="small">
            <line-chart :chart-data="monthLoan" :options="{responsive: false, maintainAspectRatio: false,scaleShowGridLines : true,}" :width="lineChartWidth" :height="lineChartHeight"></line-chart>
          </div>
        </el-col>
        <el-col :span="24" >
          <el-col :span="24" style="color: #44b6ae;">
            <i class="fa fa-line-chart" aria-hidden="true"></i>申请拒绝
          </el-col>
          <div class="small">
            <line-chart :chart-data="monthApply" :options="{responsive: false, maintainAspectRatio: false,scaleShowGridLines : true,}" :width="lineChartWidth" :height="lineChartHeight"></line-chart>
          </div>
        </el-col>
      </el-row>
      <el-row>
        <el-col :span="24" style="margin-top: 10px"><h3>逾期提醒</h3></el-col>
        <el-col :span="24">
          <div v-for="item in resData.overdue_list" :key="item.id" class="text item" v-if="resData.overdue_list.length > 0">
            <el-button type="text" @click.native="handelOverdueJump(item.id)">
              {{ '借款编号：' + item.encoding + '， 借款日为' + item.created_at + ', 借款周期为 ' + item.period + '天, 本金为 ' + item.quota + '元， 姓名 ' + item.user_name + ', 电话为 ' + item.mobile  }}
            </el-button>
          </div>
          <div v-if="resData.overdue_list.length === 0">
            暂无
          </div>
        </el-col>
      </el-row>
    </el-tab-pane>
  </el-tabs>
</template>

<script>
import LineChart from '../../components/statisticCharts/LineChart';
import apiBase from '../../apiBase';

export default {
  name: 'statistic',
  components: {
    LineChart,
  },
  data() {
    return {
      passRate: 0,
      totalPassRate: 0,
      loan: '',
      apply: '',
      monthLoan: '',
      monthApply: '',
      activeName: 'first',
      resData: {
        new_user: 0,
        overdue_list: {},
        labels: '',
      },
      resSecData: [],
      loanOption: null,
      lineChartWidth: 1350,
      lineChartHeight: 450,
    };
  },
  mounted() {
    this.getData();
  },
  methods: {
    getData() {
      this.$http.get(`${apiBase}statistic`)
        .then(response => response.json())
        .then((json) => {
          if (json.status === 'SUCCESS') {
            this.resData = json.results;
            if (this.resData.today_quota_count !== '0') {
              const todayLoanCount = this.resData.today_loan_total_count;
              const todayQuotaCount = this.resData.today_quota_count;
              this.passRate = Number((todayLoanCount / todayQuotaCount) * 100).toFixed(2);
            }
            this.loan = {
              labels: this.resData.labels,
              datasets: [
                {
                  label: '今日放款',
                  backgroundColor: 'rgba(75, 192, 192, 0.1)',
                  borderColor: '#44b6ae',
                  lineTension: 0,
                  data: this.resData.loan_amount,
                }, {
                  label: '今日还款',
                  backgroundColor: 'rgba(227, 91, 90, 0.1)',
                  borderColor: '#e35b5a',
                  lineTension: 0,
                  data: this.resData.repayed_amount,
                },
              ],
              options: {
                showScale: true,
                scaleShowGridLines: true,
                scaleShowHorizontalLines: true,
                pointDot: true,
              },
            };
            this.apply = {
              labels: this.resData.labels,
              datasets: [
                {
                  label: '申请次数',
                  backgroundColor: 'rgba(75, 192, 192, 0.1)',
                  borderColor: '#44b6ae',
                  lineTension: 0,
                  data: this.resData.apply_count,
                }, {
                  label: '拒绝次数',
                  backgroundColor: 'rgba(227, 91, 90, 0.1)',
                  borderColor: '#e35b5a',
                  lineTension: 0,
                  data: this.resData.refuse_count,
                },
              ],
              options: {
                showScale: true,
                scaleShowGridLines: true,
                scaleShowHorizontalLines: true,
                pointDot: true,
              },
            };
          }
        }).catch(() => {
          this.$message('系统错误');
        });
    },
    handleClick(tab) {
      if (tab.name === 'second') {
        this.$http.get(`${apiBase}accumulate-statistic`)
          .then(response => response.json())
          .then((json) => {
            if (json.status === 'SUCCESS') {
              this.resSecData = json.results;
              if (this.resSecData.quota_count !== '0') {
                const loanCount = this.resSecData.loan_total_count;
                const quotaCount = this.resSecData.quota_count;
                this.totalPassRate = Number((loanCount / quotaCount) * 100).toFixed(2);
              }
              this.monthLoan = {
                labels: this.resSecData.labels,
                datasets: [
                  {
                    label: '放款',
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    borderColor: '#44b6ae',
                    lineTension: 0,
                    data: this.resSecData.loan_amount,
                  }, {
                    label: '还款',
                    backgroundColor: 'rgba(227, 91, 90, 0.1)',
                    borderColor: '#e35b5a',
                    lineTension: 0,
                    data: this.resSecData.repayed_amount,
                  },
                ],
                options: {
                  showScale: true,
                  scaleShowGridLines: true,
                  scaleShowHorizontalLines: true,
                  pointDot: true,
                },
              };
              this.monthApply = {
                labels: this.resSecData.labels,
                datasets: [
                  {
                    label: '申请次数',
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    borderColor: '#44b6ae',
                    lineTension: 0,
                    data: this.resSecData.apply_count,
                  }, {
                    label: '拒绝次数',
                    backgroundColor: 'rgba(227, 91, 90, 0.1)',
                    borderColor: '#e35b5a',
                    lineTension: 0,
                    data: this.resSecData.month_refuse_count,
                  },
                ],
                options: {
                  showScale: true,
                  scaleShowGridLines: true,
                  scaleShowHorizontalLines: true,
                  pointDot: true,
                },
              };
            }
          }).catch(() => {
            this.$message('系统错误');
          });
      }
    },
    auditingJump() {
      this.$router.push({
        path: '/checks',
        query: { state: 'auditing' },
      });
    },
    reviewingJump() {
      this.$router.push({
        path: '/reviews',
        query: { state: 'reviewing' },
      });
    },
    quotaJump() {
      this.$router.push({
        path: '/reviews',
        query: { state: 'review_success' },
      });
    },
    handelOverdueJump(id) {
      this.$router.push({
        path: '/overdues',
        query: { Id: id },
      });
    },
    overdueJump() {
      this.$router.push({
        path: '/overdues',
      });
    },
    quotaAuditJump() {
      this.$router.push({
        path: '/quota-audit',
      });
    },
  },
  filters: {
    nullFilter(v) {
      return v === null ? 0 : v;
    },
  },
};
</script>
<style type="text/css">
.small {
 margin: 0 auto;
}
.box-card h3{
  font-size: 30px;
  margin: 0 0 10px 0;
  white-space: nowrap;
  padding: 0;
}
.small canvas{
  width: 95%;
}
</style>