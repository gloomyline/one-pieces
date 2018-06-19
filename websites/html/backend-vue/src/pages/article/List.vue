<template>
    <div class="article">
        <el-row>
            <el-col :span="24">
                <el-form :inline="true">
                    <el-form-item>
                        <el-button type="primary" @click.native="dialogFormVisibleParams = true">文章管理导航配置</el-button>
                        <el-button type="success" @click.native="handleAdd">添加文章</el-button>
                    </el-form-item>
                    <el-form-item label="文章分类">
                        <el-select v-model="form.nav_id" placeholder="请选择" clearable>
                            <el-option
                                    v-for="item in navChildren"
                                    :label="item.name"
                                    :value="item.id">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" @click.native="filter">查找</el-button>
                        <el-button type="primary" @click.native="clearFilter">清空筛选</el-button>
                    </el-form-item>
                </el-form>
            </el-col>
        </el-row>
        <el-table :data="tableData" stripe border style="width: 100%;">
            <el-table-column property="id" label="ID" align="center" min-width="60"></el-table-column>
            <el-table-column property="title" label="名称" align="center" min-width="120"></el-table-column>
            <el-table-column property="nav_id" label="分类" align="center" min-width="120" :formatter="formatterNav"></el-table-column>
            <el-table-column property="description" label="描述" align="center" min-width="120"></el-table-column>
            <el-table-column property="state" label="状态" align="center" width="150" :formatter="formatterState"></el-table-column>
            <el-table-column property="image" label="图片" align="center" min-width="80">
                <template slot-scope="scope">
                    <el-popover
                            ref="popover"
                            placement="right"
                            width="600"
                            trigger="click">
                        <img :src="scope.row.image" width="100%" height="100%"/>
                    </el-popover>
                    <el-button v-popover:popover v-if="scope.row.image" style="padding: 0"><img :src="scope.row.image" width="40" height="40"/></el-button>
                    <span v-else>无</span>
                </template>
            </el-table-column>
            <el-table-column property="sort" label="排序" align="center" min-width="80"></el-table-column>
            <el-table-column property="created_at" label="添加时间" align="center" width="180"></el-table-column>
            <el-table-column label="操作" align="center" min-width="150">
                <template slot-scope="scope">
                    <el-button-group>
                        <el-button type="primary" size="small" @click.native="handleUpdate(scope.row.id)">编辑</el-button>
                        <el-button type="danger" size="small" @click.native="handleDel(scope.row.id)">删除</el-button>
                    </el-button-group>
                </template>
            </el-table-column>
        </el-table>
        <page :total="pageCount" :current-page="currentPage" :page-size="pageSize" @current-change="handleCurrentChange"></page>
        <div>
            <el-dialog  @close="handleClose" :visible.sync="dialogFormVisible" title="文章">

            </el-dialog>
        </div>
        <el-dialog :visible.sync="dialogFormVisibleParams" title="文章导航配置">
            <el-form>
                <el-form-item label="请选择文章所在导航">
                    <el-select v-model="paramForm.article_id" placeholder="请选择" style="width: 200px" clearable>
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
    name: 'Article',
    data() {
      return {
        uploadUrl: `${apiBase}upload`,
        tableData: [],
        form: {
          nav_id: '',
        },
        paramForm: {
          article_id: '',
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
        this.$http.get(`${apiBase}get-nav/${this.paramForm.article_id}`)
          .then(response => response.json())
          .then((json) => {
            if (json.status === 'SUCCESS') {
              this.navChildren = json.results;
            }
          }).catch(() => {
            this.$message('系统错误');
          });
      },
      filter() {
        this.currentPage = 1;
        this.getData();
      },
      clearFilter() {
        this.form = {
          nav_id: '',
        };
        this.getData();
      },
      getData() {
        const params = {
          limit: this.pageSize,
          offset: this.pageSize * (this.currentPage - 1),
        };
        if (this.form.nav_id) {
          params.nav_id = this.form.nav_id;
        }
        this.$http.get(`${apiBase}article`, { params })
          .then(response => response.json())
          .then((json) => {
            if (json.status === 'SUCCESS') {
              this.tableData = json.results;
              this.pageCount = json.count;
              this.paramForm.article_id = json.article_id;
              if (this.paramForm.article_id) {
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
        this.$confirm('确定删除该文章信息?', '提示', {
          type: 'warning',
        }).then(() => {
          this.$http.post(`${apiBase}article-del`, { article_id: id })
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
          article_id: this.paramForm.article_id,
        };
        this.$http.put(`${apiBase}set-params/article`, params)
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
      clearForm() {
        this.form = {
          id: '',
          name: '',
          sort: 0,
          description: '',
          state: 1,
          image: '',
          content: '',
        };
      },
      handleClose() {
        this.clearForm();
        this.disableFlag = false;
      },
      // 新增
      handleAdd() {
        if (!this.paramForm.article_id) {
          this.$message({
            message: '请先完善文章管理导航，再添加文章',
            type: 'warning',
          });
          return;
        }
        this.$router.push({
          path: '/article-add',
        });
      },
      // 编辑
      handleUpdate(id) {
        this.$router.push({
          path: `/article-update/${id}`,
        });
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
    },
  };
</script>

