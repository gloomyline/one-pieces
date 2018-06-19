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
            <el-input v-model="form.bank_no" placeholder="银行卡"></el-input>
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
      <el-table-column property="id" label="ID" align="center" min-width="80"></el-table-column>
      <el-table-column property="user_name" label="用户名" align="center" min-width="130"></el-table-column>
      <el-table-column property="real_name" label="真实姓名" align="center" min-width="110"></el-table-column>
      <el-table-column property="identity_no" label="身份证" align="center" min-width="180"></el-table-column>
      <el-table-column property="bank_no" label="银行卡" align="center" min-width="185"></el-table-column>
      <el-table-column property="opening_bank_name" label="开户行" align="center" min-width="150"></el-table-column>
      <el-table-column property="state" label="状态" align="center" min-width="80" :formatter="stateFormatter"></el-table-column>
      <el-table-column property="is_default" label="默认银行卡" align="center" min-width="120">
        <template slot-scope="scope">
          <span>{{ scope.row.is_default | isDefaultFilter }}</span>
        </template>
      </el-table-column>
      <el-table-column property="created_at" label="添加时间" align="center" width="180"></el-table-column>
      <el-table-column label="操作" align="center" min-width="70">
        <template slot-scope="scope">
          <el-button-group>
            <el-button type="danger" size="small"  @click.native="handleDelBank(scope.row.id)">删 除</el-button>
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
        user_name: '',
        state: '',
        real_name: '',
        bank_no: '',
      },
      pageCount: 0,
      currentPage: 1,
      pageSize: 20,
      msg: '',
      states: [{ id: 'valid', name: '有效' }, { id: 'invalid', name: '无效' }],
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
      if (this.form.bank_no !== '') {
        params.bank_no = this.form.bank_no;
      }
      if (this.form.state !== '') {
        params.state = this.form.state;
      }
      this.$http.get(`${apiBase}auth-bank`, { params })
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
        bank_no: '',
      };
      this.getData();
    },
    stateFormatter(row) {
      return this.states.find(v => v.id === row.state).name;
    },
    handleDelBank(id) {
      this.$confirm('确定删除该记录?', '提示', {
        type: 'warning',
      }).then(() => {
        this.$http.post(`${apiBase}del-bank`, { bank_id: id })
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
    isDefaultFilter(v) {
      switch (v) {
        case 0 :
          return '否';
        case 1 :
          return '是';
        default:
          return '';
      }
    },
  },
};
</script>