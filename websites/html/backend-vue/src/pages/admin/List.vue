<template>
  <div>
    <el-row>
      <el-col :span="24">
        <el-form :inline="true">
          <el-form-item>
            <el-input v-model="form.username" placeholder="登入名"></el-input>
          </el-form-item>
          <el-form-item>
            <el-input v-model="form.real_name" placeholder="真实姓名"></el-input>
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
            <el-button type="primary" @click.native="handleAdd()">添加</el-button>
          </el-form-item>
        </el-form>
      </el-col>
    </el-row>
    <el-table :data="tableData" stripe border style="width: 100%;">
      <el-table-column property="id" label="ID" align="center" width="70"></el-table-column>
      <el-table-column property="username" label="登入名" align="center" width="120"></el-table-column>
      <el-table-column property="real_name" label="真实姓名" align="center" width="120"></el-table-column>
      <el-table-column property="role" label="角色" align="center" width="120"></el-table-column>
      <el-table-column property="state" label="状态" align="center" width="100" :formatter="stateFormatter"></el-table-column>
      <el-table-column property="login_time" label="登入时间" align="center" width="170"></el-table-column>
      <el-table-column label="操作" align="center" min-width="300">
        <template slot-scope="scope">
          <el-button-group style="float:left;margin-left:16px;">
            <el-button type="primary" size="small" @click.native="handleJump(scope.row.id)">编辑</el-button>
            <el-button type="primary" size="small" @click.native="showDialog(scope.row.id)">修改密码</el-button>
            <el-button type="primary" size="small" @click.native="setLeave(scope.row.id)" v-if="scope.row.state === 1">设置为离职</el-button>
          </el-button-group>
        </template>
      </el-table-column>
    </el-table>
    <page :total="pageCount" :current-page="currentPage" :page-size="pageSize" @current-change="handleCurrentChange"></page>
	 
    <div>
      <el-dialog :visible.sync="dialogFormVisible">
        <el-form :model="dialogForm" ref="dialogForm" label-width="120px">
          
          <el-form-item label="新密码" prop="password" required>
            <el-input auto-complete="off" v-model="dialogForm.password" placeholder="请输入新密码" style="width:300px;"></el-input>
          </el-form-item>
          <p style="margin-left:50px; line-height: 25px;" v-html="msg"></p>

        </el-form>
        <span slot="footer" class="dialog-footer">
          <el-button @click.native="dialogFormVisible = false">取 消</el-button>
          <el-button type="primary" @click.native.prevent="setPassword">确 定</el-button>
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
  name: 'admins',
  data() {
    return {
      tableData: [],
      api: {
        roles: [],
      },
      form: {
        username: '',
        real_name: '',
        state: '',
      },
      dialogForm: {
        id: '',
        password: '',
      },
      msg: '',
      pageCount: 0,
      currentPage: 1,
      pageSize: 20,
      dialogFormVisible: false,
      states: [{ id: 1, name: '在职' }, { id: 2, name: '离职' }],
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

    this.$http.get(`${apiBase}roles`)
        .then(getJsonAndCheckSuccess)
        .then((response) => {
          this.api.roles = response.results;
        }).catch(reportErrorMessage(this));
  },
  methods: {
    getData() {
      const params = {
        limit: this.pageSize,
        offset: this.pageSize * (this.currentPage - 1),
      };
      if (this.form.username !== '') {
        params.username = this.form.username;
      }
      if (this.form.real_name !== '') {
        params.real_name = this.form.real_name;
      }
      if (this.form.state !== '') {
        params.state = this.form.state;
      }
      this.$http.get(`${apiBase}admins`, { params })
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
        username: '',
        real_name: '',
      };
      this.getData();
    },
    stateFormatter(row) {
      return row.state === 1 ? '在职' : '离职';
    },
    handleAdd() {
      this.$router.push({
        path: '/add-admin',
      });
    },
    handleJump(id) {
      this.$router.push({
        path: `/update-admin/${id}`,
      });
    },
    setLeave(id) {
      this.$confirm('确定设置为离职?', '提示', {
        type: 'warning',
      }).then(() => {
        this.$http.put(`${apiBase}set-admin-leave`, { admin_id: id })
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
    showDialog(id) {
      this.dialogForm.id = id;
      this.dialogFormVisible = true;
    },
    setPassword() {
      const params = {
        id: this.dialogForm.id,
        password: this.dialogForm.password,
      };
      this.$http.put(`${apiBase}set-admin-password`, params)
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
          this.dialogFormVisible = false;
          return {};
        }).catch(reportErrorMessage(this));
    },
  },
};
</script>