<template>
  <div>
    <el-row v-if="flag === false">
      <el-col :span="24">
        <el-form :inline="true">
          <el-form-item>
            <el-input v-model="form.real_name" placeholder="真实姓名" :disabled="flag"></el-input>
          </el-form-item>
          <el-form-item>
            <el-input v-model="form.mobile" placeholder="手机号" :disabled="flag"></el-input>
          </el-form-item>
          <el-form-item label="计划还款时间">
            <el-date-picker v-model="form.dateRange" type="daterange" align="right" ></el-date-picker>
          </el-form-item>
          <el-form-item>
            <el-button type="primary" @click.native="filter">查找</el-button>
            <el-button type="primary" @click.native="clearFilter">清空筛选</el-button>
            <el-button type="primary" @click.native="handleJump">查看已分配记录</el-button>
          </el-form-item>
        </el-form>
      </el-col>
    </el-row>
    <el-row v-if="flag === true">
      <el-button type="success" style="margin-bottom: 5px" @click.native="$router.back(-1)">返回上一页</el-button>
    </el-row>
    <el-table :data="tableData" stripe border style="width: 100%;">
      <el-table-column property="id" label="ID" align="center" width="80"></el-table-column>
	  <el-table-column property="encoding" label="订单编号" align="center" min-width="225"></el-table-column>
      <el-table-column property="real_name" label="真实姓名" align="center" min-width="110"></el-table-column>
      <el-table-column property="mobile" label="手机号" align="center" min-width="125"></el-table-column>
      <el-table-column property="arrival_amount" label="借款金额(元)" align="center" min-width="100"></el-table-column>
      <el-table-column property="period" label="借款期限(天)" align="center" min-width="90"></el-table-column>
      <el-table-column property="lending_at" label="放款时间" align="center"  width="180"></el-table-column>
      <el-table-column property="planned_repayment_at" label="计划还款时间" align="center" min-width="120"></el-table-column>
	  <el-table-column property="overdue_amount" label="逾期费用(元)" align="center" min-width="100"></el-table-column>
	  <el-table-column property="overdue_days" label="逾期天数(天)" align="center" min-width="80"></el-table-column>
	  <el-table-column property="should_repayment_amount" label="应还款金额(元)" align="center" min-width="100"></el-table-column>
	  <el-table-column property="total_repayment_amount" label="应还款总额(元)" align="center" min-width="100"></el-table-column>
      <el-table-column label="操作" align="center" width="100">
        <template slot-scope="scope">
          <el-button-group style="">
            <el-button type="primary" size="small"  @click.native="showDialog(scope.row.id, '')" v-if="scope.row.urge_id === ''">分配</el-button>
            <el-button type="primary" size="small"  @click.native="showDialog(scope.row.id, scope.row.admin_id)" v-if="scope.row.urge_id !== ''">修改分配</el-button>
          </el-button-group>
        </template>
      </el-table-column>
    </el-table>
    <page :total="pageCount" :current-page="currentPage" :page-size="pageSize" @current-change="handleCurrentChange"></page>
    <div>
      <el-dialog :visible.sync="dialogFormVisible">
        <el-form :model="dialogForm" ref="dialogForm" label-width="120px">
          <el-form-item  label="选择催收员" prop="staff" required>
            <el-select v-model="formData.staff" placeholder="请选择" clearable>
              <el-option-group
                      v-for="group in options"
                      :key="group.label"
                      :label="group.label">
                <el-option
                        v-for="item in group.options"
                        :key="item.value"
                        :label="item.admin_name"
                        :value="item.admin_id">
                </el-option>
              </el-option-group>
            </el-select>
          </el-form-item>
        </el-form>
        <span slot="footer" class="dialog-footer">
        <el-button @click.native="dialogFormVisible = false">取 消</el-button>
          <el-button type="primary" @click.native.prevent="handleAssign">确 定</el-button>
        </span>
      </el-dialog>
    </div>
  </div>
</template>
<script>
import apiBase from '../../apiBase';
import Page from '../../components/Page';
import { getJsonAndCheckSuccess, reportErrorMessage } from '../../ApiUtil';

export default {
  name: 'overdue',
  data() {
    return {
      id: this.$route.query.Id,
      tableData: [],
      form: {
        mobile: '',
        real_name: '',
        dateRange: {
          key: 'begin_at end_at',
          type: 'datePair',
        },
      },
      options: [],
      formData: {
        staff: '',
        loin_id: '',
      },
      flag: false,
      pageCount: 0,
      currentPage: 1,
      pageSize: 20,
      msg: '',
      dialogFormVisible: false,
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
      if (this.id) {
        params.id = this.id;
        this.flag = true;
      }
      this.$http.get(`${apiBase}overdues`, { params })
      .then(response => response.json())
      .then((json) => {
        if (json.status === 'SUCCESS') {
          this.tableData = json.results;
          this.pageCount = json.count;
        }
      }).catch(() => {
        this.$message('系统错误');
      });
      this.$http.get(`${apiBase}staffs`)
      .then(getJsonAndCheckSuccess)
      .then((response) => {
        this.options = response.results;
      }).catch(reportErrorMessage(this));
    },
    handleCurrentChange(page) {
      this.currentPage = page;
      this.getData();
    },
    showDialog(id, adminId) {
      this.formData.loan_id = id;
      if (adminId === '') {
        this.formData.staff = '';
      } else {
        this.formData.staff = adminId;
      }
      this.dialogFormVisible = true;
    },
    handleAssign() {
      const params = {
        loan_id: this.formData.loan_id,
        admin_id: this.formData.staff,
      };
      this.$http.post(`${apiBase}assign`, params)
        .then(response => response.json())
        .then((json) => {
          if (json.status !== 'SUCCESS') {
            return Promise.reject(json.error_message);
          }
          this.$message({
            type: 'success',
            message: '分配成功！',
          });
          this.getData();
          this.dialogFormVisible = false;
          return {};
        }).catch(reportErrorMessage(this));
    },
    filter() {
      this.currentPage = 1;
      this.getData();
    },
    clearFilter() {
      this.form = {
        mobile: '',
        real_name: '',
        dateRange: '',
      };
      this.getData();
    },
    handleJump() {
      this.$router.push({
        path: '/urge-lists',
      });
    },
  },
};
</script>