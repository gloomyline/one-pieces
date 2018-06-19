<template>
	<div>
        <el-form :model="menuForm" ref="menuForm" label-width="120px"> 
          <el-form-item label="模块名称" prop="module" required>
            <el-input auto-complete="off" v-model="form.module" placeholder="请输入模块名称" style="width:300px;"></el-input>
          </el-form-item>
          <el-form-item label="名称" prop="title" required>
            <el-input auto-complete="off" v-model="form.title" placeholder="请输入名称" style="width:300px;"></el-input>
          </el-form-item>
          <el-form-item label="父级菜单" prop="parent_id" required>
            <el-select v-model="form.parent_id" placeholder="模块" clearable>
              <el-option v-for="(item,index) in api.parents" :label="item.title" :value="item.id"></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="路由" prop="route" required>
            <el-input auto-complete="off" v-model="form.route" placeholder="请输入路由" style="width:300px;"></el-input>
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
  name: 'menuForm',
  data() {
    return {
      id: this.$route.params.id,
      api: {
        parents: [{ id: '0', title: '顶级菜单' }],
      },
      form: {
        title: '',
        parent_id: '',
        route: '',
        module: '',
      },
    };
  },
  mounted() {
    this.$http.get(`${apiBase}menus?is_top=1&customize=1`)
        .then(getJsonAndCheckSuccess)
        .then((response) => {
          if (response.results.length > 0) {
            const list = response.results;
            list.forEach(item => this.api.parents.push(item));
          }
        }).catch(reportErrorMessage(this));
    if (this.id) {
      this.$http.get(`${apiBase}menu-detail/${this.id}`)
          .then(getJsonAndCheckSuccess)
          .then((json) => {
            this.form = json.results;
            this.form.parent_id = json.results.parent_id;
          }).catch(reportErrorMessage(this));
    }
  },
  methods: {
    handleSubmit() {
      if (this.id) {
        this.$http.put(`${apiBase}update-menu/${this.id}`, this.form)
          .then(getJsonAndCheckSuccess)
          .then(() => {
            this.$message({
              type: 'success',
              message: '修改成功！',
            });
            this.$router.push({ path: '/menus' });
          }).catch(reportErrorMessage(this));
      } else {
        this.$http.post(`${apiBase}add-menu`, this.form)
          .then(getJsonAndCheckSuccess)
          .then(() => {
            this.$message({
              type: 'success',
              message: '新增成功！',
            });
            this.$router.push({ path: '/menus' });
          }).catch(reportErrorMessage(this));
      }
    },
  },
};
</script>