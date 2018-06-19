<template>
  <div>
    <el-row v-if="!this.$route.query.Mobile">
      <el-col :span="24">
        <el-form :inline="true">
          <el-form-item>
            <el-input v-model="form.real_name" placeholder="真实姓名"></el-input>
          </el-form-item>
          <el-form-item>
            <el-input v-model="form.user_name" placeholder="用户名"></el-input>
          </el-form-item>
          <el-form-item>
            <el-button type="primary" @click.native="filter">查找</el-button>
            <el-button type="primary" @click.native="clearFilter">清空筛选</el-button>
          </el-form-item>
        </el-form>
      </el-col>
    </el-row>
    <el-row v-if="this.$route.query.Mobile">
      <el-button type="success" style="margin-bottom: 5px" @click.native="$router.back(-1)">返回上一页</el-button>
    </el-row>
    <el-table :data="tableData" stripe border style="width: 100%;">
      <el-table-column property="id" label="ID" align="center" min-width="80"></el-table-column>
      <el-table-column property="user_name" label="用户名" align="center" min-width="120"></el-table-column>
      <el-table-column property="real_name" label="真实姓名" align="center"></el-table-column>
      <el-table-column property="user_name" label="手机号" align="center" min-width="120"></el-table-column>
      <el-table-column property="is_identity_auth" label="身份认证" align="center">
        <template slot-scope="scope">
          <span v-html='scope.row.is_identity_auth === 1 ? "<span style=" + "color:green" + ">已填写</span>" : "<span style=" + "color:red" + ">未填写</span>"'></span>
        </template>
      </el-table-column>
      <el-table-column property="is_profile_auth" label="个人信息" align="center">
        <template slot-scope="scope">
          <span v-html='scope.row.is_profile_auth === 1 ?  "<span style=" + "color:green" + ">已填写</span>" : "<span style=" + "color:red" + ">未填写</span>"'></span>
        </template>
      </el-table-column>
      <el-table-column property="is_bankcard_auth" label="银行卡" align="center">
        <template slot-scope="scope">
          <span v-html='scope.row.is_bankcard_auth === 1 ?  "<span style=" + "color:green" + ">已填写</span>" : "<span style=" + "color:red" + ">未填写</span>"'></span>
        </template>
      </el-table-column>
      <el-table-column property="is_phone_auth" label="手机认证" align="center">
        <template slot-scope="scope">
          <span v-html='scope.row.is_phone_auth === 1 ?  "<span style=" + "color:green" + ">已填写</span>" : "<span style=" + "color:red" + ">未填写</span>"'></span>
        </template>
      </el-table-column>
      <el-table-column property="is_increase_quota" label="提升额度" align="center">
        <template slot-scope="scope">
          <span v-html='scope.row.is_increase_quota === 1 ?  "<span style=" + "color:green" + ">已填写</span>" : "<span style=" + "color:red" + ">未填写</span>"'></span>
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
      tableData: [],
      form: {
        user_name: '',
        real_name: '',
      },
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
      if (this.form.user_name !== '') {
        params.user_name = this.form.user_name;
      }
      if (this.$route.query.Mobile) {
        params.user_name = this.$route.query.Mobile;
      }
      if (this.form.real_name !== '') {
        params.real_name = this.form.real_name;
      }
      this.$http.get(`${apiBase}auth-center`, { params })
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
        user_name: '',
        real_name: '',
      };
      this.getData();
    },
  },
};
</script>