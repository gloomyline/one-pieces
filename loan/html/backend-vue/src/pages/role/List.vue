<template>
  <div>
    <el-row>
      <el-col :span="24">
        <el-form :inline="true">
          <el-form-item>
            <el-button type="primary" @click.native="handleAdd()">添加</el-button>
          </el-form-item>
        </el-form>
      </el-col>
    </el-row>
    <el-table :data="tableData" stripe border style="width: 100%;">
      <el-table-column property="name" label="角色名称" align="center"></el-table-column>
      <el-table-column property="description" label="描述" align="center"></el-table-column>
      <el-table-column label="操作" align="center">
        <template slot-scope="scope">
          <el-button-group>
            <el-button type="primary" size="small" @click.native="handleJump(scope.row.name)">编辑</el-button>
            <el-button type="primary" size="small" @click.native="handleAssign(scope.row.name)">权限分配</el-button>
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

export default {
  name: 'roles',
  data() {
    return {
      tableData: [],
      pageCount: 0,
      currentPage: 1,
      pageSize: 20,
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
      this.$http.get(`${apiBase}roles`)
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
        path: '/add-role',
      });
    },
    handleJump(name) {
      this.$router.push({
        path: `/update-role/${name}`,
      });
    },
    handleAssign(name) {
      this.$router.push({
        path: `/assign-auth/${name}`,
      });
    },
  },
};
</script>