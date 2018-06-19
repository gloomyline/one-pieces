<template>
    <div>
        <el-row>
            <el-col :span="24">
                <el-form :inline="true">
                    <el-form-item>
                        <el-input v-model="form.shop_no" placeholder="商户号"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-input v-model="form.shop_name" placeholder="商户名称"></el-input>
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
                        <el-button type="success" @click.native="handleJumpApply">商户申请</el-button>
                    </el-form-item>
                </el-form>
            </el-col>
        </el-row>
        <el-table :data="tableData" stripe border style="width: 100%;" :cell-class-name="stateCellClass">
            <el-table-column property="id" label="ID" align="center" width="80"></el-table-column>
            <el-table-column property="shop_no" label="商户号" align="center" width="120"></el-table-column>
            <el-table-column property="shop_name" label="商户名称" align="center" width="180"></el-table-column>
            <el-table-column property="created_at" label="申请时间" align="center" width="180"></el-table-column>
            <el-table-column property="salesman" label="业务经理" align="center" width="120"></el-table-column>
            <el-table-column property="auditor" label="审核人" align="center" width="120"></el-table-column>
            <el-table-column property="state" label="审核状态" align="center" width="120" :formatter="stateFormatter"></el-table-column>
            <el-table-column property="audit_updated_at" label="审核状态更新时间" align="center" width="180"></el-table-column>
            <el-table-column property="total_quota" label="总授信额度(万)" align="center" width="130"></el-table-column>
            <el-table-column property="daily_limit_quota" label="单日限额(万)" align="center" width="130" ></el-table-column>
            <el-table-column property="single_limit_quota" label="单笔限额(万)" align="center" width="130" ></el-table-column>
            <el-table-column property="category" label="商品类别" align="center" width="180">
                <template slot-scope="scope">
                    <span v-for="item in scope.row.category" v-text="item + ','"></span>
                </template>
            </el-table-column>
            <el-table-column label="操作" align="center" min-width="220" fixed="right">
                <template slot-scope="scope">
                    <el-button-group style="float:left;margin-left:16px;">
                        <el-button type="primary" size="small"  @click.native="handleUpdate(scope.row.id)">编辑</el-button>
                        <!--<el-button type="danger" size="small" @click.native="handleAudit(scope.row.id)" :disabled="scope.row.state !== 0" >审核</el-button>-->
                        <el-button type="danger" size="small" @click.native="handleAudit(scope.row.id)" v-if="scope.row.state === 0" >审&nbsp;&nbsp;&nbsp;&nbsp;核</el-button>
                        <el-button type="warning" size="small" @click.native="handleAdmin(scope.row)" v-if="scope.row.state === 1">管理员</el-button>
                        <el-button type="success" size="small" @click.native="handleDetail(scope.row.id)" >查看</el-button>
                    </el-button-group>
                </template>
            </el-table-column>
        </el-table>
        <page :total="pageCount" :current-page="currentPage" :page-size="pageSize" @current-change="handleCurrentChange"></page>

        <!--添加管理员-->
        <div>
            <el-dialog :visible.sync="dialogAddShopAdminFormVisible" title="添加商户管理员">
                <el-form :model="addForm" ref="addForm" label-width="120px">
                    <el-form-item label="商户名称">
                        {{ addForm.shop_name }}
                    </el-form-item>
                    <el-form-item label="登入名" prop="username" :rules="[{ required: true, message: '登入名不能为空' }]" >
                        <el-input auto-complete="off" v-model="addForm.username" placeholder="请输入登入名" style="width:200px;"></el-input>
                    </el-form-item>
                    <el-form-item label="密码" prop="password" :rules="[{ required: true, message: '密码不能为空' }]">
                        <el-input auto-complete="off" type="password" v-model="addForm.password" placeholder="请输入密码" style="width:200px;"></el-input>
                    </el-form-item>
                </el-form>
                <div slot="footer" class="dialog-footer">
                    <el-button @click="dialogAddShopAdminFormVisible = false">取 消</el-button>
                    <el-button type="primary" @click="handleSubmitAdd">提 交</el-button>
                </div>
            </el-dialog>
        </div>
        <!--编辑管理员修改密码-->
        <div>
            <el-dialog :visible.sync="dialogUpdateShopAdminFormVisible" title="修改密码">
                <el-form :model="updateForm" ref="addForm" label-width="120px">
                    <el-form-item label="商户名称">
                        {{ updateForm.shop_name }}
                    </el-form-item>
                    <el-form-item label="登入名">
                        {{ updateForm.username }}
                    </el-form-item>
                    <el-form-item label="新密码" prop="password" :rules="[{ required: true, message: '密码不能为空' }]">
                        <el-input auto-complete="off" type="password" v-model="updateForm.password" placeholder="请输入密码" style="width:200px;"></el-input>
                    </el-form-item>
                </el-form>
                <div slot="footer" class="dialog-footer">
                    <el-button @click="dialogUpdateShopAdminFormVisible = false">取 消</el-button>
                    <el-button type="primary" @click="handleSubmitUpdate">提 交</el-button>
                </div>
            </el-dialog>
        </div>
    </div>
