<template>
    <div>
        <el-form :model="form" ref="authForm" label-width="120px">
          <el-form-item label="权限名称" prop="name" :rules="[{ required: true, message: '权限名不能为空'}]">
            <el-input auto-complete="off" v-model="form.name" placeholder="请输入权限名称" style="width:300px;"></el-input>
          </el-form-item>
          <el-form-item label="描述" prop="description" :rules="[{ required: true, message: '描述不能为空'}]">
            <el-input auto-complete="off" v-model="form.description" placeholder="请输入描述" style="width:300px;"></el-input>
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
  name: 'authForm',
  data() {
    return {
      form: {
        name: '',
        description: '',
      },
    };
  },
  methods: {
    handleSubmit() {
      this.$http.post(`${apiBase}add-auth`, this.form)
        .then(getJsonAndCheckSuccess)
        .then(() => {
          this.$message({
            type: 'success',
            message: '新增成功！',
          });
          this.$router.push({ path: '/auths' });
        }).catch(reportErrorMessage(this));
    },
  },
};
</script>