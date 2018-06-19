<template>
  <div>
    <el-row>
          <el-col :span="24">
            <el-form :inline="true">
              <el-form-item>
                <el-input v-model="form.real_name" placeholder="真实姓名"></el-input>
              </el-form-item>
              <el-form-item>
                <el-input v-model="form.mobile" placeholder="手机号"></el-input>
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
      <el-table-column property="encoding" label="借款编号" align="center" min-width="235"></el-table-column>
      <el-table-column property="real_name" label="真实姓名" align="center" min-width="120"></el-table-column>
      <el-table-column property="mobile" label="手机号" align="center" min-width="150"></el-table-column>
      <el-table-column property="quota" label="申请金额" align="center" min-width="100">
        <template slot-scope="scope">
          <span>{{ scope.row.quota }} 元</span>
        </template>
      </el-table-column>
      <el-table-column property="period" label="申请期限" align="center" min-width="100">
        <template slot-scope="scope">
          <span>{{ scope.row.period }} 天</span>
        </template>
      </el-table-column>
      <el-table-column property="created_at" label="申请时间" align="center" min-width="170"></el-table-column>
      <el-table-column property="check_at" label="初审时间" align="center" min-width="170"></el-table-column>
      <el-table-column property="review_at" label="复审时间" align="center" min-width="170"></el-table-column>
      <el-table-column property="lending_at" label="放款时间" align="center" min-width="170"></el-table-column>
      <el-table-column property="state" label="借款状态" align="center" min-width="100" fixed="right">
        <template slot-scope="scope">
          <span :class="{ 'overdue-red' : scope.row.state === 'overdue' }" v-text="stateFormatter(scope.row)"></span>
        </template>
      </el-table-column>
      <el-table-column property="preliminary_officer" label="初审人员" align="center" min-width="120"></el-table-column>
      <el-table-column property="review_officer" label="复审人员" align="center" min-width="130"></el-table-column>
      <el-table-column label="操作" fixed="right" align="center" width="150">
        <template slot-scope="scope">
          <el-button-group>
            <el-button type="primary" size="small" @click.native="handleJump(scope.row.id, 'checks')"  v-if="scope.row.state === 'auditing'">初审</el-button>
            <el-button type="primary" size="small" @click.native="handleJump(scope.row.id, 'reviews')"  v-if="scope.row.state === 'audit_success'">复审</el-button>
            <el-button type="primary" :disabled="scope.row.lending_disabled" size="small" @click.native="setLending(scope.row.id)"  v-if="scope.row.state === 'review_success'">放款</el-button>
            <el-button type="primary" size="small" @click.native="handleDetail(scope.row.id)">查看</el-button>
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
  name: 'loan',
  data() {
    return {
      form: {
        real_name: '',
        mobile: '',
        state: '',
      },
      old_name: '',
      tableData: [],
      pageCount: 0,
      currentPage: 1,
      pageSize: 20,
      states: [{ id: 'all', name: '全部' }, { id: 'auditing', name: '待初审' }, { id: 'audit_failure', name: '初审失败' }, { id: 'reviewing', name: '待复审' }, { id: 'review_success', name: '复审成功' }, { id: 'review_failure', name: '复审失败' }, { id: 'granting', name: '放款中' }, { id: 'repaying', name: '还款中' }, { id: 'overdue', name: '逾期中' }, { id: 'finished', name: '已还完' }],

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
      if (this.form.real_name !== '') {
        params.real_name = this.form.real_name;
      }
      if (this.form.mobile !== '') {
        params.mobile = this.form.mobile;
      }
      if (this.form.state !== '') {
        params.state = this.form.state;
      }
      this.$http.get(`${apiBase}borrows`, { params })
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
        real_name: '',
        mobile: '',
        state: '',
      };
      this.getData();
    },
    stateFormatter(row) {
      return this.states.find(v => v.id === row.state).name;
    },
    handleJump(id, current) {
      if (current === 'checks') {
        this.$router.push({
          path: `/checks-audit/${id}`,
        });
      } else {
        this.$router.push({
          path: `/reviews-audit/${id}`,
        });
      }
    },
    handleDetail(id) {
      this.$router.push({
        path: `/loan-detail/${id}`,
      });
    },
    setLending(id) {
      this.$http.post(`${apiBase}lending`, { loan_id: id })
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
<style type="text/css">
  .overdue-red{
    color:red;
  }
</style>