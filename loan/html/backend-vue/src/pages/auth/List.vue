<template>
  <div>
    <el-row>
      <el-col :span="24">
        <el-form :inline="true">
          <el-form-item>
            <el-input v-model="form.name" placeholder="权限名称"></el-input>
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
      <el-table-column property="name" label="权限名称" align="center"></el-table-column>
      <el-table-column property="description" label="说明描述" align="center" width="200"></el-table-column>
      <el-table-column property="created_at" label="创建时间" align="center" width="170" :formatter="formatter"></el-table-column>
      <el-table-column property="updated_at" label="修改时间" align="center" width="170" :formatter="formatter"></el-table-column>
      <el-table-column label="操作" align="center" width="150">
        <template slot-scope="scope">
          <el-button-group>
            <el-button type="primary" size="small" @click.native="showDialog(scope.row.name, scope.row.description)">编辑</el-button>
            <el-button type="primary" size="small" @click.native="handleDel(scope.row.name)">删除</el-button>
          </el-button-group>
        </template>
      </el-table-column>
    </el-table>
    <page :total="pageCount" :current-page="currentPage" :page-size="pageSize" @current-change="handleCurrentChange"></page>

    <div>
      <el-dialog :visible.sync="dialogFormVisible">
        <el-form :model="dialogForm" ref="dialogForm" label-width="120px">
          
          <el-form-item label="权限名称" prop="name" :rules="[{ required: true, message: '权限名不能为空'}]">
            <el-input auto-complete="off" v-model="dialogForm.name" placeholder="请输入权限名称" style="width:300px;"></el-input>
          </el-form-item>
          <el-form-item label="描述" prop="description" :rules="[{ required: true, message: '描述不能为空'}]">
            <el-input auto-complete="off" v-model="dialogForm.description" placeholder="请输入描述" style="width:300px;"></el-input>
          </el-form-item>

        </el-form>
        <span slot="footer" class="dialog-footer">
          <el-button @click.native="dialogFormVisible = false">取 消</el-button>
          <el-button type="primary" @click.native.prevent="handleSubmit">确 定</el-button>
        </span>
      </el-dialog>
    </div>

  </div>
</template>
<script>
import moment from 'moment';
import apiBase from '../../apiBase';
import Page from '../../components/Page';
import { reportErrorMessage } from '../../ApiUtil';

export default {
  name: 'auth',
  data() {
    return {
      form: {
        name: '',
      },
      dialogForm: {
        name: '',
        description: '',
      },
      old_name: '',
      tableData: [],
      pageCount: 0,
      currentPage: 1,
      pageSize: 20,
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
      if (this.form.name !== '') {
        params.name = this.form.name;
      }
      this.$http.get(`${apiBase}auths`, { params })
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
        name: '',
      };
      this.getData();
    },
    formatter(row) {
      return moment.unix(row.created_at).format('YYYY-MM-DD HH:mm:ss');
    },
    handleAdd() {
      this.$router.push({
        path: '/add-auth',
      });
    },
    handleDel(name) {
      this.$confirm('确定删除?', '提示', {
        type: 'warning',
      }).then(() => {
        this.$http.post(`${apiBase}delete-auth`, { itemname: name })
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
    showDialog(name, description) {
      this.old_name = name;
      this.dialogForm.name = name;
      this.dialogForm.description = description;
      this.dialogFormVisible = true;
    },
    handleSubmit() {
      const params = {
        old_name: this.old_name,
        name: this.dialogForm.name,
        description: this.dialogForm.description,
      };
      this.$http.put(`${apiBase}update-auth`, params)
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