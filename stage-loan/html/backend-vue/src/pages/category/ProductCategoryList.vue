<template>
    <div>
        <el-row v-if="!this.parent_id">
            <el-col :span="24">
                <el-form :inline="true">
                    <el-form-item label="商户分类">
                        <el-select v-model="searchForm.parent_id" placeholder="请选择" clearable>
                            <el-option
                                    v-for="item in parents"
                                    :label="item.title"
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
        <el-row v-if="this.parent_id">
            <el-button type="success" style="margin-bottom: 5px" @click.native="$router.back(-1)">返回上一页</el-button>
        </el-row>
        <el-table :data="tableData" stripe border style="width: 100%;">
            <el-table-column property="id" label="ID" align="center" min-width="60"></el-table-column>
            <el-table-column property="title" label="分类名称" align="center" min-width="100"></el-table-column>
            <el-table-column property="level" label="级别" align="center" width="150" :formatter="formatterLevel"></el-table-column>
            <el-table-column property="parent_name" label="上级分类名称" align="center" width="150" :formatter="formatterParentName"></el-table-column>
            <el-table-column property="is_show" label="是否显示" align="center" min-width="190" :formatter="formatterIsShow"></el-table-column>
            <el-table-column property="sort" label="排序" align="center" min-width="80"></el-table-column>
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
            <el-dialog  :visible.sync="dialogFormVisible" @close="handleClose">
                <el-form :model="form" ref="form" label-width="120px" label-position="left">
                    <el-form-item label="分类名称" required>
                        <el-input v-model="form.title" placeholder="分类名称" :maxlength="10" style="width:200px;"></el-input>
                    </el-form-item>
                    <el-form-item label="上级名称">
                        <el-select v-model="form.parent_id" placeholder="请选择" style="width: 200px" clearable>
                            <el-option v-for="(item,index) in parents" :label="item.title" :value="String(item.id)"></el-option>
                        </el-select><br/><span>不选择分类默认为顶级分类</span>
                    </el-form-item>
                    <el-form-item label="排序" required>
                        <el-input-number v-model="form.sort" placeholder="不填默认为0" :min="0" :max="1000" style="width:200px;"></el-input-number>
                    </el-form-item>
                    <el-form-item label="是否显示"  required>
                        <el-radio-group v-model="form.is_show">
                            <el-radio :label="1">是</el-radio>
                            <el-radio :label="0">否</el-radio>
                        </el-radio-group>
                    </el-form-item>
                    <el-form-item label="分类描述">
                        <el-input
                                type="textarea"
                                :rows="3"
                                :maxlength='200'
                                style="width: 80%"
                                placeholder="请输入内容"
                                v-model="form.description">
                        </el-input>
                    </el-form-item>
                </el-form>
                <span slot="footer" class="dialog-footer">
                  <el-button type="primary" @click.native="handleSubmitUpdate" v-if="form.id !==''">保存修改</el-button>
                  <el-button @click.native="dialogFormVisible = false">关闭</el-button>
                </span>
            </el-dialog>
        </div>
    </div>
</template>
<script>
  import apiBase from '../../apiBase';
  import Page from '../../components/Page';
  import { reportErrorMessage } from '../../ApiUtil';

  export default {
    name: 'Category',
    data() {
      return {
        tableData: [],
        parents: [],
        searchForm: {
          parent_id: '',
        },
        form: {
          id: '',
          title: '',
          parent_id: '',
          sort: 0,
          description: '',
          is_show: 1,
        },
        parent_id: this.$route.query.parent_id,
        pageCount: 0,
        currentPage: 1,
        pageSize: 20,
        dialogFormVisible: false,
        flag: false,
      };
    },
    components: {
      Page,
    },
    mounted() {
      if (!isNaN(this.$route.query.page)) {
        this.currentPage = Number(this.$route.query.page);
      }
      this.getData();
    },
    methods: {
      getData() {
        const params = {
          limit: this.pageSize,
          offset: this.pageSize * (this.currentPage - 1),
        };
        if (this.searchForm.parent_id !== '') {
          params.parent_id = this.searchForm.parent_id;
        }
        if (this.parent_id) {
          params.parent_id = this.parent_id;
        }
        this.$http.get(`${apiBase}category-pro`, { params })
          .then(response => response.json())
          .then((json) => {
            if (json.status === 'SUCCESS') {
              this.tableData = json.results.children;
              this.parents = json.results.parent;
              this.pageCount = json.count;
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
        this.searchForm = {
          parent_id: '',
        };
        this.getData();
      },
      handleCurrentChange(page) {
        this.currentPage = page;
        this.getData();
      },
      handleDel(id) {
        this.$confirm('确定删除该分类?', '提示', {
          type: 'warning',
        }).then(() => {
          this.$http.post(`${apiBase}category-del`, { category_id: id })
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
      handleUpdate(row) {
        this.form = row;
        this.form.parent_id = String(row.parent_id);
        if (this.form.parent_id === 0) {
          this.form.parent_id = '';
        }
        this.dialogFormVisible = true;
      },
      handleSubmitUpdate() {
        const params = {
          category_id: this.form.id,
          title: this.form.title,
          parent_id: this.form.parent_id,
          sort: this.form.sort,
          is_show: this.form.is_show,
          description: this.form.description,
        };
        this.$http.post(`${apiBase}category-update`, params)
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
          title: '',
          parent_id: '',
          sort: 0,
          description: '',
          is_show: 1,
        };
      },
      handleClose() {
        this.clearForm();
      },
      formatterIsShow(row) {
        switch (row.is_show) {
          case 0 : return '否';
          case 1 : return '是';
          default : return '';
        }
      },
      formatterLevel(row) {
        switch (row.parent_id) {
          case 0 : return '商户分类';
          default : return '商品分类';
        }
      },
      formatterParentName(row) {
        return this.parents.find(v => v.id === row.parent_id) ? this.parents.find(v => v.id === row.parent_id).title : '';
      },
    },
  };
</script>