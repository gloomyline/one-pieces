<template>
    <div>
        <h5 style="margin-left:40px;">{{ name }}</h5>
        <el-form ref="menuForm" label-width="120px">
          <el-form-item label="菜单">
            <el-checkbox-group v-model="checkAuth">
              <el-checkbox
                :label="item.name" v-for="(item, index) in api.auths" 
                v-if="item.name.indexOf('backend_menus') === 0" :checked="item.isChecked" @change="handleToggle(item.name)">
                {{ item.description }}(菜单)
              </el-checkbox>
            </el-checkbox-group>
          </el-form-item>
          
          <el-form-item label="权限">
            <el-checkbox-group v-model="checkAuth">
              <el-checkbox
                :label="item.name" v-for="(item, index) in api.auths" 
                v-if="item.name.indexOf('backend_menus') !== 0" :checked="item.isChecked" @change="handleToggle(item.name)">
                {{ item.description }}
              </el-checkbox>
            </el-checkbox-group>
          </el-form-item>

          <el-form-item>
            <el-button type="primary" @click="$router.back()">返回</el-button>
          </el-form-item>
        </el-form>
    </div> 
</template>
<script>
import apiBase from '../../apiBase';
import { getJsonAndCheckSuccess, reportErrorMessage } from '../../ApiUtil';

export default {
  name: 'assignForm',
  data() {
    return {
      name: this.$route.params.name,
      api: {
        auths: [],
      },
      checkAuth: [],
    };
  },
  mounted() {
    this.$http.get(`${apiBase}role-auths?name=${this.name}`)
        .then(getJsonAndCheckSuccess)
        .then((response) => {
          if (response.results.length > 0) {
            const list = response.results;
            list.forEach(item => this.api.auths.push(item));
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
    handleToggle(name) {
      const params = {
        parent: this.name,
        child: name,
      };
      const index = this.checkAuth.findIndex(v => v === name);
      if (index === -1) {
        params.enable = 2;
      } else {
        params.enable = 1;
      }
      this.$http.post(`${apiBase}assign-auth`, params)
        .then(response => response.json())
        .then((json) => {
          if (json.status !== 'SUCCESS') {
            return Promise.reject(json.error_message);
          }
          this.$message({
            type: 'success',
            message: json.error_message,
          });
          return {};
        }).catch(reportErrorMessage(this));
    },
  },
};
</script>