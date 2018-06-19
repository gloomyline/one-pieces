<template>
	<div>
        <el-form :model="form" ref="adminForm" label-width="120px">
          <el-form-item label="登入名" prop="username" :rules="[{ required: true, message: '登入名不能为空' }]">
            <el-input auto-complete="off" v-model="form.username" placeholder="请输入登入名" style="width:200px;"></el-input>
          </el-form-item>
          <el-form-item label="密码" prop="password" v-if="id === undefined" :rules="[{ required: true, message: '密码不能为空'}]">
            <el-input auto-complete="off" v-model="form.password" placeholder="请输入密码" style="width:200px;"></el-input>
          </el-form-item>
          <el-form-item label="真实姓名" prop="real_name" :rules="{ required: true, message: '真实姓名不能为空' }">
            <el-input auto-complete="off" v-model="form.real_name" placeholder="请输入真实姓名" style="width:200px;"></el-input>
          </el-form-item>
          <el-form-item label="角色" prop="role" :rules="{ required: true, message: '角色不能为空' }">
            <el-select v-model="form.role" placeholder="角色" clearable>
              <el-option v-for="(item,index) in api.roles" :label="item.name" :value="item.name"></el-option>
            </el-select>
          </el-form-item>
          <el-form-item>
	        <el-button type="primary" @click.native="handleSubmit">保存</el-button>
	        <el-button type="primary" @click="$router.back()">取消</el-button>
	      </el-form-item>
        </el-form>
    </div> 
</template>
<script>
import apiBase from '../../apiBase';
import { getJsonAndCheckSuccess, reportErrorMessage } from '../../ApiUtil';

export default {
  name: 'adminForm',
  data() {
    return {
      id: this.$route.params.id,
      api: {
        roles: [],
      },
      form: {
        username: '',
        password: '',
        real_name: '',
        role: '',
      },
    };
  },
  mounted() {
    this.$http.get(`${apiBase}roles`)
        .then(getJsonAndCheckSuccess)
        .then((response) => {
          this.api.roles = response.results;
        }).catch(reportErrorMessage(this));
    if (this.id) {
      this.$http.get(`${apiBase}admin-detail/${this.id}`)
          .then(getJsonAndCheckSuccess)
          .then((json) => {
            this.form = json.results;
          }).catch(reportErrorMessage(this));
    }
  },
  methods: {
    handleSubmit() {
      const params = {
        username: this.form.username,
        real_name: this.form.real_name,
        role: this.form.role,
      };
      if (this.id) {
        this.$http.put(`${apiBase}update-admin/${this.id}`, params)
          .then(getJsonAndCheckSuccess)
          .then(() => {
            this.$message({
              type: 'success',
              message: '修改成功！',
            });
            this.$router.push({ path: '/admins' });
          }).catch(reportErrorMessage(this));
      } else {
        params.password = this.form.password;
        this.$http.post(`${apiBase}add-admin`, params)
          .then(getJsonAndCheckSuccess)
          .then(() => {
            this.$message({
              type: 'success',
              message: '新增成功！',
            });
            this.$router.push({ path: '/admins' });
          }).catch(reportErrorMessage(this));
      }
    },
  },
};
</script>