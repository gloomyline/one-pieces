<template>
    <div class="example">
        <el-row>
            <el-col :span="24">
                <el-form :inline="true">
                    <el-form-item>
                        <el-button type="primary" @click.native="dialogFormVisibleParams = true">案例中心导航配置</el-button>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="success" @click.native="handleAdd">添加案例</el-button>
                    </el-form-item>
                </el-form>
            </el-col>
        </el-row>
        <el-table :data="tableData" stripe border style="width: 100%;">
            <el-table-column property="id" label="ID" align="center" min-width="60"></el-table-column>
            <el-table-column property="name" label="名称" align="center" min-width="120"></el-table-column>
            <el-table-column property="nav_id" label="分类" align="center" min-width="120" :formatter="formatterNav"></el-table-column>
            <el-table-column property="description" label="描述" align="center" min-width="120"></el-table-column>
            <el-table-column property="link" label="URL链接地址" align="center" width="150"></el-table-column>
            <el-table-column property="state" label="状态" align="center" width="150" :formatter="formatterState"></el-table-column>
            <el-table-column property="image" label="图片" align="center" min-width="80">
                <template slot-scope="scope">
                    <el-popover
                            ref="popover4"
                            placement="right"
                            width="600"
                            trigger="click">
                        <img :src="scope.row.image" width="100%" height="100%"/>
                    </el-popover>
                    <el-button v-popover:popover4 v-if="scope.row.image" style="padding: 0"><img :src="scope.row.image" width="40" height="40"/></el-button>
                    <span v-else>无</span>
                </template>
            </el-table-column>
            <el-table-column property="sort" label="排序" align="center" min-width="80"></el-table-column>
            <el-table-column property="created_at" label="添加时间" align="center" width="180"></el-table-column>
            <el-table-column label="操作" align="center" min-width="150">
                <template slot-scope="scope">
                    <el-button-group>
                        <el-button type="primary" size="small" @click.native="handleUpdate(scope.row)">编辑</el-button>
                        <el-button type="danger" size="small" @click.native="handleDel(scope.row.id)">删除</el-button>
                    </el-button-group>
                </template>
            </el-table-column>
        </el-table>
        <page :total="pageCount" :current-page="currentPage" :page-size="pageSize" @current-change="handleCurrentChange"></page>
        <div>
            <el-dialog  @close="handleClose" :visible.sync="dialogFormVisible" title="案例">
                <el-form :model="form" ref="form" label-width="140px" label-position="left">
                    <el-form-item label="请选择案例分类" prop="nav_id" :rules="[{ required: true, message: '案例分类不能为空' }]">
                        <el-select v-model="form.nav_id" placeholder="请选择" style="width: 200px" clearable>
                            <el-option v-for="(item,index) in navChildren" :label="item.name" :value="item.id"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="名称" prop="name" :rules="[{ required: true, message: '案例名称' }]">
                        <el-input v-model="form.name" placeholder="请输入名称" :maxlength="10" style="width:200px;"></el-input>
                    </el-form-item>
                    <el-form-item label="描述" prop="description">
                        <el-input
                                type="textarea"
                                :rows="2"
                                :maxlength='50'
                                style="width: 200px"
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
                            <div slot="tip" class="el-upload__tip">只能上传jpg/png文件，且不超过2MB,注：案例图片最佳展示尺寸为300px*200px</div>
                        </el-upload>

                    </el-form-item>
                    <el-form-item label="URL链接地址" prop="link">
                        <el-input v-model="form.link" placeholder="例：http://www.onepieces.cn" :maxlength="50" style="width:200px;"></el-input>
                    </el-form-item>
                    <el-form-item label="状态"  required>
                        <el-radio-group v-model="form.state">
                            <el-radio :label="1">显示</el-radio>
                            <el-radio :label="2">隐藏</el-radio>
                        </el-radio-group>
                    </el-form-item>
                    <el-form-item label="排序" required>
                        <el-input-number v-model="form.sort" placeholder="不填默认为0" :min="0" :max="1000" style="width:200px;"></el-input-number>
                    </el-form-item>

                </el-form>
                <span slot="footer" class="dialog-footer">
                  <el-button type="primary" @click.native="handleSubmit" v-if="form.id === ''">保存</el-button>
                  <el-button type="primary" @click.native="handleSubmitUpdate" v-if="form.id !=='' ">保存修改</el-button>
                  <el-button @click.native="dialogFormVisible = false">关闭</el-button>
                </span>
            </el-dialog>
        </div>
        <el-dialog :visible.sync="dialogFormVisibleParams" title="案例中心导航配置">
            <el-form>
                <el-form-item label="请选择案例中心所在导航">
                    <el-select v-model="paramForm.example_id" placeholder="请选择" style="width: 200px" clearable>
                        <el-option v-for="(item,index) in nav" :label="item.name" :value="item.id"></el-option>
                    </el-select>
                </el-form-item>
            </el-form>
            <span slot="footer" class="dialog-footer">
                  <el-button type="primary" @click.native="handleSubmitParams">保存</el-button>
            </span>
        </el-dialog>
    </div>
