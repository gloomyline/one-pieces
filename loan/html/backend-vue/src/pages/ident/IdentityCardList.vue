<template>
  <div>
    <el-row>
      <el-col :span="24">
        <el-form :inline="true">
          <el-form-item>
            <el-input v-model="form.user_name" placeholder="用户名"></el-input>
          </el-form-item>
          <el-form-item>
            <el-input v-model="form.real_name" placeholder="真实姓名"></el-input>
          </el-form-item>
          <el-form-item>
            <el-input v-model="form.identity_no" placeholder="身份证"></el-input>
          </el-form-item>
          <el-form-item>
            <el-select v-model="form.state" placeholder="状态" clearable>
              <el-option 
                v-for="item in states" 
                :label="item.name" 
                :value="item.id">
              </el-option>
            </el-select>
          </el-form-item>
          <el-form-item>
            <el-button type="primary" @click.native="filter">查找</el-button>
            <el-button type="primary" @click.native="clearFilter">清空筛选</el-button>
          </el-form-item>
        </el-form>
      </el-col>
    </el-row>
    <el-table :data="tableData" stripe border style="width: 100%;">
      <el-table-column property="id" label="ID" align="center"></el-table-column>
      <el-table-column property="user_name" label="用户名" align="center" min-width="120"></el-table-column>
      <el-table-column property="real_name" label="真实姓名" align="center"></el-table-column>
      <el-table-column property="identity_no" label="身份证" align="center" min-width="180"></el-table-column>
      <el-table-column property="state" label="状态" align="center" :formatter="stateFormatter"></el-table-column>
      <el-table-column property="created_at" label="添加时间" align="center" width="180"></el-table-column>
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
        state: '',
        real_name: '',
        identity_no: '',
      },
      pageCount: 0,
      currentPage: 1,
      pageSize: 20,
      msg: '',
      states: [{ id: 0, name: '未填写' }, { id: 1, name: '已填写' }],
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
      if (this.form.real_name !== '') {
        params.real_name = this.form.real_name;
      }
      if (this.form.identity_no !== '') {
        params.identity_no = this.form.identity_no;
      }
      if (this.form.state !== '') {
        params.state = this.form.state;
      }
      this.$http.get(`${apiBase}identity`, { params })
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
        state: '',
        identity_no: '',
      };
      this.getData();
    },
    stateFormatter(row) {
      return this.states.find(v => v.id === row.state).name;
    },
  },
};
</script>