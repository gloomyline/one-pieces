<template>
    <div style="overflow: auto">
        <el-button type="success" style="margin-bottom: 5px" @click.native="$router.back(-1)">返回上一页</el-button>
        <el-form :model="form" :rules="rules" ref="articleForm" label-width="120px" style="width:1220px;">

            <h4>所属商户名称： {{ shop_name }}</h4>
            <el-form-item label="选择分类" prop="category" :rules="[{ required: true, message: '商品分类不能为空' }]">
                <el-select v-model="form.category" placeholder="选择分类" clearable>
                    <el-option-group
                            v-for="group in category"
                            :key="group.label"
                            :label="group.label">
                        <el-option
                                v-for="item in group.options"
                                :key="item.value"
                                :label="item.title"
                                :value="item.id">
                        </el-option>
                    </el-option-group>
                </el-select>
            </el-form-item>
            <h4>基本信息</h4>
            <el-row>
                <el-col :span="10">
                    <el-form-item label="商品名称" prop="title" :rules="[{ required: true, message: '商品名称不能为空' }]">
                        <el-input auto-complete="off" v-model="form.title" placeholder="请输入商品名称" style="width:300px;" :maxlength="50"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="商品货号" prop="no" :rules="[{ required: true, message: '商品货号不能为空' }]">
                        <el-input auto-complete="off" v-model="form.no" placeholder="请输入商品货号" style="width:300px;" :maxlength="20"></el-input>
                    </el-form-item>
                </el-col>
            </el-row>

            <el-form-item label="商品图片">
                <el-row>
                    <el-col :span="4" align="center">
                        <el-upload
                                ref="image0"
                                class="image-uploader"
                                :action="action"
                                :show-file-list="false"
                                :withCredentials="true"
                                :on-success="handleUploadOneSuccess"
                                :before-upload="beforeAvatarUpload">
                            <img v-if="pic.one" :src="pic.one" v-model="pic.one" class="image-article">
                            <i v-else class="el-icon-plus image-uploader-icon"></i>
                            <div slot="tip" class="el-upload__tip">
                                <el-button type="text" @click.native="" size="small">商品主图</el-button>
                                <el-button type="danger" @click.native="pic.one = ''; handleDelPic()" size="small" :disabled="!pic.one">删除</el-button></div>
                        </el-upload>
                    </el-col>
                    <el-col :span="4" align="center">
                        <el-upload
                                ref="image1"
                                class="image-uploader"
                                :action="action"
                                :show-file-list="false"
                                :withCredentials="true"
                                :disabled="!pic.one"
                                :on-success="handleUploadTwoSuccess"
                                :before-upload="beforeAvatarUpload">
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
                                :action="action"
                                :show-file-list="false"
                                :withCredentials="true"
                                :disabled="!pic.two"
                                :on-success="handleUploadThreeSuccess"
                                :before-upload="beforeAvatarUpload">
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
                                :action="action"
                                :show-file-list="false"
                                :withCredentials="true"
                                :disabled="!pic.three"
                                :on-success="handleUploadFourSuccess"
                                :before-upload="beforeAvatarUpload">
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
                                :action="action"
                                :show-file-list="false"
                                :withCredentials="true"
                                :disabled="!pic.four"
                                :on-success="handleUploadFiveSuccess"
                                :before-upload="beforeAvatarUpload">
                            <img v-if="pic.five" :src="pic.five" class="image-article">
                            <i v-else class="el-icon-plus image-uploader-icon"></i>
                            <div slot="tip" class="el-upload__tip">
                                <el-button type="primary" @click.native="handleSetMainPic(4)" size="small" :disabled="!pic.five">设置为主图</el-button>
                                <el-button type="danger" @click.native="pic.five = ''; handleDelPic()" size="small" :disabled="!pic.five">删除</el-button>
                            </div>
                        </el-upload>
                    </el-col>
                </el-row>
                <div>请上商品照片（第一张将作为商品封面，1~5张，不超过2Mb，最佳尺寸340*200px，支持JPG/PNG）</div>
            </el-form-item>
            <el-row style="text-align: center">
                <el-col :span="24" align="center">
                    <el-table :data="form.specList"  style="width: 1000px;" align="center">
                        <el-table-column type="index" align="center"></el-table-column>
                        <el-table-column property="spec" label="规格" align="center"></el-table-column>
                        <el-table-column property="price" label="价格（元）" align="center"></el-table-column>
                        <el-table-column property="stock" label="库存" align="center"  ></el-table-column>
                        <el-table-column label="操作" align="center" >
                            <template slot-scope="scope">
                                <el-button-group style="">
                                    <el-button type="primary" size="small" @click.native="handleUpdateSpec(scope.row)">编辑</el-button>
                                    <el-button type="danger" size="small" @click.native="handleDelSpec(scope.row.id)" v-if="form.specList.length > 1">删除</el-button>
                                </el-button-group>
                            </template>
                        </el-table-column>
                    </el-table>
                    <div style="width: 1000px;text-align: left;padding: 10px"> <el-button type="primary" @click.native="handleAddSpec"  size="small">新增规格</el-button></div>
                </el-col>
            </el-row>
            <el-form-item label="排序" prop="sort" required>
                <el-input-number auto-complete="off" v-model="form.sort" placeholder="" style="width:200px;" :min="0"></el-input-number>
            </el-form-item>
            <el-form-item style="padding: 0; margin: 0">
                <div style="color: red">(注：文本编辑上传的图片大小不超过3M，若上传图片为100%显示，无需手动调整)</div>
            </el-form-item>
            <el-form-item label="产品介绍">
                <div style="line-height: 0">
                    <Ueditor @ready="editorReadyIntro"></Ueditor>
                </div>
            </el-form-item>
            <el-form-item label="规格参数">
                <div style="line-height: 0">
                    <Ueditor @ready="editorReadySpec"></Ueditor>
                </div>
            </el-form-item>
            <el-form-item label="售后">
                <div style="line-height: 0">
                    <Ueditor @ready="editorReadyService"></Ueditor>
                </div>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click.native="handleSubmit">保存</el-button>
                <el-button type="primary" @click="$router.back()">取消</el-button>
            </el-form-item>
        </el-form>
        <!--添加/编辑规格-->
        <div>
            <el-dialog :visible.sync="dialogSpecFormVisible" :title="specForm.id === '' ? '添加规格' : '编辑规格'">
                <el-form :model="specForm" :rules="rules" ref="addForm" label-width="120px">
                    <el-form-item label="规格" prop="spec" :rules="[{ required: true, message: '规格不能为空' }]">
                        <el-input auto-complete="off" v-model="specForm.spec" placeholder="请输入规格" style="width:200px;" :maxlength="23"></el-input>
                    </el-form-item>
                    <el-form-item label="价格（元）" prop="price" :rules="[{ required: true, message: '价格不能为空' }]" >
                        <el-input-number auto-complete="off" v-model="specForm.price" placeholder="价格填写不能超过单笔限额" :min="0" style="width:200px;"></el-input-number>
                    </el-form-item>
                    <el-form-item label="库存" prop="stock" :rules="[{ required: true, message: '库存不能为空' }]">
                        <el-input-number v-model="specForm.stock" placeholder="请输入" :min="0" style="width:200px;"></el-input-number>
                    </el-form-item>
                </el-form>
                <div slot="footer" class="dialog-footer">
                    <el-button @click="dialogSpecFormVisible = false">取 消</el-button>
                    <el-button type="primary" @click="handleSubmitSpec" v-if="specForm.id === ''">提 交</el-button>
                    <el-button type="primary" @click="handleSubmitUpdateSpec" v-if="specForm.id !== ''">保 存</el-button>
                </div>
            </el-dialog>
        </div>
    </div>
