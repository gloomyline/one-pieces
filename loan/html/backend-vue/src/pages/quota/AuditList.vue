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
      <el-table-column property="apply_quota" label="申请额度" align="center" min-width="100" :formatter="quotaFormatter"></el-table-column>
      <el-table-column property="apply_type" label="额度类型" align="center" min-width="120">
        <template slot-scope="scope">
          <span>{{ scope.row.apply_type | filterType }}</span>
        </template>
      </el-table-column>
      <el-table-column property="state" label="状态" align="center" min-width="100">
        <template slot-scope="scope">
          <span>{{ scope.row.state | filterState }}</span>
        </template>
      </el-table-column>
      <el-table-column property="created_at" label="申请时间" align="center" min-width="180"></el-table-column>
      <el-table-column label="操作" align="center" min-width="150">
        <template slot-scope="scope">
          <el-button-group style="">
            <el-button type="primary" size="small" @click.native="auditQuota(scope.row)">审 核</el-button>
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
          <el-form-item label="申请额度">
            <span v-text="dialogForm.applyQuota"></span>
          </el-form-item>
          <el-form-item label="通过金额" required>
            <el-input type="number" min="0" :max="Math.abs(dialogForm.applyQuota)" step="100" v-model="dialogForm.allowQuota" style="width: 220px">
              <template slot="prepend" v-if="dialogForm.applyQuota < 0">-</template>
              <template slot="prepend" v-if="dialogForm.applyQuota > 0">+</template>
            </el-input>
          </el-form-item>
          <el-form-item label="审核状态"  required>
            <el-radio-group v-model="dialogForm.state">
              <el-radio :label="1">通过</el-radio>
              <el-radio :label="2">失败</el-radio>
            </el-radio-group>
          </el-form-item>
          <el-form-item label="备注" >
            <el-input
                    type="textarea"
                    :rows="4"
                    style="width: 60%"
                    placeholder="请输入内容"
                    v-model="dialogForm.content">
            </el-input>
          </el-form-item>
        </el-form>
        <span slot="footer" class="dialog-footer">
          <el-button type="primary" @click.native="handelSubmit">保存</el-button>
          <el-button @click.native="dialogFormVisible = false">关闭</el-button>
        </span>
      </el-dialog>
    </div>

    <div>
      <el-dialog :visible.sync="dialogFormUserVisible" :title='dialogForm.title'>
        <el-form :model="dialogForm" ref="dialogForm" label-width="120px" label-position="left">
          <el-form-item label="用户名称">
            <span v-text="dialogForm.mobile"></span>
          </el-form-item>
          <el-form-item label="当前总额度">
            <span v-text="dialogForm.totalQuota"></span>
          </el-form-item>
          <el-form-item label="来源">
            <span>用户申请</span>
          </el-form-item>
          <el-form-item label="操作类型"  required>
            <el-radio-group v-model="dialogForm.act">
              <el-radio :label="1">增加</el-radio>
              <el-radio :label="0">减少</el-radio>
            </el-radio-group>
          </el-form-item>
          <el-form-item label="通过金额" required>
            <el-input type="number" min="0" :max="2000" step="100" v-model="dialogForm.allowQuota" style="width: 220px"></el-input>
          </el-form-item>
          <el-form-item label="审核状态"  required>
            <el-radio-group v-model="dialogForm.state">
              <el-radio :label="1">通过</el-radio>
              <el-radio :label="2">失败</el-radio>
            </el-radio-group>
          </el-form-item>
          <el-form-item label="备注" >
            <el-input
                    type="textarea"
                    :rows="4"
                    style="width: 500px"
                    placeholder="请输入内容"
                    v-model="dialogForm.content">
            </el-input>
          </el-form-item>
        </el-form>
        <span slot="footer" class="dialog-footer">
          <el-button type="primary" @click.native="handelSubmit">保存</el-button>
          <el-button @click.native="dialogFormUserVisible = false">关闭</el-button>
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
      dialogForm: {
        title: '添加额度',
        mobile: '',
        id: '',
        totalQuota: 0,
        state: 1,
        act: 1,
        applyQuota: 0,
        allowQuota: 0,
        content: '',
      },
      pageCount: 0,
      currentPage: 1,
      pageSize: 20,
      msg: '',
      dialogFormVisible: false,
      dialogFormUserVisible: false,
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
      this.$http.get(`${apiBase}quota-audit`, { params })
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
    handelViewAuthJump(mobile) {
      this.$router.push({
        path: '/auth-center',
        query: { Mobile: mobile },
      });
    },
    auditQuota(row) {
      this.dialogForm.mobile = row.mobile;
      this.dialogForm.totalQuota = row.total_quota;
      this.dialogForm.id = row.id;
      this.dialogForm.state = 1;
      if (row.apply_type === 1) { // 用户申请
        this.dialogForm.act = 1;
        this.dialogFormUserVisible = true;
      } else {
        this.dialogForm.act = '';
        this.dialogFormVisible = true;
        this.dialogForm.applyQuota = row.apply_quota;
        this.dialogForm.allowQuota = Math.abs(row.apply_quota);
      }
    },
    handelSubmit() {
      const amount = Number(this.dialogForm.allowQuota);
      if (!Number.isInteger(amount) || amount < 0) {
        this.$message('金额只能为正整数');
        return;
      }
      const params = {
        quota_apply_id: this.dialogForm.id,
        allow_quota: this.dialogForm.allowQuota,
        state: this.dialogForm.state,
        content: this.dialogForm.content,
      };
      if (this.dialogForm.act === 1) {
        params.allow_quota = this.dialogForm.allowQuota;
      }
      if (this.dialogForm.act === 0) {
        params.allow_quota = -Number(this.dialogForm.allowQuota);
      }
      this.$http.post(`${apiBase}audit-quota`, params)
        .then(response => response.json())
        .then((json) => {
          if (json.status !== 'SUCCESS') {
            return Promise.reject(json.error_message);
          }
          this.$message({
            type: 'success',
            message: '操作成功！',
          });
          this.getData();
          this.dialogFormVisible = false;
          this.dialogFormUserVisible = false;
          return {};
        }).catch(reportErrorMessage(this));
    },
    quotaFormatter(row) {
      return row.apply_type === 1 ? '' : row.apply_quota;
    },
  },
  filters: {
    filterState(v) {
      switch (v) {
        case 0 :
          return '待审核';
        case 1 :
          return '审核通过';
        case 2 :
          return '审核失败';
        default:
          return '';
      }
    },
    filterType(v) {
      switch (v) {
        case 0 :
          return '后台添加';
        case 1 :
          return '用户申请';
        case 2 :
          return '系统添加';
        default:
          return '';
      }
    },
  },
};
</script>