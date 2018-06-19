<template>
  <div>
    <el-row>
      <el-col :span="24">
        <el-form :inline="true">
          <el-form-item>
            <el-input v-model="form.username" placeholder="登陆名"></el-input>
          </el-form-item>
          <el-form-item>
            <el-input v-model.trim="form.real_name" placeholder="真实姓名"></el-input>
          </el-form-item>
          <el-form-item>
            <el-button type="primary" @click.native="filter">查找</el-button>
            <el-button type="primary" @click.native="clearFilter">清空筛选</el-button>
            <el-button type="success" @click.native="handExport('urge')">导出</el-button>
          </el-form-item>
        </el-form>
      </el-col>
    </el-row>
    <el-table :data="tableData" stripe border style="width: 100%;">
      <el-table-column property="id" label="ID" align="center" min-width="80"></el-table-column>
      <el-table-column property="username" label="登陆名" align="center" min-width="120"></el-table-column>
      <el-table-column property="real_name" label="真实姓名" align="center" min-width="100"></el-table-column>
      <el-table-column property="role_name" label="角色" align="center" min-width="100"></el-table-column>
      <el-table-column property="assigned" label="已分配笔数" align="center" min-width="100"></el-table-column>
      <el-table-column property="waiting_urge_count" label="等待催收笔数" align="center" min-width="100"></el-table-column>
      <el-table-column property="repaying_urge_count" label="催收未还笔数" align="center" min-width="100"></el-table-column>
      <el-table-column property="finished_urge_count" label="已还款笔数" align="center" min-width="100"></el-table-column>
      <el-table-column property="finished_urge_amount" label="已催回总额(元)" align="center" min-width="100"></el-table-column>
      <el-table-column property="other_urge_amount" label="剩余未催回总额(元)" align="center" min-width="100">
        <template slot-scope="scope">
          <span>{{ Number(scope.row.waiting_urge_amount) + Number(scope.row.repaying_urge_amount) }}</span>
        </template>
      </el-table-column>
      <el-table-column property="bad_urge_count" label="坏账数" align="center" min-width="100"></el-table-column>
      <el-table-column property="bad_urge_amount" label="坏账总额" align="center" min-width="100"></el-table-column>
    </el-table>
    <page :total="pageCount" :current-page="currentPage" :page-size="pageSize" @current-change="handleCurrentChange"></page>

  </div>
</template>
<script>
import apiBase from '../../apiBase';
import Page from '../../components/Page';

export default {
  name: 'urgeStatistics',
  data() {
    return {
      tableData: [],
      form: {
        username: '',
        real_name: '',
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
      if (this.form.username !== '') {
        params.username = this.form.username;
      }
      if (this.form.real_name !== '') {
        params.real_name = this.form.real_name;
      }
      this.$http.get(`${apiBase}urge-statistics`, { params })
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
        username: '',
        real_name: '',
      };
      this.getData();
    },
    handExport(v) {
      const params = {
        limit: this.pageSize,
        offset: this.pageSize * (this.currentPage - 1),
      };
      if (this.form.username !== '') {
        params.username = this.form.username;
      } else {
        params.username = '';
      }
      if (this.form.real_name !== '') {
        params.real_name = this.form.real_name;
      } else {
        params.real_name = '';
      }
      window.open(`${apiBase}exp-excel?act=${v}&limit=${params.limit}&offset=${params.offset}&username=${params.username}&real_name=${params.real_name}&page=${this.currentPage}`);
    },
  },
};
</script>