<template>
    <div>
        <el-form :model="form" :rules="rules" ref="form" label-width="120px">
            <el-form-item label="原密码" prop="old_password" :rules="[{ required: true, message: '原密码不能为空' }]">
                <el-input type="password" auto-complete="off" v-model="form.old_password" placeholder="请输入原密码" style="width:200px;"></el-input>
            </el-form-item>
            <el-form-item label="新密码" prop="new_password" :rules="[{ required: true, message: '新密码不能为空' }]">
                <el-input type="password" auto-complete="off" v-model="form.new_password" placeholder="请输入新密码" style="width:200px;"></el-input>
            </el-form-item>
            <el-form-item label="确认密码" prop="repeat_password" :rules="[{ required: true, message: '确认密码不能为空' }]">
                <el-input type="password" auto-complete="off" v-model="form.repeat_password" placeholder="请输入确认密码" style="width:200px;"></el-input>
            </el-form-item>

            <el-form-item>
                <el-button type="primary" @click.native="handleSubmit">保存</el-button>
            </el-form-item>
        </el-form>
    </div>
</template>
<script>
  import apiBase from '../../apiBase';
  import { getJsonAndCheckSuccess, reportErrorMessage } from '../../ApiUtil';

  export default {
    name: 'pwdForm',
    data() {
      return {
        form: {
          old_password: '',
          new_password: '',
          repeat_password: '',
        },
      };
    },
    mounted() {
    },
    methods: {
      handleSubmit() {
        const params = {
          old_password: this.form.old_password,
          new_password: this.form.new_password,
          repeat_password: this.form.repeat_password,
        };
        this.$http.put(`${apiBase}pwd`, params)
          .then(getJsonAndCheckSuccess)
          .then(() => {
            this.$message({
              type: 'success',
              message: '保存成功！',
            });
            this.clearForm();
            // this.$router.push({ path: '/index' });
            window.location.href = '/site/login';
          }).catch(reportErrorMessage(this));
      },
      clearForm() {
        this.form = {
          old_password: '',
          new_password: '',
          repeat_password: '',
        };
      },
    },
  };
</script>