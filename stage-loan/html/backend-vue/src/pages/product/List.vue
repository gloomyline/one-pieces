<template>
  <div>
    <el-table :data="tableData" stripe border style="width: 100%;">
      <el-table-column property="id" label="ID" align="center" min-width="60"></el-table-column>
      <el-table-column property="title" label="借贷产品名称" align="center" min-width="100"></el-table-column>
      <el-table-column property="quota_range" label="借款金额范围(元)" align="center" width="150"></el-table-column>
      <el-table-column property="periods" label="借款期数(月)" align="center" :formatter="periodsFormatter" min-width="190"></el-table-column>
      <el-table-column property="annualized_interest_rate" label="年化利率（%）" align="center" min-width="80"></el-table-column>
      <el-table-column property="repayment_way" label="还款方式" align="center" min-width="100"></el-table-column>
      <el-table-column property="trial_rate" label="信审费率（%）" align="center" min-width="90"></el-table-column>
      <el-table-column property="service_rate" label="服务费率（%）" align="center" min-width="90"></el-table-column>
      <el-table-column property="poundage" label="手续费率（%）" align="center" min-width="90"></el-table-column>
      <el-table-column property="overdue_rate" label="逾期费率（%）" align="center" min-width="90"></el-table-column>
      <el-table-column property="prepayment_poundage" label="提前还款手续费率（%）" align="center" min-width="100"></el-table-column>
      <el-table-column property="active" label="状态" align="center" :formatter="activeFormatter" min-width="80"></el-table-column>
      <el-table-column property="updated_at" label="更新时间" align="center" width="110"></el-table-column>
      <el-table-column label="操作" align="center" min-width="100">
        <template slot-scope="scope">
          <el-button-group>
            <el-button type="primary" size="small" @click.native="handleJump(scope.row.id)">编辑</el-button>
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
  name: 'products',
  data() {
    return {
      tableData: [],
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
      this.$http.get(`${apiBase}products`)
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
    handleAdd() {
      this.$router.push({
        path: '/add-product',
      });
    },
    handleJump(name) {
      this.$router.push({
        path: `/update-product/${name}`,
      });
    },
    activeFormatter(v) {
      switch (v.active) {
        case 0: return '关闭';
        case 1: return '开启';
        default: return '';
      }
    },
    periodsFormatter(row) {
      const arr = row.periods.split(',');
      let val = '';
      if (arr && arr.length > 0) {
        arr.forEach((v, k) => {
          if (k === arr.length - 1) {
            val = `${val}${v}期`;
          } else {
            val = `${val}${v}期,`;
          }
        });
      }
      return val;
    },
  },
};
</script>