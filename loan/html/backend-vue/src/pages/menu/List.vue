<template>
  <div>
    <el-row>
      <el-col :span="24">
        <el-form :inline="true">
          <el-form-item>
            <el-input v-model="form.title" placeholder="菜单名称"></el-input>
          </el-form-item>
          <el-form-item>
            <el-input v-model="form.module" placeholder="模块名称"></el-input>
          </el-form-item>
          <el-form-item>
            <el-select v-model="form.is_top" placeholder="是否顶级菜单" clearable>
              <el-option 
                v-for="item in states" 
                :label="item.name" 
                :value="item.id">
              </el-option>
            </el-select>
          </el-form-item>
          <el-form-item>
            <el-button type="primary" @click.native="filter">查找</el-button>
            <el-button type="primary" @click.native="clearFilter">清空筛选</el-button>
            <el-button type="primary" @click.native="handleAdd()">添加</el-button>
          </el-form-item>
        </el-form>
      </el-col>
    </el-row>
    <el-table :data="tableData" stripe border style="width: 100%;">
      <el-table-column property="id" label="ID" align="center" width="60"></el-table-column>
      <el-table-column property="parent_id" label="顶级菜单" align="center" width="100" inline-template>
        <span v-if="row.parent_id === 0">是</span>
        <span v-else>否</span>
      </el-table-column>
      <el-table-column property="module" label="模块名称" align="center" width="160"></el-table-column>
      <el-table-column property="route" label="路由" align="center"></el-table-column>
      <el-table-column property="title" label="菜单名称" align="center"></el-table-column>
      <el-table-column property="created_at" label="创建时间" align="center"></el-table-column>
      <el-table-column label="操作" align="center" width="100" inline-template>
        <el-button-group>
          <el-button type="primary" size="small" @click.native="handleJump(row.id)">编辑</el-button>
        </el-button-group>
      </el-table-column>

      </el-table-column>
    </el-table>
    <page :total="pageCount" :current-page="currentPage" :page-size="pageSize" @current-change="handleCurrentChange"></page>
  </div>
</template>
<script>
import apiBase from '../../apiBase';
import Page from '../../components/Page';

export default {
  name: 'roles',
  data() {
    return {
      form: {
        title: '',
        module: '',
        is_top: '',
      },
      tableData: [],
      pageCount: 0,
      currentPage: 1,
      pageSize: 20,
      states: [{ id: 1, name: '是' }, { id: 2, name: '否' }],
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
      if (this.form.module !== '') {
        params.module = this.form.module;
      }
      if (this.form.is_top !== '') {
        params.is_top = this.form.is_top;
      }
      this.$http.get(`${apiBase}menus`, { params })
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
    filter() {
      this.currentPage = 1;
      this.getData();
    },
    clearFilter() {
      this.form = {
        username: '',
        real_name: '',
      };
      this.getData();
    },
    handleAdd() {
      this.$router.push({
        path: '/add-menu',
      });
    },
    handleJump(id) {
      this.$router.push({
        path: `/update-menu/${id}`,
      });
    },
  },
};
</script>