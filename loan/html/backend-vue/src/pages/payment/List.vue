<template>
  <div>
    <el-table :data="tableData" stripe border style="width: 100%;">
      <el-table-column property="id" label="ID" align="center" width="60"></el-table-column>
      <el-table-column property="no_order" label="商户唯一订单流水号" align="center" width="220"></el-table-column>
      <el-table-column property="user_name" label="用户姓名" align="center"></el-table-column>
      <el-table-column property="money_order" label="交易金额" align="center"></el-table-column>
      <el-table-column property="info_order" label="订单描述" align="center"></el-table-column>
      <el-table-column property="bank_name" label="银行名称" align="center"></el-table-column>
      <el-table-column property="bank_no" label="银行卡号" align="center"></el-table-column>
      <el-table-column property="created_at" label="下单时间" align="center"></el-table-column>
      <el-table-column label="操作" align="center" width="100">
        <template slot-scope="scope">
          <el-button-group>
            <el-button type="primary" size="small" @click.native="handleLending(scope.row.id)">确认放款</el-button>
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
  name: 'pay-confirm',
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
      const params = {
        limit: this.pageSize,
        offset: this.pageSize * (this.currentPage - 1),
      };
      this.$http.get(`${apiBase}confirm-list`, { params })
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
    handleLending(id) {
      this.$http.post(`${apiBase}payment-confirm`, { payLog_id: id })
      .then(response => response.json())
      .then((json) => {
        if (json.status === 'SUCCESS') {
          this.$message(json.error_message);
        } else {
          this.$message(json.error_message);
        }
      }).catch(() => {
        this.$message('系统错误');
      });
    },
  },
};
</script>