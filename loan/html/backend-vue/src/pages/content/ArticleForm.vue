<template>
	<div>
        <el-form :model="form" ref="articleForm" label-width="120px">
          <el-form-item label="标题" prop="title" :rules="[{ required: true, message: '请输入文章标题', trigger: 'blur'}, { min: 0, max: 30, message: '长度不超过30个字符', trigger: 'blur' }]">
            <el-input auto-complete="off" v-model="form.title" :maxlength="30" placeholder="请输入文章的标题" style="width:300px;"></el-input>
          </el-form-item>
          <el-form-item label="文章分类" prop="type" :rules="[{ required: true, message: '请选择文章分类' }]">
            <el-select v-model="form.type" placeholder="请选择" clearable>
              <el-option
                      v-for="item in types"
                      :label="item.name"
                      :value="item.id">
              </el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="添加图片" required>
            <el-upload
                    class="image-uploader"
                    ref="image"
                    :action="url_action"
                    :show-file-list="false"
                    :withCredentials="true"
                    :on-success="handleAvatarSuccess"
                    :before-upload="beforeAvatarUpload">
              <img v-if="form.image" :src="form.image" class="image-article">
              <i v-else class="el-icon-plus image-uploader-icon"></i>
              <div slot="tip" class="el-upload__tip">只能上传jpg/png文件，且不超过2MB</div>
            </el-upload>
          </el-form-item>
          <el-form-item label="显示" required>
            <el-radio v-model="form.state" label="1">是</el-radio>
            <el-radio v-model="form.state" label="2">否</el-radio>
          </el-form-item>
          <el-form-item label="排序" prop="sort" required>
            <el-input-number  v-model="form.sort" placeholder="" style="width:200px;" :min="0" :max="100"></el-input-number>
          </el-form-item>
          <el-form-item label="文章内容" required>
            <div style="line-height: 0">
              <Ueditor @ready="editorReady"></Ueditor>
            </div>
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
import Ueditor from '../../components/Ueditor';

export default {
  name: 'articleForm',
  components: { Ueditor },
  data() {
    return {
      url_action: `${apiBase}upload`,
      fileList: [],
      id: this.$route.params.id,
      api: {
        roles: [],
      },
      form: {
        title: '',
        type: '',
        image: '',
        state: '1',
        sort: 1,
        content: '',
      },
      imageUrl: '',
      types: [{ id: 'activity', name: '活动中心' }, { id: 'problem', name: '常见问题' }],
    };
  },
  mounted() {
    if (this.id) {
      this.$http.get(`${apiBase}get-article/${this.id}`)
          .then(getJsonAndCheckSuccess)
          .then((json) => {
            this.form = json.results;
          }).catch(reportErrorMessage(this));
    }
  },
  methods: {
    handleSubmit() {
      const params = {
        title: this.form.title,
        type: this.form.type,
        state: this.form.state,
        sort: this.form.sort,
        content: this.form.content,
      };
      if (this.imageUrl) {
        params.image = this.imageUrl;
      }
      if (this.id) {
        this.$http.put(`${apiBase}article-update/${this.id}`, params)
          .then(getJsonAndCheckSuccess)
          .then(() => {
            this.$message({
              type: 'success',
              message: '修改成功！',
            });
            this.$router.push({ path: '/article' });
          }).catch(reportErrorMessage(this));
      } else {
        this.$http.post(`${apiBase}article-add`, params)
          .then(getJsonAndCheckSuccess)
          .then(() => {
            this.$message({
              type: 'success',
              message: '文章添加成功！',
            });
            this.$router.push({ path: '/article' });
          }).catch(reportErrorMessage(this));
      }
    },
    handleAvatarSuccess(res, file) {
      this.form.image = URL.createObjectURL(file.raw);
      this.imageUrl = res.url;
    },
    beforeAvatarUpload(file) {
      const isJPG = file.type === 'image/jpeg';
      const isPNG = file.type === 'image/png';
      const isLt2M = file.size / 1024 / 1024 < 2;
      if (!(isJPG || isPNG)) {
        this.$message.error('上传图片只能是 JPG/PNG 格式!');
      }
      if (!isLt2M) {
        this.$message.error('上传图片大小不能超过 2MB!');
      }
      return (isJPG || isPNG) && isLt2M;
    },
    editorReady(instance) {
      setTimeout(() => {
        instance.setContent(this.form.content);
      }, 1000);
      instance.addListener('contentChange', () => {
        this.form.content = instance.getContent();
      });
    },
  },
};
</script>
<style>
  .image-uploader .el-upload {
    border: 1px dashed #d9d9d9;
    border-radius: 6px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
  }
  .image-uploader .el-upload:hover {
    border-color: #20a0ff;
  }
  .image-uploader-icon {
    font-size: 28px;
    color: #8c939d;
    width: 178px;
    height: 178px;
    line-height: 178px;
    text-align: center;
  }
  .image-article {
    width: 178px;
    height: 178px;
    display: block;
  }
  .info-test{
    border-radius: 10px;
    line-height: 20px;
    padding: 10px;
    margin: 10px;
    background-color: #ffffff;
  }
</style>