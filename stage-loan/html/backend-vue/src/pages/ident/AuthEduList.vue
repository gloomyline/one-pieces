<template>
  <div>
    <el-row>
      <el-col :span="24"  v-if="!this.$route.query.mobile">
        <el-form :inline="true">
          <el-form-item>
            <el-input v-model="form.user_name" placeholder="真实姓名"></el-input>
          </el-form-item>
          <el-form-item>
            <el-input v-model="form.mobile" placeholder="手机号码"></el-input>
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
      <el-row v-if="this.$route.query.mobile">
        <el-button type="success" style="margin-bottom: 5px" @click.native="$router.back(-1)">返回上一页</el-button>
      </el-row>
    </el-row>
    <el-table :data="tableData" stripe border style="width: 100%;">
      <el-table-column property="id" label="ID" align="center"></el-table-column>
      <el-table-column property="user_name" label="用户姓名" align="center"></el-table-column>
      <el-table-column property="mobile" label="手机号码" align="center"></el-table-column>
      <el-table-column property="state" label="状态" align="center" :formatter="stateFormatter"></el-table-column>
      <el-table-column property="has_report" label="是否生成报告" align="center" :formatter="reportFormatter"></el-table-column>
      <el-table-column property="created_at" label="添加时间" align="center" width="180"></el-table-column>
      <el-table-column label="操作" align="center">
        <template slot-scope="scope">
          <el-button-group>
            <el-button type="primary" size="small"  @click.native="handleDetailInfoShow(scope.row.id)" v-if="scope.row.has_report === 1">查看</el-button>
            <el-button type="danger" size="small"  @click.native="delEduAuth(scope.row.id)">删除</el-button>
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
  name: 'auth-edu',
  data() {
    return {
      tableData: [],
      form: {
        user_name: '',
        state: '',
        mobile: '',
      },
      pageCount: 0,
      currentPage: 1,
      pageSize: 20,
      msg: '',
      states: [{ id: ' ', name: '全部' }, { id: 'busy', name: '待认证' }, { id: 'nopass', name: '认证失败' }, { id: 'pass', name: '认证成功' }],
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
      if (this.form.mobile !== '') {
        params.mobile = this.form.mobile;
      }
      if (this.form.state !== '') {
        params.state = this.form.state;
      }
      if (this.$route.query.mobile) {
        params.mobile = this.$route.query.mobile;
      }
      this.$http.get(`${apiBase}auth-edu`, { params })
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
    reportFormatter(row) {
      return row.has_report === 1 ? '已生成' : '未生成';
    },
    handleDetailInfoShow(id) {
      this.$router.push({
        path: `/edu-detail/${id}`,
      });
    },
    delEduAuth(id) {
      this.$confirm('确定删除该记录?', '提示', {
        type: 'warning',
      }).then(() => {
        this.$http.post(`${apiBase}del-limu`, { limu_id: id })
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
};
</script>