</template>
<script>
  import apiBase from '../../apiBase';
  import Page from '../../components/Page';
  import { getJsonAndCheckSuccess, reportErrorMessage } from '../../ApiUtil';

  export default {
    name: 'shopList',
    data() {
      return {
        tableData: [],
        form: {
          shop_no: '',
          state: '',
          shop_name: '',
          value9: '',
        },
        addForm: {
          username: '',
          password: '',
          shop_name: '',
          shop_id: '',
        },
        updateForm: {
          password: '',
          username: '',
          shop_name: '',
          admin_id: '',
        },
        shopAdmin: [],
        pageCount: 0,
        currentPage: 1,
        pageSize: 20,
        msg: '',
        dialogAddShopAdminFormVisible: false,
        dialogUpdateShopAdminFormVisible: false,
        states: [{ id: 0, name: '待审核' }, { id: 1, name: '通过' }, { id: 2, name: '未通过' }],
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
        if (this.form.shop_no !== '') {
          params.shop_no = this.form.shop_no;
        }
        if (this.form.shop_name !== '') {
          params.shop_name = this.form.shop_name;
        }
        if (this.form.state !== '') {
          params.state = this.form.state;
        }
        this.$http.get(`${apiBase}shop-list`, { params })
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
          shop_no: '',
          shop_name: '',
          state: '',
        };
        this.getData();
      },
      stateFormatter(row) {
        return this.states.find(v => v.id === row.state).name;
      },
      handleJumpApply() {
        this.$router.push({
          path: '/shop-apply',
        });
      },
      handleUpdate(shopId) {
        this.$router.push({
          path: `/shop-update/${shopId}`,
        });
      },
      handleAudit(shopId) {
        this.$router.push({
          path: `/shop-audit/${shopId}`,
        });
      },
      handleDetail(shopId) {
        this.$router.push({
          path: `/shop-detail/${shopId}`,
        });
      },
      handleAdmin(row) {
        this.$http.get(`${apiBase}shop-admin/${row.id}`)
          .then(getJsonAndCheckSuccess)
          .then((json) => {
            this.shopAdmin = json.results;
            if (this.shopAdmin.length !== 0) { // 返回结果不为空 进入修改密码页面
              this.dialogUpdateShopAdminFormVisible = true;
              this.updateForm.username = this.shopAdmin.username;
              this.updateForm.shop_name = row.shop_name;
              this.updateForm.admin_id = this.shopAdmin.id;
            } else { // 返回结果为空 进入添加管理员页面
              this.dialogAddShopAdminFormVisible = true;
              this.addForm.shop_name = row.shop_name;
              this.addForm.shop_id = row.id;
            }
          }).catch(reportErrorMessage(this));
      },
      handleSubmitUpdate() { // 修改密码
        const params = {
          admin_id: this.updateForm.admin_id,
          password: this.updateForm.password,
        };
        this.$http.put(`${apiBase}shop-admin-pwd`, params)
          .then(response => response.json())
          .then((json) => {
            if (json.status !== 'SUCCESS') {
              return Promise.reject(json.error_message);
            }
            this.$message({
              type: 'success',
              message: '密码修改成功！',
            });
            // this.getData();
            this.dialogUpdateShopAdminFormVisible = false;
            return {};
          }).catch(reportErrorMessage(this));
      },
      handleSubmitAdd() { // 添加管理员
        const params = {
          username: this.addForm.username,
          password: this.addForm.password,
          shop_id: this.addForm.shop_id,
        };
        this.$http.post(`${apiBase}shop-admin-add`, params)
          .then(response => response.json())
          .then((json) => {
            if (json.status !== 'SUCCESS') {
              return Promise.reject(json.error_message);
            }
            this.$message({
              type: 'success',
              message: '添加管理员成功！',
            });
            // this.getData();
            this.dialogAddShopAdminFormVisible = false;
            return {};
          }).catch(reportErrorMessage(this));
      },
      stateCellClass({ row, column }) {
        if (column.property === 'state') {
          if (row.state === 2) {
            return 'failureClass';
          } else if (row.state === 1) {
            return 'successClass';
          }
          return 'defaultClass';
        }
        return '';
      },
    },
  };
</script>