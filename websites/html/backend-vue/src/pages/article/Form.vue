<template>
    <div class="article">
        <el-form :model="form" ref="form" label-width="140px" label-position="left">
            <el-form-item label="请选择分类" prop="nav_id" :rules="[{ required: true, message: '文章分类不能为空' }]">
                <el-select v-model="form.nav_id" placeholder="请选择" style="width: 300px" clearable>
                    <el-option v-for="(item,index) in navChildren" :label="item.name" :value="item.id"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="名称" prop="title" :rules="[{ required: true, message: '名称不能为空' }]">
                <el-input v-model="form.title" placeholder="请输入名称" :maxlength="50" style="width:300px;"></el-input>
            </el-form-item>
            <el-form-item label="作者" prop="author" :rules="[{ required: true, message: '文章作者不能为空' }]">
                <el-input v-model="form.author" placeholder="请输入文章作者" :maxlength="50" style="width:300px;"></el-input>
            </el-form-item>
            <el-form-item label="描述" prop="description">
                <el-input
                        type="textarea"
                        :rows="2"
                        :maxlength='50'
                        style="width: 300px"
                        placeholder="请输入描述内容"
                        v-model="form.description">
                </el-input>
            </el-form-item>
            <el-form-item label="图片">
                <el-upload
                        class="image-uploader"
                        :action="uploadUrl"
                        :withCredentials="true"
                        :show-file-list="false"
                        :on-success="handleImageSuccess"
                        :before-upload="beforeImageUpload">
                    <img v-if="form.image" :src="form.image" class="image">
                    <i v-else class="el-icon-plus image-uploader-icon"></i>
                    <div slot="tip" class="el-upload__tip">只能上传jpg/png文件，且不超过2MB,注：图片最佳展示尺寸为150px*100px</div>
                </el-upload>

            </el-form-item>
            <el-form-item label="状态"  required>
                <el-radio-group v-model="form.state">
                    <el-radio :label="1">显示</el-radio>
                    <el-radio :label="2">隐藏</el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item label="是否在通知栏展示"  required>
                <el-radio-group v-model="form.notice">
                    <el-radio :label="1">是</el-radio>
                    <el-radio :label="2">否</el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item label="排序" required>
                <el-input-number v-model="form.sort" placeholder="不填默认为0" :min="0" :max="1000" style="width:200px;"></el-input-number>
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
        uploadUrl: `${apiBase}upload`,
        form: {
          id: '',
          nav_id: '',
          title: '',
          author: '万匹思',
          content: '',
          sort: 0,
          description: '',
          state: 1,
          image: '',
          notice: 2,
        },
        id: this.$route.params.id,
        navChildren: [],
      };
    },
    mounted() {
      this.getNavChild();
      if (this.id) {
        this.$http.get(`${apiBase}get-article/${this.id}`)
          .then(getJsonAndCheckSuccess)
          .then((json) => {
            this.form = json.results;
          }).catch(reportErrorMessage(this));
      }
    },
    methods: {
      getNavChild() {
        this.$http.get(`${apiBase}article-need`)
          .then(response => response.json())
          .then((json) => {
            if (json.status === 'SUCCESS') {
              this.navChildren = json.results;
            }
          }).catch(() => {
            this.$message('系统错误');
          });
      },
      handleSubmit() {
        const params = {
          title: this.form.title,
          author: this.form.author,
          nav_id: this.form.nav_id,
          state: this.form.state,
          sort: this.form.sort,
          description: this.form.description,
          image: this.form.image,
          content: this.form.content,
          notice: this.form.notice,
        };
        if (this.id) {
          params.id = this.id;
          this.$http.put(`${apiBase}article-update`, params)
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
      // 图片上传
      handleImageSuccess(res) {
        // this.form.imageUrl = URL.createObjectURL(file.raw);
        this.form.image = res.url;
      },
      beforeImageUpload(file) {
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
    .article .image-uploader .el-upload {
        border: 1px dashed #d9d9d9;
        border-radius: 6px;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    .article .image-uploader .el-upload:hover {
        border-color: #409EFF;
    }
    .article .image-uploader-icon {
        font-size: 28px;
        color: #8c939d;
        width: 150px;
        height: 100px;
        line-height: 100px;
        text-align: center;
    }
    .article .image {
        width: 150px;
        height: 100px;
        display: block;
    }
</style>
