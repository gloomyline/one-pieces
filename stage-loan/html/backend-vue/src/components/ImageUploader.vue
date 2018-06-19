<template>
  <span>
    <span v-if="value">
      <img :src="value + '@!200'">
      <el-button icon="close" @click="handleDelete">删除</el-button>
    </span>
    <span v-else>
      <input id="foo" type="file" @change="handleChange"></input>
      <el-button icon="upload" :disabled="file === null" @click="handleUpload">上传</el-button>
    </span>
  </span>
</template>

<script>
import apiBase from '../apiBase';

export default {
  props: {
    value: String,
  },
  data() {
    return {
      file: null,
    };
  },
  methods: {
    handleDelete() {
      this.$emit('input', '');
    },
    handleUpload() {
      const fd = new FormData();
      fd.append('file', this.file);

      this.$http.post(`${apiBase}brands/upload`, fd)
        .then(response => response.json())
        .then((json) => {
          if (json.status !== 'SUCCESS') {
            return Promise.reject(json.error_message);
          }
          this.file = null;
          this.$emit('input', json.results[0].url);
          return {};
        }).catch((e) => {
          this.$message({
            showClose: true,
            message: e,
            type: 'error',
            duration: 0,
          });
        });
    },
    handleChange(e) {
      this.file = e.target.files[0];
    },
  },
};
</script>

<style>
</style>