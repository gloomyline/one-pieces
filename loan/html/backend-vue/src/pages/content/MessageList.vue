<template>
  <div>
    <el-row>
      <el-col :span="24">
        <el-form :inline="true">
          <el-form-item>
            <el-input v-model="form.mobile" placeholder="手机号"></el-input>
          </el-form-item>
          <el-form-item>
            <el-select v-model="form.type" placeholder="类型" clearable>
              <el-option 
                v-for="item in types"
                :label="item.name" 
                :value="item.id">
              </el-option>
            </el-select>
          </el-form-item>
          <el-form-item>
            <el-select v-model="form.state" placeholder="发送状态" clearable>
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
      <el-table-column property="real_name" label="真实姓名" align="center" min-width="120"></el-table-column>
      <el-table-column property="mobile" label="手机号" align="center" min-width="150"></el-table-column>
      <el-table-column property="type" label="类型" align="center" :formatter="typeFormatter" min-width="150"></el-table-column>
      <el-table-column property="send_message" label="内容" align="center" min-width="200"></el-table-column>
      <el-table-column property="created_at" label="发送时间" align="center" min-width="180"></el-table-column>
      <el-table-column property="state" label="发送状态" align="center" min-width="100" :formatter="stateFormatter">
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
        mobile: '',
        type: '',
        state: '',
      },
      pageCount: 0,
      currentPage: 1,
      pageSize: 20,
      types: [{ id: 'auth_code', name: '短信验证码' }, { id: 'loan', name: '放款通知' }, { id: 'repayment', name: '还款通知' }, { id: 'overdue', name: '逾期通知' }, { id: 'repay_succ', name: '还款成功' }, { id: 'withdraw', name: '提现通知' }, { id: 'overdue_mass', name: '逾期群发短信' }],
      states: [{ id: 1, name: '成功' }, { id: 2, name: '失败' }],
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
      if (this.form.type !== '') {
        params.type = this.form.type;
      }
      if (this.form.state !== '') {
        params.state = this.form.state;
      }
      this.$http.get(`${apiBase}messages`, { params })
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
    handleDetail(id) {
      this.$router.push({
        path: `/user-detail/${id}`,
      });
    },
    filter() {
      this.currentPage = 1;
      this.getData();
    },
    clearFilter() {
      this.form = {
        mobile: '',
        type: '',
        state: '',
      };
      this.getData();
    },
    typeFormatter(row) {
      return this.types.find(v => v.id === row.type).name;
    },
    stateFormatter(row) {
      return row.state === 'OK' ? '成功' : '失败';
    },
  },
};
</script>