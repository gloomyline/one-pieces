<template>
    <div style="overflow: auto">
        <el-form :model="form" :rules="rules" ref="shopDecorateForm" label-width="120px" style="width:1220px;">
            <el-form-item label="店铺logo" prop="title" :rules="[{ required: true, message: '商品名称不能为空' }]" >
                <el-upload
                        ref="image0"
                        class="image-uploader"
                        :action="actionLogo"
                        :show-file-list="false"
                        :withCredentials="true"
                        :on-success="handleUploadLogoSuccess"
                        :before-upload="beforeUpload">
                    <img v-if="form.logo" :src="form.logo" v-model="form.logo-logo" class="image-logo">
                    <i v-else class="el-icon-plus image-uploader-icon"></i>
                    <div slot="tip" class="el-upload__tip">
                        <el-button type="text" @click.native="" size="small">上传店铺logo</el-button>(大小不超过2Mb，最佳尺寸128*128px，支持JPG/PNG)
                    </div>
                </el-upload>
            </el-form-item>
            <el-form-item label="商户店铺图片" required>
                <el-row>
                    <el-col :span="4" align="center">
                        <el-upload
                                ref="image0"
                                class="image-uploader"
                                :action="actionShop"
                                :show-file-list="false"
                                :withCredentials="true"
                                :on-success="handleUploadOneSuccess"
                                :before-upload="beforeUpload">
                            <img v-if="pic.one" :src="pic.one" v-model="pic.one" class="image-article">
                            <i v-else class="el-icon-plus image-uploader-icon"></i>
                            <div slot="tip" class="el-upload__tip">
                                <el-button type="text" @click.native="" size="small">店铺主图</el-button>
                                <el-button type="danger" @click.native="pic.one = ''; handleDelPic()" size="small" :disabled="!pic.one">删除</el-button></div>
                        </el-upload>
                    </el-col>
                    <el-col :span="4" align="center">
                        <el-upload
                                ref="image1"
                                class="image-uploader"
                                :action="actionShop"
                                :show-file-list="false"
                                :withCredentials="true"
                                :disabled="!pic.one"
                                :on-success="handleUploadTwoSuccess"
                                :before-upload="beforeUpload">
                            <img v-if="pic.two" :src="pic.two" class="image-article">
                            <i v-else class="el-icon-plus image-uploader-icon"></i>
                            <div slot="tip" class="el-upload__tip">
                                <el-button type="primary" @click.native="handleSetMainPic(1)" size="small" :disabled="!pic.two">设置为主图</el-button>
                                <el-button type="danger" @click.native="pic.two = ''; handleDelPic()" size="small" :disabled="!pic.two">删除</el-button>
                            </div>
                        </el-upload>
                    </el-col>
                    <el-col :span="4" align="center">
                        <el-upload
                                ref="image1"
                                class="image-uploader"
                                :action="actionShop"
                                :show-file-list="false"
                                :withCredentials="true"
                                :disabled="!pic.two"
                                :on-success="handleUploadThreeSuccess"
                                :before-upload="beforeUpload">
                            <img v-if="pic.three" :src="pic.three" class="image-article">
                            <i v-else class="el-icon-plus image-uploader-icon"></i>
                            <div slot="tip" class="el-upload__tip">
                                <el-button type="primary" @click.native="handleSetMainPic(2)" size="small" :disabled="!pic.three">设置为主图</el-button>
                                <el-button type="danger" @click.native="pic.three = ''; handleDelPic()" size="small" :disabled="!pic.three">删除</el-button>
                            </div>
                        </el-upload>
                    </el-col>
                    <el-col :span="4" align="center">
                        <el-upload
                                ref="image1"
                                class="image-uploader"
                                :action="actionShop"
                                :show-file-list="false"
                                :withCredentials="true"
                                :disabled="!pic.three"
                                :on-success="handleUploadFourSuccess"
                                :before-upload="beforeUpload">
                            <img v-if="pic.four" :src="pic.four" class="image-article">
                            <i v-else class="el-icon-plus image-uploader-icon"></i>
                            <div slot="tip" class="el-upload__tip">
                                <el-button type="primary" @click.native="handleSetMainPic(3)" size="small" :disabled="!pic.four">设置为主图</el-button>
                                <el-button type="danger" @click.native="pic.four = ''; handleDelPic()" size="small" :disabled="!pic.four">删除</el-button>
                            </div>
                        </el-upload>
                    </el-col>
                    <el-col :span="4" align="center">
                        <el-upload
                                ref="image1"
                                class="image-uploader"
                                :action="actionShop"
                                :show-file-list="false"
                                :withCredentials="true"
                                :disabled="!pic.four"
                                :on-success="handleUploadFiveSuccess"
                                :before-upload="beforeUpload">
                            <img v-if="pic.five" :src="pic.five" class="image-article">
                            <i v-else class="el-icon-plus image-uploader-icon"></i>
                            <div slot="tip" class="el-upload__tip">
                                <el-button type="primary" @click.native="handleSetMainPic(4)" size="small" :disabled="!pic.five">设置为主图</el-button>
                                <el-button type="danger" @click.native="pic.five = ''; handleDelPic()" size="small" :disabled="!pic.five">删除</el-button>
                            </div>
                        </el-upload>
                    </el-col>
                </el-row>
                <div>请上店铺照片（第一张将作为店铺封面，1~5张，不超过2Mb，最佳750*400px，支持JPG/PNG）</div>
            </el-form-item>
            <el-form-item label="商户介绍" prop="intro">
                <el-input :maxlength="30" v-model="form.intro" placeholder="" style="width:600px;" ></el-input>
            </el-form-item>
            <el-form-item label="商品分类" prop="sort">
                <span v-for="item in form.category" v-text="item + '、 '"></span>
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
    name: 'shopDecorateForm',
    components: {
    },
    data() {
      return {
        // action: `${apiBase}upload`,
        actionLogo: `${apiBase}qiniu-upload/logo`,
        actionShop: `${apiBase}qiniu-upload/shop`,
        fileList: [],
        id: this.$route.params.id,
        form: {
          picArr: [],
          intro: '',
          logo: '',
          category: [],
        },
        pic: {
          one: '',
          two: '',
          three: '',
          four: '',
          five: '',
        },
      };
    },
    mounted() {
      this.getDetail();
    },
    methods: {
      getDetail() {
        this.$http.get(`${apiBase}shop-detail`)
          .then(getJsonAndCheckSuccess)
          .then((response) => {
            this.form = response.result;
            this.picFormatter();
          }).catch(reportErrorMessage(this));
      },
      handleSubmit() {
        this.handleChange(); // 图片调整
        const params = {
          logo: this.form.logo,
          picArr: this.form.picArr,
          intro: this.form.intro,
        };
        this.$http.put(`${apiBase}decorate`, params)
          .then(getJsonAndCheckSuccess)
          .then(() => {
            this.$message({
              type: 'success',
              message: '保存成功！',
            });
            this.getDetail();
            return {};
          }).catch(reportErrorMessage(this));
      },
      // 图片初始化
      picFormatter() {
        this.clearPic();
        if (this.form.picArr[0]) {
          this.pic.one = this.form.picArr[0];
        }
        if (this.form.picArr[1]) {
          this.pic.two = this.form.picArr[1];
        }
        if (this.form.picArr[2]) {
          this.pic.three = this.form.picArr[2];
        }
        if (this.form.picArr[3]) {
          this.pic.four = this.form.picArr[3];
        }
        if (this.form.picArr[4]) {
          this.pic.five = this.form.picArr[4];
        }
      },
      // 图片上传成功
      handleUploadLogoSuccess(res) {
        this.form.logo = res.url;
      },
      handleUploadOneSuccess(res) {
        this.pic.one = res.url;
      },
      handleUploadTwoSuccess(res) {
        this.pic.two = res.url;
      },
      handleUploadThreeSuccess(res) {
        this.pic.three = res.url;
      },
      handleUploadFourSuccess(res) {
        this.pic.four = res.url;
      },
      handleUploadFiveSuccess(res) {
        this.pic.five = res.url;
      },
      // 删除图片
      handleDelPic() {
        this.handleChange();
        this.picFormatter();
      },
      // 清空pic
      clearPic() {
        this.pic = {
          one: '',
          two: '',
          three: '',
          four: '',
          five: '',
        };
      },
      // 赋值
      handleChange() {
        let i = 0;
        this.form.picArr = [];
        if (this.pic.one) {
          this.form.picArr[i] = this.pic.one;
          i += 1;
        }
        if (this.pic.two) {
          this.form.picArr[i] = this.pic.two;
          console.log(this.pic.one);
          i += 1;
        }
        if (this.pic.three) {
          this.form.picArr[i] = this.pic.three;
          i += 1;
        }
        if (this.pic.four) {
          this.form.picArr[i] = this.pic.four;
          i += 1;
        }
        if (this.pic.five) {
          this.form.picArr[i] = this.pic.five;
          i += 1;
        }
      },
      handleSetMainPic(i) {
        if (i === 1) {
          const temp = this.pic.two;
          this.pic.two = this.pic.one;
          this.pic.one = temp;
        }
        if (i === 2) {
          const temp = this.pic.three;
          this.pic.three = this.pic.one;
          this.pic.one = temp;
        }
        if (i === 3) {
          const temp = this.pic.four;
          this.pic.four = this.pic.one;
          this.pic.one = temp;
        }
        if (i === 4) {
          const temp = this.pic.five;
          this.pic.five = this.pic.one;
          this.pic.one = temp;
        }
      },
      beforeUpload(file) {
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
      handleUpdateSpec(row) {
        this.dialogSpecFormVisible = true;
        this.specForm = {
          id: row.id,
          spec: row.spec,
          price: row.price,
          stock: row.stock,
        };
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
        width: 150px;
        height: 133px;
        line-height: 133px;
        text-align: center;
    }
    .image-article {
        width: 150px;
        height: 133px;
        display: block;
    }
    .image-uploader-icon-logo {
        font-size: 28px;
        color: #8c939d;
        width: 150px;
        height: 150px;
        line-height: 150px;
        text-align: center;
    }
    .image-logo {
        width: 150px;
        height: 150px;
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