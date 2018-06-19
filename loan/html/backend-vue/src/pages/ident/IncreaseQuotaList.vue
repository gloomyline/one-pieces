<template>
  <div>
    <el-row >
      <el-col :span="24">
        <el-form :inline="true">
          <el-form-item>
            <el-input v-model="form.mobile" placeholder="用户名"></el-input>
          </el-form-item>
          <el-form-item>
            <el-input v-model="form.real_name" placeholder="真实姓名"></el-input>
          </el-form-item>
          <el-form-item>
            <el-button type="primary" @click.native="filter">查找</el-button>
            <el-button type="primary" @click.native="clearFilter">清空筛选</el-button>
          </el-form-item>
        </el-form>
      </el-col>
    </el-row>
    <el-table :data="tableData" stripe border style="width: 100%;">
      <el-table-column property="id" label="ID" align="center" min-width="60"></el-table-column>
      <el-table-column property="mobile" label="用户名" align="center" min-width="120"></el-table-column>
      <el-table-column property="real_name" label="真实姓名" align="center" min-width="100"></el-table-column>
      <el-table-column property="is_edu_auth" label="学历认证" align="center" min-width="100">
        <template slot-scope="scope">
          <el-button type="success" size="small" @click.native="handleJumpEdu(scope.row.mobile)" v-if="scope.row.is_edu_auth === 1">查看报告</el-button>
          <span class="unfilled-red" v-else="scope.row.is_edu_auth === 0">未填写</span>
        </template>
      </el-table-column>
      <el-table-column property="is_taobao_auth" label="淘宝认证" align="center" min-width="100">
        <template slot-scope="scope">
          <el-button type="success" size="small" @click.native="handleJumpTaobao(scope.row.mobile)" v-if="scope.row.is_taobao_auth === 1">查看报告</el-button>
          <span class="unfilled-red" v-else="scope.row.is_taobao_auth === 0">未填写</span>
        </template>
      </el-table-column>
      <el-table-column property="is_jd_auth" label="京东认证" align="center" min-width="100">
        <template slot-scope="scope">
          <el-button type="success" size="small" @click.native="handleJumpJd(scope.row.mobile)" v-if="scope.row.is_jd_auth === 1">查看报告</el-button>
          <span class="unfilled-red" v-else="scope.row.is_jd_auth === 0">未填写</span>
        </template>
      </el-table-column>
      <el-table-column property="bankcard" label="常用信用卡" align="center" min-width="100">
        <template slot-scope="scope">
          <span :class="{ 'fill-out-green' : scope.row.bankcard === 1, 'unfilled-red' : scope.row.bankcard === 0 }" v-text="authFormatter(scope.row.bankcard)"></span>
        </template>
      </el-table-column>
      <el-table-column property="wechat" label="微信认证" align="center"  min-width="100">
        <template slot-scope="scope">
          <span :class="{ 'fill-out-green' : scope.row.wechat === 1, 'unfilled-red' : scope.row.wechat === 0 }" v-text="authFormatter(scope.row.wechat)"></span>
        </template>
      </el-table-column>
      <el-table-column property="qq" label="QQ认证" align="center" min-width="100">
        <template slot-scope="scope">
          <span :class="{ 'fill-out-green' : scope.row.qq === 1, 'unfilled-red' : scope.row.qq === 0 }" v-text="authFormatter(scope.row.qq)"></span>
        </template>
      </el-table-column>
      <el-table-column property="is_ebank_auth" label="网银流水" align="center" min-width="100">
        <template slot-scope="scope">
          <el-button type="success" size="small" @click.native="handleJumpEbank(scope.row.mobile)" v-if="scope.row.is_ebank_auth === 1">查看报告</el-button>
          <span class="unfilled-red" v-else="scope.row.is_ebank_auth === 0">未填写</span>
        </template>
      </el-table-column>
      <el-table-column property="is_bill_auth" label="信用卡账单" align="center" min-width="100">
        <template slot-scope="scope">
          <el-button type="success" size="small" @click.native="handleJumpBill(scope.row.mobile)" v-if="scope.row.is_bill_auth === 1">查看报告</el-button>
          <span class="unfilled-red" v-else="scope.row.is_bill_auth === 0">未填写</span>
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
  name: 'increaseQuota',
  data() {
    return {
      tableData: [],
      form: {
        mobile: '',
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
      if (this.form.mobile !== '') {
        params.mobile = this.form.mobile;
      }
      if (this.form.real_name !== '') {
        params.real_name = this.form.real_name;
      }
      this.$http.get(`${apiBase}increase-quota`, { params })
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
        real_name: '',
      };
      this.getData();
    },
    authFormatter(v) {
      switch (v) {
        case 0 :
          return '未填写';
        case 1 :
          return '已填写';
        case 2 :
          return '暂无';
        default:
          return '';
      }
    },
    handleJumpEdu(v) { // 学历认证
      this.$router.push({
        path: '/auth-edu',
        query: { mobile: v },
      });
    },
    handleJumpTaobao(v) { // 淘宝
      this.$router.push({
        path: '/auth-taobao',
        query: { mobile: v },
      });
    },
    handleJumpJd(v) { // 京东
      this.$router.push({
        path: '/auth-jd',
        query: { mobile: v },
      });
    },
    handleJumpBill(v) { // 信用卡账单
      this.$router.push({
        path: '/auth-bill',
        query: { mobile: v },
      });
    },
    handleJumpEbank(v) { // 网银流水
      this.$router.push({
        path: '/auth-ebank',
        query: { mobile: v },
      });
    },
  },
};
</script>
<style>
 .fill-out-green{
   color:green
 }
 .unfilled-red{
   color: red;
 }
</style>