<template>
  <div>
    <el-row>
      <el-col>
        <el-button type="info" style="margin-bottom: 5px" @click.native="$router.back(-1)">返回上一页</el-button>
        <el-button @click.native="handleDetail">基本信息</el-button>
        <el-button type="success" @click.native="handleLoanLog">借还记录</el-button>
      </el-col>
    </el-row>
    <el-table :data="tableData" stripe border style="width: 100%;">
      <el-table-column property="id" label="ID" align="center" min-width="100"></el-table-column>
      <el-table-column property="encoding" label="借款编号" align="center" min-width="210"></el-table-column>
      <el-table-column property="type" label="借款类型" align="center" min-width="120" :formatter="typeFormatter"></el-table-column>
      <el-table-column property="shop_name" label="商户名称" align="center" min-width="190"></el-table-column>
      <el-table-column property="product_name" label="商品名称" align="center" min-width="180"></el-table-column>
      <el-table-column property="quota" label="借款金额（元）" align="center" min-width="110"></el-table-column>
      <el-table-column property="period" label="期数（月）" align="center" min-width="100"></el-table-column>
      <el-table-column property="interest" label="借款息费（元）" align="center" min-width="100"></el-table-column>
      <el-table-column property="arrival_amount" label="放款金额（元）" align="center" min-width="100"></el-table-column>
      <el-table-column property="lending_at" label="放款时间" align="center" min-width="170"></el-table-column>
      <el-table-column property="should_repayment_amount" label="应还金额（元）" align="center" min-width="110"></el-table-column><!---->
      <el-table-column property="repayment_amount" label="已还金额（元）" align="center" min-width="110"></el-table-column><!---->
      <el-table-column property="planned_repayment_at" label="计划还款时间" align="center" min-width="100"></el-table-column>
      <el-table-column property="repayment_at" label="实际还款时间" align="center" min-width="180"></el-table-column>
      <el-table-column property="state" label="借款状态" align="center" min-width="120" :formatter="stateFormatter"></el-table-column>
      <el-table-column label="操作" align="center" min-width="150">
        <template slot-scope="scope">
          <el-button-group style="">
            <el-button type="primary" size="small"  @click.native="handleLoanDetail(scope.row.id)">查看</el-button>
          </el-button-group>
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
  name: 'userLoanLog',
  data() {
    return {
      id: this.$route.params.id,
      tableData: [],
      pageCount: 0,
      currentPage: 1,
      pageSize: 20,
      msg: '',
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
    handleLoanDetail(id) {
      this.$router.push({
        path: `/loan-detail/${id}`,
      });
    },
    handleLoanLog() {
      this.$router.push({
        path: `/user-loan/${this.id}`,
      });
    },
    filter() {
      this.currentPage = 1;
      this.getData();
    },
    stateFormatter(v) {
      switch (v.state) {
        case 'auditing': return '待初审';
        case 'audit_failure' : return '初审失败';
        case 'reviewing': return '待复审';
        case 'review_failure' : return '复审失败';
        case 'review_success' : return '复审成功';
        case 'confirming' : return '商家确认中';
        case 'confirm_success' : return '商家确认通过';
        case 'confirm_failure' : return '商家确认未通过';
        case 'granting' : return '放款中';
        case 'repaying' : return '还款中';
        case 'finished' : return '已还完';
        case 'overdue' : return '逾期';
        default: return '';
      }
    },
    typeFormatter(v) {
      switch (v.type) {
        case 1: return '现金分期';
        case 2: return '消费分期';
        default : return '';
      }
    },
  },
};
</script>