<template>
  <div>
    <el-row>
      <el-col :span="24">
        <el-form :inline="true">
          <el-form-item label="日期">
            <el-date-picker v-model="form.dateRange" type="daterange" align="right"></el-date-picker>
          </el-form-item>
          <el-form-item>
            <el-button type="primary" @click.native="filter">查找</el-button>
            <el-button type="primary" @click.native="clearFilter">清空筛选</el-button>
            <el-button type="success" @click.native="handExport('daily')">导出</el-button>
          </el-form-item>
        </el-form>
      </el-col>
    </el-row>
    <el-table :data="tableData" stripe border style="width: 100%;">
      <el-table-column property="id" label="ID" align="center" min-width="80"></el-table-column>
      <el-table-column property="created_at" label="日期" align="center" min-width="120"></el-table-column>
      <el-table-column property="user_count" label="新增用户" align="center" min-width="100"></el-table-column>
      <el-table-column property="loan_count" label="借款笔数" align="center" min-width="100"></el-table-column>
      <el-table-column property="lend_count" label="放款笔数" align="center" min-width="100"></el-table-column>
      <el-table-column property="loan_lend_rate" label="通过率(%)" align="center" min-width="100">
        <template slot-scope="scope">
          <span v-text="scope.row.loan_count > 0 ? Number((scope.row.lend_count / scope.row.loan_count) * 100).toFixed(2) : '--'"></span>
        </template>
      </el-table-column>
      <el-table-column property="overdue_count" label="逾期笔数" align="center" min-width="100"></el-table-column>
      <el-table-column property="urge_count" label="催收次数" align="center" min-width="100"></el-table-column>
      <el-table-column property="bad_count" label="坏账笔数" align="center" min-width="100" ></el-table-column>
      <el-table-column property="lend_amount" label="放款金额(元)" align="center" min-width="100"></el-table-column>
      <el-table-column property="repayment_amount" label="还款金额(元)" align="center" min-width="100"></el-table-column>
      <el-table-column property="loan_interest" label="借款息费金额(元)" align="center" min-width="100"></el-table-column>
      <el-table-column property="overdue_amount" label="逾期金额(元)" align="center" min-width="100"></el-table-column>
      <el-table-column property="overdue_penalty" label="逾期罚息(元)" align="center" min-width="100"></el-table-column>
      <el-table-column property="bad_amount" label="坏账金额(元)" align="center" min-width="100"></el-table-column>
    </el-table>
    <page :total="pageCount" :current-page="currentPage" :page-size="pageSize" @current-change="handleCurrentChange"></page>

  </div>
</template>
<script>
import apiBase from '../../apiBase';
import Page from '../../components/Page';

export default {
  name: 'daily',
  data() {
    return {
      tableData: [],
      form: {
        dateRange: {
          key: 'begin_at end_at',
          type: 'datePair',
        },
      },
      pageCount: 0,
      currentPage: 1,
      pageSize: 20,
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
      if (this.form.dateRange && this.form.dateRange[0] && this.form.dateRange[1]) {
        params.start = Date.parse(this.form.dateRange[0]) / 1000;
        params.end = Date.parse(this.form.dateRange[1]) / 1000;
      }
      this.$http.get(`${apiBase}daily`, { params })
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
    filter() {
      this.currentPage = 1;
      this.getData();
    },
    clearFilter() {
      this.form = {
        dateRange: '',
      };
      this.getData();
    },
    handExport(v) {
      const params = {
        limit: this.pageSize,
        offset: this.pageSize * (this.currentPage - 1),
      };
      if (this.form.dateRange && this.form.dateRange[0] && this.form.dateRange[1]) {
        params.start = Date.parse(this.form.dateRange[0]) / 1000;
        params.end = Date.parse(this.form.dateRange[1]) / 1000;
      } else {
        params.start = '';
        params.end = '';
      }
      window.open(`${apiBase}exp-excel?act=${v}&limit=${params.limit}&offset=${params.offset}&start=${params.start}&end=${params.end}&page=${this.currentPage}`);
    },
  },
};
</script>