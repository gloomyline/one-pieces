<template>
    <div>
        <el-row>
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
            <el-table-column property="total_quota" label="总额度（元）" align="center" min-width="100"></el-table-column>
            <el-table-column property="available_quota" label="可用额度（元）" align="center" min-width="100"></el-table-column>
            <!--<el-table-column property="fronzen_quota" label="冻结额度（元）" align="center" min-width="100"></el-table-column>-->
            <el-table-column property="promoted_quota" label="已提升额度（元）" align="center" min-width="100">
                <template slot-scope="scope">
                    <span v-text="scope.row.total_quota > 0 ? scope.row.total_quota - scope.row.init_total_quota : 0"></span>
                </template>
            </el-table-column>
            <el-table-column property="single_limit_quota" label="单笔限额（元）" align="center" min-width="100"></el-table-column>
            <el-table-column property="daily_limit_quota" label="单日限额（元）" align="center" min-width="100"></el-table-column>
            <el-table-column label="操作" align="center" min-width="180">
                <template slot-scope="scope">
                    <el-button-group>
                        <el-button type="primary" size="small"  @click.native="promoteQuota(scope.row)">添加额度</el-button>
                        <el-button type="primary" size="small" @click.native="handelViewJump(scope.row.id)">查 看</el-button>
                    </el-button-group>
                </template>
            </el-table-column>
        </el-table>
        <page :total="pageCount" :current-page="currentPage" :page-size="pageSize" @current-change="handleCurrentChange"></page>

        <div>
            <el-dialog :visible.sync="dialogFormVisible" :title='dialogForm.title'>
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
                    <el-form-item label="操作类型"  required>
                        <el-radio-group v-model="dialogForm.act1">
                            <el-radio :label="1">增加</el-radio>
                            <el-radio :label="0">减少</el-radio>
                        </el-radio-group>
                    </el-form-item>
                    <el-form-item label="额度金额" required>
                        <el-input-number v-model="dialogForm.apply_quota1" controls-position="right"  :min="0" :max="900000" :step="500"></el-input-number>
                    </el-form-item>
                    <h4 style="color: #003eff; padding-top:10px; border-top: solid 1px #000000">单日限额</h4>
                    <el-form-item label="操作类型"  required>
                        <el-radio-group v-model="dialogForm.act2">
                            <el-radio :label="1">增加</el-radio>
                            <el-radio :label="0">减少</el-radio>
                        </el-radio-group>
                    </el-form-item>
                    <el-form-item label="额度金额" required>
                        <el-input-number v-model="dialogForm.apply_quota2" controls-position="right"  :min="0" :max="900000" :step="500"></el-input-number>
                    </el-form-item>
                    <h4 style="color: #003eff; padding-top:10px; border-top: solid 1px #000000">单笔限额</h4>
                    <el-form-item label="操作类型"  required>
                        <el-radio-group v-model="dialogForm.act3">
                            <el-radio :label="1">增加</el-radio>
                            <el-radio :label="0">减少</el-radio>
                        </el-radio-group>
                    </el-form-item>
                    <el-form-item label="额度金额" required>
                        <el-input-number v-model="dialogForm.apply_quota3" controls-position="right"  :min="0" :max="900000" :step="500"></el-input-number>
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
          shop_no: '',
          shop_name: '',
        },
        dialogForm: {
          title: '添加商户额度',
          shop_name: '',
          shop_no: '',
          total_quota: '',
          single_limit_quota: '',
          daily_limit_quota: '',
          id: '',
          act1: 1,
          apply_quota1: 0,
          act2: 1,
          apply_quota2: 0,
          act3: 1,
          apply_quota3: 0,
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
        this.$http.get(`${apiBase}shop-quota-list`, { params })
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
      promoteQuota(row) {
        this.clearDialogForm();
        this.dialogFormVisible = true;
        this.dialogForm.shop_name = row.shop_name;
        this.dialogForm.shop_no = row.shop_no;
        this.dialogForm.total_quota = row.total_quota;
        this.dialogForm.daily_limit_quota = row.daily_limit_quota;
        this.dialogForm.single_limit_quota = row.single_limit_quota;
        this.dialogForm.id = row.id;
      },
      clearDialogForm() {
        this.dialogForm = {
          title: '添加商户额度',
          shop_name: '',
          shop_no: '',
          total_quota: '',
          single_limit_quota: '',
          daily_limit_quota: '',
          id: '',
          act1: 1,
          apply_quota1: 0,
          act2: 1,
          apply_quota2: 0,
          act3: 1,
          apply_quota3: 0,
        };
      },
      handelSubmit() {
        const params = {};
        if (this.dialogForm.act1 === 1) {
          params.apply_total = this.dialogForm.apply_quota1;
        } else if (this.dialogForm.act1 === 0) {
          params.apply_total = -Number(this.dialogForm.apply_quota1);
        }
        if (this.dialogForm.act2 === 1) {
          params.apply_daily_limit = this.dialogForm.apply_quota2;
        } else if (this.dialogForm.act2 === 0) {
          params.apply_daily_limit = -Number(this.dialogForm.apply_quota2);
        }
        if (this.dialogForm.act3 === 1) {
          params.apply_single_limit = this.dialogForm.apply_quota3;
        } else if (this.dialogForm.act3 === 0) {
          params.apply_single_limit = -Number(this.dialogForm.apply_quota3);
        }
        this.$http.post(`${apiBase}shop-quota-apply/${this.dialogForm.id}`, params)
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