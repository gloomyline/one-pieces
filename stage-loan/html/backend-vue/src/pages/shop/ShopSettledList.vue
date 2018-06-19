<template>
    <div>
        <el-row>
            <el-col :span="24">
                <el-form :inline="true">
                    <el-form-item>
                        <el-input v-model="form.shopName" placeholder="商户名称"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" @click.native="filter">查找</el-button>
                        <el-button type="primary" @click.native="clearFilter">清空筛选</el-button>
                    </el-form-item>
                </el-form>
            </el-col>
        </el-row>
        <el-table :data="tableData" stripe border style="width: 100%;">
            <el-table-column property="id" label="ID" align="center" min-width="100"></el-table-column>
            <el-table-column property="shop_name" label="商户名称" align="center" min-width="200"></el-table-column>
            <el-table-column property="contacts_name" label="联系人" align="center" min-width="120" ></el-table-column>
            <el-table-column property="contacts_mobile" label="联系手机号" align="center" min-width="150"></el-table-column>
            <el-table-column property="contacts_addr" label="联系地址" align="center" min-width="250"></el-table-column>
            <el-table-column property="created_at" label="提交时间" align="center" width="180"></el-table-column>
            <el-table-column label="操作" align="center" width="180" >
                <template slot-scope="scope">
                    <el-button-group style="">
                        <el-button type="danger" size="small"  @click.native="delItem(scope.row.id)">删 除</el-button>
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
          shopNmae: '',
        },
        delId: '',
        pageCount: 0,
        currentPage: 1,
        pageSize: 20,
        msg: '',
        dialogFormVisible: false,
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
        if (this.form.shopName !== '') {
          params.shop_name = this.form.shopName;
        }
        this.$http.get(`${apiBase}shop-settled`, { params })
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
          shopName: '',
        };
        this.getData();
      },
      delItem(id) {
        this.$confirm('确定删除该条记录?', '提示', {
          type: 'warning',
        }).then(() => {
          this.$http.put(`${apiBase}del-shop-settled/${id}`)
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
    },
    filters: {
    },
  };
</script>