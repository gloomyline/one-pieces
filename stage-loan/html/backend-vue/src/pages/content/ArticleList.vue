<template>
  <div>
    <el-row>
      <el-col :span="24">
        <el-form :inline="true">
          <el-form-item>
            <el-input v-model="form.title" placeholder="文章标题"></el-input>
          </el-form-item>
          <el-form-item>
            <el-select v-model="form.type" placeholder="分类" clearable>
              <el-option 
                v-for="item in types"
                :label="item.name" 
                :value="item.id">
              </el-option>
            </el-select>
          </el-form-item>
          <el-form-item>
            <el-button type="primary" @click.native="filter">查找</el-button>
            <el-button type="primary" @click.native="clearFilter">清空筛选</el-button>
            <el-button type="success" @click.native="handleAdd">添加文章</el-button>
          </el-form-item>
        </el-form>
      </el-col>
    </el-row>
    <el-table :data="tableData" stripe border style="width: 100%;">
      <el-table-column property="id" label="ID" align="center" width="180"></el-table-column>
      <el-table-column property="title" label="文章标题" align="center"></el-table-column>
      <el-table-column property="type" label="类型" align="center" width="200" :formatter="typeFormatter"></el-table-column>
      <el-table-column property="state" label="状态" align="center" width="180" :formatter="stateFormatter"></el-table-column>
      <el-table-column property="created_at" label="添加时间" align="center" width="180"></el-table-column>
      <el-table-column label="操作" align="center" width="180" >
        <template slot-scope="scope">
          <el-button-group style="">
            <el-button type="danger" size="small"  @click.native="delItem(scope.row.id)">删 除</el-button>
            <el-button type="primary" size="small"  @click.native="updateItem(scope.row.id)">编 辑</el-button>
          </el-button-group>
        </template>
      </el-table-column>
    </el-table>
    <page :total="pageCount" :current-page="currentPage" :page-size="pageSize" @current-change="handleCurrentChange"></page>

  </div>
</template>
<script>
import apiBase from '../../apiBase';
import Page from '../../components/Page';
import { reportErrorMessage } from '../../ApiUtil';

export default {
  name: 'admins',
  data() {
    return {
      tableData: [],
      form: {
        title: '',
        type: '',
      },
      pageCount: 0,
      currentPage: 1,
      pageSize: 20,
      types: [{ id: 'activity', name: '活动中心' }, { id: 'problem', name: '常见问题' }],
      states: [{ id: 1, name: '显示' }, { id: 2, name: '隐藏' }],
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
      if (this.form.title !== '') {
        params.title = this.form.title;
      }
      if (this.form.type !== '') {
        params.type = this.form.type;
      }
      this.$http.get(`${apiBase}article`, { params })
      .then(response => response.json())
      .then((json) => {
        if (json.status === 'SUCCESS') {
          this.tableData = json.results;
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
    handleAdd() {
      this.$router.push({
        path: '/article-add/',
      });
    },
    filter() {
      this.currentPage = 1;
      this.getData();
    },
    clearFilter() {
      this.form = {
        title: '',
        type: '',
      };
      this.getData();
    },
    stateFormatter(row) {
      return row.state === 1 ? '显示' : '隐藏';
    },
    typeFormatter(row) {
      return this.types.find(v => v.id === row.type).name;
    },
    delItem(id) {
      this.$confirm('确定删除该文章?', '提示', {
        type: 'warning',
      }).then(() => {
        this.$http.post(`${apiBase}del-article`, { article_id: id })
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
    updateItem(id) {
      this.$router.push({
        path: `/article-update/${id}`,
      });
    },
  },
};
</script>