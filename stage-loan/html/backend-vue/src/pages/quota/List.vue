<template>
  <div>
    <el-row>
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
      <el-table-column property="id" label="ID" align="center" min-width="80"></el-table-column>
      <el-table-column property="mobile" label="用户名" align="center" min-width="150"></el-table-column>
      <el-table-column property="real_name" label="真实姓名" align="center" min-width="120"></el-table-column>
      <el-table-column property="total_quota" label="总额度" align="center" min-width="100"></el-table-column>
      <el-table-column property="available_quota" label="可用额度" align="center" min-width="100"></el-table-column>
      <el-table-column property="fronzen_quota" label="冻结额度" align="center" min-width="100"></el-table-column>
      <el-table-column property="promoted_quota" label="已提升额度" align="center" min-width="100">
        <template slot-scope="scope">
          <span v-text="scope.row.total_quota > 0 ? scope.row.total_quota - userBasicQuota : 0"></span>
        </template>
      </el-table-column>
      <el-table-column label="操作" align="center" min-width="180">
        <template slot-scope="scope">
          <el-button-group>
            <el-button type="primary" size="small"  @click.native="promoteQuota(scope.row)">添加额度</el-button>
            <el-button type="primary" size="small" @click.native="handelViewAuthJump(scope.row.mobile)">查 看</el-button>
          </el-button-group>
        </template>
      </el-table-column>
    </el-table>
    <page :total="pageCount" :current-page="currentPage" :page-size="pageSize" @current-change="handleCurrentChange"></page>

    <div>
      <el-dialog :visible.sync="dialogFormVisible" :title='dialogForm.title'>
        <el-form :model="dialogForm" ref="dialogForm" label-width="120px" label-position="left">
          <el-form-item label="用户名称">
            <span v-text="dialogForm.mobile"></span>
          </el-form-item>
          <el-form-item label="当前总额度">
            <span v-text="dialogForm.totalQuota"></span>
          </el-form-item>
          <el-form-item label="操作类型"  required>
            <el-radio-group v-model="dialogForm.act">
              <el-radio :label="1">增加</el-radio>
              <el-radio :label="0">减少</el-radio>
            </el-radio-group>
          </el-form-item>
          <el-form-item label="额度金额" required>
            <el-input-number v-model="dialogForm.applyQuota" controls-position="right"  :min="0" :max="20000" :step="500"></el-input-number>
          </el-form-item>
        </el-form>
        <span slot="footer" class="dialog-footer">
          <el-button type="primary" @click.native="handelSubmit">保存</el-button>
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
        real_name: '',
      },
      dialogForm: {
        title: '添加额度',
        mobile: '',
        id: '',
        totalQuota: 0,
        act: 1,
        applyQuota: 0,
      },
      userBasicQuota: 0,
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
      if (this.form.mobile !== '') {
        params.mobile = this.form.mobile;
      }
      if (this.form.real_name !== '') {
        params.real_name = this.form.real_name;
      }
      this.$http.get(`${apiBase}quotas`, { params })
      .then(response => response.json())
      .then((json) => {
        if (json.status === 'SUCCESS') {
          this.tableData = json.results;
          this.pageCount = json.count;
          this.userBasicQuota = json.user_basic_quota;
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
    handelViewAuthJump(mobile) {
      this.$router.push({
        path: '/auth-center',
        query: { Mobile: mobile },
      });
    },
    promoteQuota(row) {
      this.dialogFormVisible = true;
      this.dialogForm.mobile = row.mobile;
      this.dialogForm.totalQuota = row.total_quota;
      this.dialogForm.id = row.id;
      this.dialogForm.act = 1;
      this.dialogForm.applyQuota = 0;
    },
    handelSubmit() {
      const amount = Number(this.dialogForm.applyQuota);
      if (!Number.isInteger(amount) || amount < 0) {
        this.$message('金额只能为正整数');
        return;
      }
      const params = {
        user_id: this.dialogForm.id,
      };
      if (this.dialogForm.act === 1) {
        params.apply_quota = this.dialogForm.applyQuota;
      }
      if (this.dialogForm.act === 0) {
        params.apply_quota = -Number(this.dialogForm.applyQuota);
      }
      this.$http.post(`${apiBase}quota-apply`, params)
        .then(response => response.json())
        .then((json) => {
          if (json.status !== 'SUCCESS') {
            return Promise.reject(json.error_message);
          }
          this.$message({
            type: 'success',
            message: '添加成功！',
          });
          // this.getData();
          this.dialogFormVisible = false;
          return {};
        }).catch(reportErrorMessage(this));
    },
  },
};
</script>