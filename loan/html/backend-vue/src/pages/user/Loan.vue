<template>
  <div>
    <el-row>
      <el-col>
        <el-button type="info" style="margin-bottom: 5px" @click.native="$router.back(-1)">返回上一页</el-button>
        <el-button @click.native="handleDetail">基本信息</el-button>
        <el-button type="success" @click.native="handleLoanlog">借还记录</el-button>
      </el-col>
    </el-row>
    <el-table :data="tableData" stripe border style="width: 100%;">
      <el-table-column property="id" label="ID" align="center" min-width="100"></el-table-column>
      <el-table-column property="encoding" label="借款编号" align="center" min-width="190"></el-table-column>
      <el-table-column property="quota" label="借款本金" align="center" min-width="100"></el-table-column>
      <el-table-column property="period" label="借款期限" align="center" min-width="100">
        <template slot-scope="scope">
          <span>{{ scope.row.period }}天</span>
        </template>
      </el-table-column>
      <el-table-column property="interest" label="借款息费" align="center" min-width="100"></el-table-column>
      <el-table-column property="arrival_amount" label="放款金额" align="center" min-width="100"></el-table-column>
      <el-table-column property="lending_at" label="放款时间" align="center" min-width="170"></el-table-column>
      <el-table-column property="repayment_amount" label="应还款金额" align="center" min-width="100"></el-table-column>
      <el-table-column property="actual_repayment_amount" label="实际还款金额" align="center" min-width="100"></el-table-column>
      <el-table-column property="repayment_at" label="还款时间" align="center" min-width="170"></el-table-column>
      <el-table-column property="state" label="借款状态" align="center" min-width="120">
        <template slot-scope="scope">
          <span>{{ scope.row.state | stateFilter }}</span>
        </template>
      </el-table-column>
    </el-table>
    <page :total="pageCount" :current-page="currentPage" :page-size="pageSize" @current-change="handleCurrentChange"></page>
  </div>
</template>
<script>
import apiBase from '../../apiBase';
import Page from '../../components/Page';

export default {
  name: 'admins',
  data() {
    return {
      id: this.$route.params.id,
      tableData: [],
      pageCount: 0,
      currentPage: 1,
      pageSize: 20,
      states: [{ id: 'auditing', name: '等待审核' }, { id: 'granting', name: '放款中' }, { id: 'repaying', name: '还款中' }, { id: 'finished', name: '已还款' }, { id: 'overdue', name: '逾期' }, { id: 'review_failure', name: '复审失败' }],
    };
  },
  components: {
    Page,
  },
  mounted() {
    if (!isNaN(this.$route.query.page)) {
      this.currentPage = Number(this.$route.query.page);
    }
    this.getData();
  },
  methods: {
    getData() {
      const params = {
        limit: this.pageSize,
        offset: this.pageSize * (this.currentPage - 1),
      };
      this.$http.get(`${apiBase}user-loan/${this.id}`, { params })
      .then(response => response.json())
      .then((json) => {
        if (json.status === 'SUCCESS') {
          this.tableData = json.results;
          this.pageCount = json.count;
        }
      }).catch(() => {
        this.$message('系统错误');
      });
    },
    handleCurrentChange(page) {
      this.currentPage = page;
      this.getData();
    },
    handleDetail() {
      this.$router.push({
        path: `/user-detail/${this.id}`,
      });
    },
    handleLoanlog() {
      this.$router.push({
        path: `/user-loan/${this.id}`,
      });
    },
    filter() {
      this.currentPage = 1;
      this.getData();
    },
    stateFormatter(row) {
      return this.states.find(v => v.id === row.state).name;
    },
  },
  filters: {
    stateFilter(v) {
      switch (v) {
        case 'auditing' :
          return '等待审核';
        case 'audit_failure' :
          return '初审失败';
        case 'reviewing' :
          return '待复审';
        case 'review_failure' :
          return '复审失败';
        case 'review_success' :
          return '复审成功';
        case 'granting' :
          return '放款中';
        case 'repaying' :
          return '还款中';
        case 'finished' :
          return '已还款';
        case 'overdue' :
          return '逾期';
        default :
          return '';
      }
    },
  },
};
</script>