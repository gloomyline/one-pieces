<template>
	<div>
        <el-form :model="form" ref="roleForm" label-width="120px">
          <el-form-item label="名称" prop="name" :rules="[{ required: true, message: '权限名不能为空'}]">
            <el-input auto-complete="off" v-model="form.name" placeholder="请输入名称" style="width:200px;"></el-input>
          </el-form-item>
          <el-form-item label="描述" prop="description" :rules="[{ required: true, message: '描述不能为空'}]">
            <el-input auto-complete="off" v-model="form.description" placeholder="请输入描述" style="width:500px;"></el-input>
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
  name: 'roleForm',
  data() {
    return {
      role: this.$route.params.name,
      form: {
        name: '',
        description: '',
      },
    };
  },
  mounted() {
    if (this.role) {
      this.$http.get(`${apiBase}role-detail/${this.role}`)
          .then(getJsonAndCheckSuccess)
          .then((json) => {
            this.form = json.results;
          }).catch(reportErrorMessage(this));
    }
  },
  methods: {
    handleSubmit() {
      const params = {
        name: this.form.name,
        description: this.form.description,
      };
      if (this.role) {
        this.$http.put(`${apiBase}update-role/${this.role}`, params)
          .then(getJsonAndCheckSuccess)
          .then(() => {
            this.$message({
              type: 'success',
              message: '修改成功！',
            });
            this.$router.push({ path: '/roles' });
          }).catch(reportErrorMessage(this));
      } else {
        this.$http.post(`${apiBase}add-role`, params)
          .then(getJsonAndCheckSuccess)
          .then(() => {
            this.$message({
              type: 'success',
              message: '新增成功！',
            });
            this.$router.push({ path: '/roles' });
          }).catch(reportErrorMessage(this));
      }
    },
  },
};
</script>