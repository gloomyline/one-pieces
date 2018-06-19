<template>

    <div>
        <el-row>
           <!-- <el-col :span="24">
                <el-button type="success" style="margin-bottom: 5px" @click.native="$router.back(-1)">返回上一页</el-button>
            </el-col>-->
            <el-col :span="24">
                <el-form :inline="true">
                    <el-form-item>
                        <el-input v-model="form.shop_name" placeholder="商户名称"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-input v-model="form.shop_no" placeholder="商户号"></el-input>
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
            <el-table-column property="shop_name" label="商户名称" align="center" min-width="150"></el-table-column>
            <el-table-column property="shop_no" label="商户号" align="center" min-width="120"></el-table-column>
            <el-table-column property="total_quota" label="总额度" align="center" min-width="100"></el-table-column>
            <el-table-column property="available_quota" label="可用额度" align="center" min-width="100"></el-table-column>
           <!-- <el-table-column property="fronzen_quota" label="冻结额度" align="center" min-width="100"></el-table-column>-->
            <el-table-column property="apply_total" label="总额度申请" align="center" min-width="100">
                <template slot-scope="scope">
                    <span v-if="scope.row.apply_total < 0" style="color: red" v-text="scope.row.apply_total"></span>
                    <span v-if="scope.row.apply_total >= 0" v-text="scope.row.apply_total"></span>
                </template>
            </el-table-column>
            <el-table-column property="apply_daily_limit" label="单日限额申请" align="center" min-width="100">
                <template slot-scope="scope">
                    <span v-if="scope.row.apply_daily_limit < 0" style="color: red" v-text="scope.row.apply_daily_limit"></span>
                    <span v-if="scope.row.apply_daily_limit>= 0" v-text="scope.row.apply_daily_limit"></span>
                </template>
            </el-table-column>
            <el-table-column property="apply_single_limit" label="单笔限额申请" align="center" min-width="100">
                <template slot-scope="scope">
                    <span v-if="scope.row.apply_single_limit < 0" style="color: red" v-text="scope.row.apply_single_limit"></span>
                    <span v-if="scope.row.apply_single_limit >= 0" v-text="scope.row.apply_single_limit"></span>
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
                    <el-button-group>
                        <el-button type="primary" size="small" @click.native="auditQuota(scope.row)">审 核</el-button>
                        <el-button type="primary" size="small" @click.native="handelViewJump(scope.row.shop_id)">查 看</el-button>
                    </el-button-group>
                </template>
            </el-table-column>
        </el-table>
        <page :total="pageCount" :current-page="currentPage" :page-size="pageSize" @current-change="handleCurrentChange"></page>

        <div>
            <el-dialog :visible.sync="dialogFormVisible" title='商户额度审核'>
                <el-form :model="dialogForm" ref="dialogForm" label-width="120px" label-position="left">
                    <el-row>
                        <el-col :lg="24">商户名称：<span v-text="dialogForm.shop_name"></span></el-col>
                        <el-col :lg="24">商户号：<span v-text="dialogForm.shop_no"></span></el-col>
                        <el-col :lg="8">
                            <span style="width: 120px">当前总额度:  </span><span v-text="dialogForm.total_quota"></span>(元)
                        </el-col>
                        <el-col :lg="8">
                            <span style="width: 120px">当前单日限额:  </span><span v-text="dialogForm.daily_limit_quota"></span>(元)
                        </el-col>
                        <el-col :lg="8">
                            <span style="width: 120px">当前单笔限额:  </span><span v-text="dialogForm.single_limit_quota"></span>(元)
                        </el-col>
                    </el-row>
                    <h4 style="color: #003eff; padding-top:10px; border-top: solid 1px #000000">总额度</h4>
                    <el-form-item label="申请额度">
                        {{ dialogForm.apply_total }}
                    </el-form-item>
                    <el-form-item label="通过金额" required>
                        <el-input-number v-model="dialogForm.allow_total" controls-position="right"  :min="-900000" :max="900000" :step="500"></el-input-number>
                    </el-form-item>
                    <el-form-item label="审核状态"  required>

                        <el-radio-group v-model="dialogForm.state_total">
                            <el-radio :label="1">通过</el-radio>
                            <el-radio :label="2">失败</el-radio>
                        </el-radio-group>
                    </el-form-item>
                    <h4 style="color: #003eff; padding-top:10px; border-top: solid 1px #000000">单日限额</h4>
                    <el-form-item label="申请额度">
                        {{ dialogForm.apply_daily_limit }}
                    </el-form-item>
                    <el-form-item label="通过金额" required>
                        <el-input-number v-model="dialogForm.allow_daily_limit" controls-position="right"  :min="-900000" :max="900000" :step="500"></el-input-number>
                    </el-form-item>
                    <el-form-item label="审核状态"  required>
                        <el-radio-group v-model="dialogForm.state_daily_limit">
                            <el-radio :label="1">通过</el-radio>
                            <el-radio :label="2">失败</el-radio>
                        </el-radio-group>
                    </el-form-item>
                    <h4 style="color: #003eff; padding-top:10px; border-top: solid 1px #000000">单笔限额</h4>
                    <el-form-item label="申请额度">
                        {{ dialogForm.apply_single_limit}}
                    </el-form-item>
                    <el-form-item label="通过金额" required>
                        <el-input-number v-model="dialogForm.allow_single_limit" controls-position="right"  :min="-900000" :max="900000" :step="500"></el-input-number>
                    </el-form-item>
                    <el-form-item label="审核状态"  required>
                        <el-radio-group v-model="dialogForm.state_single_limit">
                            <el-radio :label="1">通过</el-radio>
                            <el-radio :label="2">失败</el-radio>
                        </el-radio-group>
                    </el-form-item>
                    <h4 style="color: #003eff; padding-top:10px; border-top: solid 1px #000000"></h4>
                    <el-form-item label="备注"  required>
                        <el-input
                                type="textarea"
                                :rows="4"
                                style="width: 80%"
                                placeholder="请输入内容"
                                v-model="dialogForm.memo">
                        </el-input>
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
    name: 'shopQuotaAudit',
    data() {
      return {
        tableData: [],
        form: {
          shop_name: '',
          shop_no: '',
        },
        dialogForm: {
          shop_name: '',
          shop_no: '',
          total_quota: '',
          single_limit_quota: '',
          daily_limit_quota: '',
          id: '',
          apply_total: 0,
          allow_total: 0,
          state_total: 1,
          apply_single_limit: 0,
          allow_single_limit: 0,
          state_single_limit: 1,
          apply_daily_limit: 0,
          allow_daily_limit: 0,
          state_daily_limit: 1,
          memo: '',
        },
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
        if (this.form.shop_name !== '') {
          params.shop_name = this.form.shop_name;
        }
        if (this.form.shop_no !== '') {
          params.shop_no = this.form.shop_no;
        }
        this.$http.get(`${apiBase}shop-quota-audit-list`, { params })
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
          shop_name: '',
          shop_no: '',
        };
        this.getData();
      },
      handelViewJump(shopId) {
        this.$router.push({
          path: `/shop-detail/${shopId}`,
        });
      },
      auditQuota(row) {
        this.dialogForm.shop_name = row.shop_name;
        this.dialogForm.shop_no = row.shop_no;
        this.dialogForm.id = row.id;
        this.dialogForm.total_quota = row.total_quota;
        this.dialogForm.daily_limit_quota = row.daily_limit_quota;
        this.dialogForm.single_limit_quota = row.single_limit_quota;
        this.dialogForm.apply_total = this.dialogForm.allow_total = row.apply_total;
        this.dialogForm.apply_daily_limit = this.dialogForm.allow_daily_limit = row.apply_daily_limit;
        this.dialogForm.apply_single_limit = row.apply_single_limit;
        this.dialogForm.allow_single_limit = row.apply_single_limit;
        this.dialogFormVisible = true;
      },
      handelSubmit() {
        const params = {
          allow_total: this.dialogForm.allow_total,
          state_total: this.dialogForm.state_total,
          allow_daily_limit: this.dialogForm.allow_daily_limit,
          state_daily_limit: this.dialogForm.state_daily_limit,
          allow_single_limit: this.dialogForm.allow_single_limit,
          state_single_limit: this.dialogForm.state_single_limit,
          memo: this.dialogForm.memo,
        };
        this.$http.put(`${apiBase}shop-quota-audit/${this.dialogForm.id}`, params)
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
            return {};
          }).catch(reportErrorMessage(this));
      },
    },
    filters: {
      filterState(v) {
        switch (v) {
          case 0 :
            return '待审核';
          case 1 :
            return '已审核';
          default:
            return '';
        }
      },
    },
  };
</script>