<template>
    <div>
        <el-row>
            <el-col>
                <el-button type="success" @click.native="handleAddNav">新增导航</el-button>
                <el-button type="primary" @click.native="handleJumpBacK" v-show="current_pid !== 0">返回上级导航</el-button>
            </el-col>
        </el-row>
        <el-row style="font-size: 14px;">当前导航位置: / <span v-for="item in nav">{{ item }} / </span></el-row>
        <el-table :data="tableData" stripe border style="width: 100%;">
            <el-table-column type="expand">
                <template slot-scope="props">
                    <el-form label-position="left" inline class="demo-table-expand">
                        <el-form-item label="描述">
                            <span>{{ props.row.description }}</span>
                        </el-form-item>
                    </el-form>
                </template>
            </el-table-column>
            <el-table-column property="id" label="ID" align="center" min-width="60"></el-table-column>
            <el-table-column property="name" label="名称" align="center" min-width="100"></el-table-column>
            <el-table-column property="link" label="链接地址" align="center" width="150"></el-table-column>
            <el-table-column property="type" label="类型" align="center" width="150" :formatter="formatterType"></el-table-column>
            <el-table-column property="is_show" label="是否显示" align="center" min-width="80" :formatter="formatterIsShow"></el-table-column>
            <el-table-column property="is_open" label="是否打开新页面" align="center" min-width="80" :formatter="formatterIsOpen"></el-table-column>
            <el-table-column property="sort" label="排序" align="center" min-width="80"></el-table-column>
            <el-table-column label="设置" align="center" min-width="180">
                <template slot-scope="scope" v-if="level < 3">
                    <el-button-group>
                        <el-button type="primary" size="small" @click.native="handleAddChild(scope.row.id)">新增下级</el-button>
                        <el-button type="primary" size="small" @click.native="handleJump(scope.row.id, scope.row.name)">查看下级</el-button>
                    </el-button-group>
                </template>
            </el-table-column>
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
            <el-dialog  @close="handleClose" :visible.sync="dialogFormVisible">
                <el-form :model="form" ref="form" label-width="120px" label-position="left">
                    <el-form-item label="名称" prop="name" :rules="[{ required: true, message: '导航名称不能为空' }]">
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
                    <el-form-item label="上级名称" v-if="disableFlag">
                        <el-select v-model="form.pid" placeholder="请选择" style="width: 200px" :disabled="disableFlag" clearable>
                            <el-option v-for="(item,index) in tableData" :label="item.name" :value="item.id"></el-option>
                        </el-select>
                        <!--<span>置空默认为一级导航</span>-->
                    </el-form-item>
                    <el-form-item label="状态"  required>
                        <el-radio-group v-model="form.is_show">
                            <el-radio :label="1">显示</el-radio>
                            <el-radio :label="2">隐藏</el-radio>
                        </el-radio-group>
                    </el-form-item>
                    <el-form-item label="类型"  prop="type" :rules="[{ required: true, message: '类型不能为空'}]">
                        <el-select v-model="form.type" placeholder="请选择" style="width: 200px"clearable>
                            <el-option v-for="(item,index) in types" :label="item.name" :value="item.id"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="是否打开页面"  required>
                        <el-radio-group v-model="form.is_open">
                            <el-radio :label="1">是</el-radio>
                            <el-radio :label="2">否</el-radio>
                        </el-radio-group>
                    </el-form-item>
                    <el-form-item label="URL链接地址" prop="link">
                        <el-input v-model="form.link" placeholder="请输入" :maxlength="50" style="width:200px;"></el-input>
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
    </div>
</template>
<script>
  import apiBase from '../../apiBase';
  import Page from '../../components/Page';
  import { reportErrorMessage } from '../../ApiUtil';

  export default {
    name: 'navigation',
    data() {
      return {
        tableData: [],
        form: {
          id: '',
          name: '',
          pid: '',
          link: '',
          type: '',
          sort: 0,
          description: '',
          is_show: 1,
          is_open: 1,
        },
        current_pid: 0, // 当前pid
        last_pid: 0, // 上一级pid
        level: 1,
        nav: [],
        pageCount: 0,
        currentPage: 1,
        pageSize: 20,
        dialogFormVisible: false,
        disableFlag: false,
        types: [{ id: 1, name: '文本列表' }, { id: 2, name: '图片列表' }, { id: 3, name: '页面' }, { id: 4, name: '内容' }],
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
          pid: this.current_pid,
        };
        this.$http.get(`${apiBase}nav`, { params })
          .then(response => response.json())
          .then((json) => {
            if (json.status === 'SUCCESS') {
              this.tableData = json.results.data;
              this.last_pid = json.results.pid;
              this.pageCount = json.count;
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
        this.$confirm('确定删除该导航?', '提示', {
          type: 'warning',
        }).then(() => {
          this.$http.post(`${apiBase}nav-del`, { nav_id: id })
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
      handleSubmit() {
        const params = {
          name: this.form.name,
          pid: this.form.pid,
          link: this.form.link,
          type: this.form.type,
          sort: this.form.sort,
          is_show: this.form.is_show,
          is_open: this.form.is_open,
          description: this.form.description,
        };
        this.$http.post(`${apiBase}nav-add`, params)
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
        this.form = row;
        if (this.form.pid === 0) {
          this.form.pid = '';
        }
        this.dialogFormVisible = true;
      },
      handleSubmitUpdate() {
        const params = {
          id: this.form.id,
          name: this.form.name,
          pid: this.form.pid,
          link: this.form.link,
          type: this.form.type,
          sort: this.form.sort,
          is_show: this.form.is_show,
          is_open: this.form.is_open,
          description: this.form.description,
        };
        this.$http.put(`${apiBase}nav-update`, params)
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
      // 查看下级
      handleJump(v, name) {
        this.last_pid = this.current_pid;
        this.current_pid = v;
        this.level = this.level + 1;
        // console.log(this.level);
        this.nav.push(name);
        this.getData();
      },
      // 返回上一级
      handleJumpBacK() {
        this.current_pid = this.last_pid;
        this.level = this.level - 1;
        // console.log(this.level);
        if (this.nav) {
          this.nav.pop();
        }
        this.getData();
      },
      clearForm() {
        this.form = {
          id: '',
          name: '',
          pid: '',
          link: '',
          type: '',
          sort: 0,
          description: '',
          is_show: 1,
          is_open: 1,
        };
      },
      // 新增当前级导航
      handleAddNav() {
        this.dialogFormVisible = true;
        this.form.pid = this.current_pid;
      },
      handleClose() {
        this.clearForm();
        this.disableFlag = false;
      },
      handleAddChild(v) {
        this.form.pid = v;
        this.dialogFormVisible = true;
        this.disableFlag = true;
      },
      formatterIsShow(row) {
        switch (row.is_show) {
          case 1 : return '显示';
          case 2 : return '隐藏';
          default : return '';
        }
      },
      formatterIsOpen(row) {
        switch (row.is_open) {
          case 1 : return '是';
          case 2 : return '否';
          default : return '';
        }
      },
      formatterType(row) {
        switch (row.type) {
          case 1 : return '列表';
          case 2 : return '内容';
          default : return '';
        }
      },
    },
  };
</script>

<style>
.el-col-24 {
    padding: 8px;
}
</style>