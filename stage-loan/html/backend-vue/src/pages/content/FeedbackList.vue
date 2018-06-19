<template>
  <div>
    <el-row>
      <el-col :span="24">
        <el-form :inline="true">
          <el-form-item>
            <el-input v-model="form.mobile" placeholder="用户名"></el-input>
          </el-form-item>
          <el-form-item>
            <el-button type="primary" @click.native="filter">查找</el-button>
            <el-button type="primary" @click.native="clearFilter">清空筛选</el-button>
          </el-form-item>
        </el-form>
      </el-col>
    </el-row>
    <el-table :data="tableData" stripe border style="width: 100%;">
      <el-table-column property="id" label="ID" align="center" width="180"></el-table-column>
      <el-table-column property="mobile" label="提交用户名" align="center" width="180"></el-table-column>
      <el-table-column property="type" label="反馈类型" align="center" width="120" >
        <template slot-scope="scope">
          <span>{{ scope.row.type | typeFilters }}</span>
        </template>
      </el-table-column>
      <el-table-column property="content" label="反馈内容" align="center"></el-table-column>
      <el-table-column property="created_at" label="提交时间" align="center" width="180"></el-table-column>
      <el-table-column label="操作" align="center" width="180" >
        <template slot-scope="scope">
          <el-button-group style="">
            <el-button type="danger" size="small"  @click.native="delItem(scope.row.id)">删 除</el-button>
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
import { reportErrorMessage } from '../../ApiUtil';

export default {
  name: 'admins',
  data() {
    return {
      tableData: [],
      form: {
        mobile: '',
      },
      delId: '',
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
      if (this.form.mobile !== '') {
        params.mobile = this.form.mobile;
      }
      this.$http.get(`${apiBase}feedback`, { params })
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
        mobile: '',
      };
      this.getData();
    },
    delItem(id) {
      this.$confirm('确定删除该条记录?', '提示', {
        type: 'warning',
      }).then(() => {
        this.$http.post(`${apiBase}del-feedback`, { feedbackId: id })
        .then(response => response.json())
        .then((json) => {
          if (json.status !== 'SUCCESS') {
            return Promise.reject(json.error_message);
          }
          this.$message({
            type: 'success',
            message: '删除成功！',
          });
          this.getData();
          return {};
        }).catch(reportErrorMessage(this));
      }).catch(() => {});
    },
  },
  filters: {
    typeFilters(v) {
      switch (v) {
        case 'credit' :
          return '信用不足';
        case 'info' :
          return '填写资料';
        case 'loan' :
          return '借款';
        case 'repayment' :
          return '还款';
        case 'func' :
          return '功能建议';
        case 'other' :
          return '其他';
        default:
          return '';
      }
    },
  },
};
</script>