</template>
<script>

  import apiBase from '../../apiBase';
  import { getJsonAndCheckSuccess, reportErrorMessage } from '../../ApiUtil';
  import Ueditor from '../../components/Ueditor';

  export default {
    name: 'productForm',
    components: {
      Ueditor,
    },
    data() {
      return {
        // action: `${apiBase}upload`,
        action: `${apiBase}qiniu-upload/pro`,
        product_id: this.$route.params.id,
        fileList: [],
        id: this.$route.params.id,
        category: [],
        form: {
          title: '',
          category: [],
          no: '',
          picArr: [],
          sort: 0,
          intro: '',
          spec: '',
          service: '',
          specList: [],
        },
        specForm: {
          id: '',
          spec: '',
          price: '',
          stock: '',
        },
        shop_name: '',
        pic: {
          one: '',
          two: '',
          three: '',
          four: '',
          five: '',
        },
        dialogSpecFormVisible: false,
      };
    },
    mounted() {
      this.getData();
      this.getDetail();
    },
    methods: {
      getDetail() {
        this.$http.get(`${apiBase}detail/${this.$route.params.id}`)
          .then(getJsonAndCheckSuccess)
          .then((response) => {
            this.form = response.result;
            this.picFormatter();
          }).catch(reportErrorMessage(this));
      },
      getData() {
        this.$http.get(`${apiBase}need`)
          .then(getJsonAndCheckSuccess)
          .then((response) => {
            this.category = response.result;
            this.shop_name = response.shop_name;
          }).catch(reportErrorMessage(this));
      },
      AddList() {
        this.form.specList.push({ spec: '', price: '', stock: '' });
      },
      handleAddSpec() {
        this.clearSpecForm();
        this.dialogSpecFormVisible = true;
      },
      delList(i) {
        this.form.specList.splice(i, 1);
      },
      handleSubmit() {
        this.handleChange();
        const params = {
          title: this.form.title,
          category: this.form.category,
          no: this.form.no,
          sort: this.form.sort,
          picArr: this.form.picArr,
          intro: this.form.intro,
          spec: this.form.spec,
          service: this.form.service,
        };
        this.$http.put(`${apiBase}update-pro/${this.product_id}`, params)
          .then(getJsonAndCheckSuccess)
          .then(() => {
            this.$message({
              type: 'success',
              message: '保存成功！',
            });
            this.$router.push({ path: '/product-list' });
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
      editorReadyIntro(instance) {
        setTimeout(() => {
          instance.setContent(this.form.intro);
        }, 1000);
        instance.addListener('contentChange', () => {
          this.form.intro = instance.getContent();
        });
      },
      editorReadySpec(instance) {
        setTimeout(() => {
          instance.setContent(this.form.spec);
        }, 1000);
        instance.addListener('contentChange', () => {
          this.form.spec = instance.getContent();
        });
      },
      editorReadyService(instance) {
        setTimeout(() => {
          instance.setContent(this.form.service);
        }, 1000);
        instance.addListener('contentChange', () => {
          this.form.service = instance.getContent();
        });
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
      handleDelSpec(id) {
        this.$confirm('确定执行删除操作吗?', '提示', {
          type: 'warning',
        }).then(() => {
          this.$http.post(`${apiBase}del-spec`, { pro_spec_id: id })
            .then(response => response.json())
            .then((json) => {
              if (json.status !== 'SUCCESS') {
                return Promise.reject(json.error_message);
              }
              this.handleDelSpecSuccess(id);
              this.$message({
                type: 'success',
                message: '删除成功！',
              });
              return {};
            }).catch(reportErrorMessage(this));
        }).catch(() => {});
      },
      handleSubmitSpec() {
        const params = {
          product_id: this.product_id,
          spec: this.specForm.spec,
          price: this.specForm.price,
          stock: this.specForm.stock,
        };
        this.$http.post(`${apiBase}add-spec`, params)
          .then(response => response.json())
          .then((json) => {
            if (json.status !== 'SUCCESS') {
              return Promise.reject(json.error_message);
            }
            this.form.specList = json.result;
            this.$message({
              type: 'success',
              message: '添加成功！',
            });
            this.dialogSpecFormVisible = false;
            return {};
          }).catch(reportErrorMessage(this));
      },
      handleSubmitUpdateSpec() {
        const params = {
          spec: this.specForm.spec,
          price: this.specForm.price,
          stock: this.specForm.stock,
        };
        this.$http.put(`${apiBase}update-spec/${this.specForm.id}`, params)
          .then(response => response.json())
          .then((json) => {
            if (json.status !== 'SUCCESS') {
              return Promise.reject(json.error_message);
            }
            this.$message({
              type: 'success',
              message: '编辑成功！',
            });
            // this.getData();
            this.handleChangeSpecList();
            this.dialogSpecFormVisible = false;
            return {};
          }).catch(reportErrorMessage(this));
      },
      // 编辑后的数据跟新
      handleChangeSpecList() {
        for (let i = 0; i < this.form.specList.length;) {
          if (this.form.specList[i].id && this.form.specList[i].id === this.specForm.id) {
            this.form.specList[i].spec = this.specForm.spec;
            this.form.specList[i].price = this.specForm.price;
            this.form.specList[i].stock = this.specForm.stock;
          }
          i += 1;
        }
      },
      // 删除后规格列表数据跟新
      handleDelSpecSuccess(id) {
        for (let i = 0; i < this.form.specList.length;) {
          if (this.form.specList[i].id && this.form.specList[i].id === id) {
            this.form.specList.splice(i, 1);
            break;
          }
          i += 1;
        }
      },
      clearSpecForm() {
        this.specForm = {
          id: '',
          spec: '',
          price: '',
          stock: '',
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