</template>
<script>
  import apiBase from '../../apiBase';
  import Page from '../../components/Page';
  import { reportErrorMessage } from '../../ApiUtil';

  export default {
    name: 'example',
    data() {
      return {
        uploadUrl: `${apiBase}upload`,
        tableData: [],
        form: {
          id: '',
          nav_id: '',
          name: '',
          link: '',
          sort: 0,
          description: '',
          state: 1,
          image: '',
        },
        paramForm: {
          example_id: '',
        },
        nav: [],
        navChildren: [],
        pageCount: 0,
        currentPage: 1,
        pageSize: 20,
        dialogFormVisible: false,
        dialogFormVisibleParams: false,
        disableFlag: false,
      };
    },
    components: {
      Page,
    },
    mounted() {
      if (!isNaN(this.$route.query.page)) {
        this.currentPage = Number(this.$route.query.page);
      }
      this.getNav();
      this.getData();
    },
    methods: {
      getNav() {
        this.$http.get(`${apiBase}get-nav/0`)
          .then(response => response.json())
          .then((json) => {
            if (json.status === 'SUCCESS') {
              this.nav = json.results;
            }
          }).catch(() => {
            this.$message('系统错误');
          });
      },
      getNavChild() {
        this.$http.get(`${apiBase}get-nav/${this.paramForm.example_id}`)
          .then(response => response.json())
          .then((json) => {
            if (json.status === 'SUCCESS') {
              this.navChildren = json.results;
            }
          }).catch(() => {
            this.$message('系统错误');
          });
      },
      getData() {
        const params = {
          limit: this.pageSize,
          offset: this.pageSize * (this.currentPage - 1),
        };
        this.$http.get(`${apiBase}example`, { params })
          .then(response => response.json())
          .then((json) => {
            if (json.status === 'SUCCESS') {
              this.tableData = json.results;
              this.pageCount = json.count;
              this.paramForm.example_id = json.example_id;
              if (this.paramForm.example_id) {
                this.getNavChild();
              }
            }
          }).catch(() => {
            this.$message('系统错误');
          });
      },
      handleCurrentChange(page) {
        this.currentPage = page;
        this.getData();
      },
      handleDel(id) {
        this.$confirm('确定删除该案例信息?', '提示', {
          type: 'warning',
        }).then(() => {
          this.$http.post(`${apiBase}example-del`, { example_id: id })
            .then(response => response.json())
            .then((json) => {
              if (json.status !== 'SUCCESS') {
                return Promise.reject(json.error_message);
              }
              this.$message({
                type: 'success',
                message: '删除成功！',
              });
              this.getData();
              return {};
            }).catch(reportErrorMessage(this));
        }).catch(() => {});
      },
      handleSubmitParams() {
        const params = {
          example_id: this.paramForm.example_id,
        };
        this.$http.put(`${apiBase}set-params/example`, params)
          .then(response => response.json())
          .then((json) => {
            if (json.status !== 'SUCCESS') {
              return Promise.reject(json.error_message);
            }
            this.$message({
              type: 'success',
              message: '设置成功！',
            });
            this.getData();
            this.dialogFormVisibleParams = false;
            return {};
          }).catch(reportErrorMessage(this));
      },
      handleSubmit() {
        const params = {
          name: this.form.name,
          nav_id: this.form.nav_id,
          link: this.form.link,
          state: this.form.state,
          sort: this.form.sort,
          description: this.form.description,
          image: this.form.image,
        };
        this.$http.post(`${apiBase}example-add`, params)
          .then(response => response.json())
          .then((json) => {
            if (json.status !== 'SUCCESS') {
              return Promise.reject(json.error_message);
            }
            this.$message({
              type: 'success',
              message: '添加成功！',
            });
            this.getData();
            this.dialogFormVisible = false;
            return {};
          }).catch(reportErrorMessage(this));
      },
      handleUpdate(row) {
        if (!this.navChildren.length) {
          this.getNavChild();
        }
        this.form = row;
        this.dialogFormVisible = true;
      },
      handleSubmitUpdate() {
        const params = {
          id: this.form.id,
          nav_id: this.form.nav_id,
          name: this.form.name,
          link: this.form.link,
          state: this.form.state,
          sort: this.form.sort,
          description: this.form.description,
          image: this.form.image,
        };
        this.$http.put(`${apiBase}example-update`, params)
          .then(response => response.json())
          .then((json) => {
            if (json.status !== 'SUCCESS') {
              return Promise.reject(json.error_message);
            }
            this.$message({
              type: 'success',
              message: '编辑成功！',
            });
            this.getData();
            this.dialogFormVisible = false;
            return {};
          }).catch(reportErrorMessage(this));
      },
      clearForm() {
        this.form = {
          id: '',
          name: '',
          link: '',
          sort: 0,
          description: '',
          state: 1,
          image: '',
        };
      },
      handleClose() {
        this.clearForm();
        this.disableFlag = false;
      },
      // 新增
      handleAdd() {
        if (!this.paramForm.example_id) {
          this.$message({
            message: '请先完善案例中心导航，再添加案例',
            type: 'warning',
          });
          return;
        }
        if (!this.navChildren.length) {
          this.getNavChild();
        }
        this.dialogFormVisible = true;
      },
      formatterState(row) {
        switch (row.state) {
          case 1 : return '显示';
          case 2 : return '隐藏';
          default : return '';
        }
      },
      formatterNav(row) {
        return this.navChildren.find(v => row.nav_id === v.id) ? this.navChildren.find(v => row.nav_id === v.id).name : '';
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
    },
  };
</script>

<style>
    .example .image-uploader .el-upload {
        border: 1px dashed #d9d9d9;
        border-radius: 6px;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    .example .image-uploader .el-upload:hover {
        border-color: #409EFF;
    }
    .example .image-uploader-icon {
        font-size: 28px;
        color: #8c939d;
        width: 300px;
        height: 200px;
        line-height: 200px;
        text-align: center;
    }
    .example .image {
        width: 300px;
        height: 200px;
        display: block;
    }
</style>
