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
          <el-form-item label="注册时间范围">
            <el-date-picker v-model="form.dateRange" type="daterange" align="right" ></el-date-picker>
          </el-form-item>
          <el-form-item>
            <el-button type="primary" @click.native="filter">查找</el-button>
            <el-button type="primary" @click.native="clearFilter">清空筛选</el-button>
          </el-form-item>
        </el-form>
      </el-col>
    </el-row>
    <el-table :data="tableData" stripe border style="width: 100%;">
      <el-table-column property="id" label="ID" align="center" width="100"></el-table-column>
      <el-table-column property="real_name" label="真实姓名" align="center" width="150"></el-table-column>
      <el-table-column property="mobile" label="手机号" align="center" width="180"></el-table-column>
      <el-table-column property="success_count" label="成功借款次数" align="center" width="100"></el-table-column>
      <el-table-column property="referrals" label="推广人" align="center" width="150"></el-table-column>
      <el-table-column property="created_at" label="注册时间" align="center" width="180"></el-table-column>
      <el-table-column property="state" label="用户类型" align="center" width="180" :formatter="stateFormatter"></el-table-column>
      <el-table-column label="操作" align="center" min-width="150">
        <template slot-scope="scope">
          <el-button-group style="">
            <el-button type="primary" size="small"  @click.native="handleDetail(scope.row.id)">查看</el-button>
            <el-button type="danger" size="small" @click.native="setSwitch(scope.row.id)" v-if="scope.row.is_forbidden === 1">禁用</el-button>
            <el-button type="primary" size="small" @click.native="setSwitch(scope.row.id)" v-if="scope.row.is_forbidden === 2">启用</el-button>
          </el-button-group>
        </template>
      </el-table-column>
    </el-table>
    <page :total="pageCount" :current-page="currentPage" :page-size="pageSize" @current-change="handleCurrentChange"></page>

    <div>
      <el-dialog v-model="dialogFormVisible">
        <p style="margin-left:50px; line-height: 25px;" v-html="msg"></p>
        <span slot="footer" class="dialog-footer">
          <el-button @click.native="dialogFormVisible = false">关闭</el-button>
        </span>
      </el-dialog>
    </div>

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
        state: '',
        real_name: '',
        dateRange: {
          key: 'begin_at end_at',
          type: 'datePair',
        },
      },
      pageCount: 0,
      currentPage: 1,
      pageSize: 20,
      msg: '',
      dialogFormVisible: false,
      states: [{ id: 0, name: '所有用户' }, { id: 1, name: '注册未申请' }, { id: 2, name: '正常用户' }, { id: 3, name: '逾期用户' }, { id: 4, name: '黑名单' }],
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
      if (this.form.dateRange && this.form.dateRange[0] && this.form.dateRange[1]) {
        params.start_at = Date.parse(this.form.dateRange[0]) / 1000;
        params.end_at = Date.parse(this.form.dateRange[1]) / 1000;
      }
      if (this.form.mobile !== '') {
        params.mobile = this.form.mobile;
      }
      if (this.form.real_name !== '') {
        params.real_name = this.form.real_name;
      }
      if (this.form.state !== '') {
        params.state = this.form.state;
      }
      this.$http.get(`${apiBase}users`, { params })
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
    setSwitch(id) {
      this.$confirm('确定切换该用户的状态?', '提示', {
        type: 'warning',
      }).then(() => {
        this.$http.put(`${apiBase}user-forbid`, { user_id: id })
          .then(response => response.json())
          .then((json) => {
            if (json.status !== 'SUCCESS') {
              return Promise.reject(json.error_message);
            }
            this.$message({
              type: 'success',
              message: '设置成功！',
            });
            this.getData();
            return {};
          }).catch(reportErrorMessage(this));
      }).catch(() => {});
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
        real_name: '',
        state: '',
        dateRange: '',
      };
      this.getData();
    },
    stateFormatter(row) {
      return this.states.find(v => v.id === row.state).name;
    },


  },
};
</script>