<template>
    <div>
        <el-row>
            <el-col :span="24">
                <el-form :inline="true">
                    <el-form-item>
                        <el-input v-model="form.shop_name" placeholder="商户名称"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-input v-model="form.shop_no" placeholder="商户号"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" @click.native="filter">查找</el-button>
                        <el-button type="primary" @click.native="clearFilter">清空筛选</el-button>
                    </el-form-item>
                </el-form>
            </el-col>
        </el-row>
        <el-table :data="tableData" border style="width: 100%;" :cell-class-name="cellClassName" :header-cell-class-name="headerCellClassName">
            <el-table-column property="id" label="ID" align="center" min-width="80"></el-table-column>
            <el-table-column property="shop_name" label="商户名称" align="center" min-width="150"></el-table-column>
            <el-table-column property="shop_no" label="商户号" align="center" min-width="120"></el-table-column>
            <el-table-column property="apply_total" label="总额度申请" align="center"  min-width="100"></el-table-column>
            <el-table-column property="state_total" label="审核状态" align="center" min-width="100">
                <template slot-scope="scope" >
                    <span style="color: red" v-text="stateFormatter(scope.row.state_total)" v-if="scope.row.state_total === 2"></span>
                    <span v-text="stateFormatter(scope.row.state_total)" style="color: green"  v-if="scope.row.state_total === 1"></span>
                </template>
            </el-table-column>
            <el-table-column property="allow_total" label="通过额度" align="center" min-width="100"></el-table-column>

            <el-table-column property="apply_daily_limit" label="单日限额申请" align="center" min-width="100"></el-table-column>
            <el-table-column property="state_daily_limit" label="审核状态" align="center" min-width="100">
                <template slot-scope="scope">
                    <span v-text="stateFormatter(scope.row.state_daily_limit)" style="color: red" v-if="scope.row.state_daily_limit === 2"></span>
                    <span v-text="stateFormatter(scope.row.state_daily_limit)" style="color: green" v-if="scope.row.state_daily_limit === 1"></span>
                </template>
            </el-table-column>
            <el-table-column property="allow_daily_limit" label="通过额度" align="center" min-width="100"></el-table-column>
            <el-table-column property="apply_single_limit" label="单笔限额申请" align="center" min-width="100"></el-table-column>
            <el-table-column property="state_single_limit" label="审核状态" align="center" min-width="100">
                <template slot-scope="scope">
                    <span v-text="stateFormatter(scope.row.state_single_limit)" style="color: red" v-if="scope.row.state_single_limit === 2"></span>
                    <span v-text="stateFormatter(scope.row.state_single_limit)" style="color: green" v-if="scope.row.state_single_limit === 1"></span>
                </template>
            </el-table-column>
            <el-table-column property="allow_single_limit" label="通过额度" align="center" min-width="100"></el-table-column>
            <el-table-column property="memo" label="备注" align="center" min-width="180"></el-table-column>
            <el-table-column property="auditor" label="审核人员" align="center" min-width="100"></el-table-column>
            <el-table-column property="updated_at" label="审核时间" align="center" width="180">
            </el-table-column>
        </el-table>
        <page :total="pageCount" :current-page="currentPage" :page-size="pageSize" @current-change="handleCurrentChange"></page>

    </div>
</template>
<script>
  import apiBase from '../../apiBase';
  import Page from '../../components/Page';

  export default {
    name: 'auditQuotaLog',
    data() {
      return {
        tableData: [],
        form: {
          shop_name: '',
          shop_no: '',
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
        if (this.form.shop_name !== '') {
          params.shop_name = this.form.shop_name;
        }
        if (this.form.shop_no !== '') {
          params.shop_no = this.form.shop_no;
        }
        this.$http.get(`${apiBase}shop-quota-log-list`, { params })
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
          shop_name: '',
          shop_no: '',
        };
        this.getData();
      },
      stateFormatter(v) {
        switch (v) {
          case 0 :
            return '待审核';
          case 1 :
            return '通过';
          case 2 :
            return '失败';
          default:
            return '';
        }
      },
      cellClassName(a) {
        if (a.columnIndex === 3 || a.columnIndex === 4 || a.columnIndex === 5) {
          return 'success-cell';
        }
        if (a.columnIndex === 6 || a.columnIndex === 7 || a.columnIndex === 8) {
          return 'warning-cell';
        }
        if (a.columnIndex === 9 || a.columnIndex === 10 || a.columnIndex === 11) {
          return 'red-cell';
        }
        return '';
      },
      headerCellClassName(a) {
        if (a.columnIndex === 3 || a.columnIndex === 4 || a.columnIndex === 5) {
          return 'success-cell';
        }
        if (a.columnIndex === 6 || a.columnIndex === 7 || a.columnIndex === 8) {
          return 'warning-cell';
        }
        if (a.columnIndex === 9 || a.columnIndex === 10 || a.columnIndex === 11) {
          return 'red-cell';
        }
        return '';
      },
    },
  };
</script>
<style type="text/css">
    .el-table .warning-cell {
        background: oldlace;
    }

    .el-table .success-cell {
        background: #f0f9eb;
    }
    .el-table .red-cell {
        background: #f5e7c2;
    }
</style>