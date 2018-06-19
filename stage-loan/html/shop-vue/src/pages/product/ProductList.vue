<template>
    <div>
        <el-row>
            <el-col :span="24">
                <div style="margin: 20px 0">
                    <el-button :type="activeForm.all ? 'primary' : ''" @click.native="clearActiveForm(); activeForm.all = true; cleanActive()" round>全部商品（{{ countList.allCount }}）</el-button>
                    <el-button :type="activeForm.isOnSale ? 'primary' : ''" @click.native="clearActiveForm(); activeForm.isOnSale = true; cleanActive()" round>已上架（{{ countList.onSaleCount }}）</el-button>
                    <el-button :type="activeForm.noOnSale ? 'primary' : ''" @click.native="clearActiveForm(); activeForm.noOnSale = true; cleanActive()" round>未上架（{{ countList.allCount - countList.onSaleCount}}）</el-button>
                    <el-button :type="activeForm.wait ? 'primary' : ''" @click.native="clearActiveForm(); activeForm.wait = true; cleanActive()" round>待审核（{{ countList.waitCount }}）</el-button>
                    <el-button :type="activeForm.noPass ? 'primary' : ''" @click.native="clearActiveForm(); activeForm.noPass = true; cleanActive()" round>未通过（{{ countList.noPassCount }}）</el-button>
                </div>
            </el-col>
        </el-row>
        <el-row>
            <el-col :span="24">
                <el-form :inline="true">
                    <el-form-item>
                        <el-input v-model="form.title" placeholder="商品名称"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-input v-model="form.no" placeholder="商品货号"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-select v-model="form.category" placeholder="商品分类" clearable>
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
                    <el-form-item label="创建时间范围:">
                        <el-date-picker v-model="form.dateRange" type="daterange" align="right" ></el-date-picker>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" @click.native="filter">查找</el-button>
                        <el-button type="primary" @click.native="clearFilter">清空筛选</el-button>
                        <el-button type="success" @click.native="handleJumpAdd">商品添加</el-button>
                    </el-form-item>
                </el-form>
            </el-col>
        </el-row>
        <el-table :data="tableData" stripe border style="width: 100%;">
            <el-table-column property="id" label="ID" align="center" width="80"></el-table-column>
            <el-table-column property="title" label="商品名称" align="center" width="150"></el-table-column>
            <el-table-column property="no" label="商品货号" align="center" width="120"></el-table-column>
            <el-table-column property="category" label="类别" align="center" width="120"></el-table-column>
            <el-table-column property="sort" label="排序" align="center" width="100"></el-table-column>
            <el-table-column property="total_stock" label="库存" align="center" width="100"></el-table-column>
            <el-table-column property="sale" label="销量" align="center" width="100"></el-table-column>
            <el-table-column property="created_at" label="创建时间" align="center" width="180"></el-table-column>
            <el-table-column property="on_sale" label="上/下架" align="center" width="130">
                <template slot-scope="scope">
                    <el-button-group style="" align="center">
                        <el-tooltip class="item" effect="dark" :content="scope.row.on_sale === 1 ? '点击下架商品' : '点击上架商品'" placement="right">
                            <el-button :type="scope.row.on_sale === 1 ? 'success' : 'info'"
                                       size="small"
                                       @click.native="handleOnSale(scope.row.id)"
                                       v-text="scope.row.on_sale === 1 ? '上架' : '下架'"
                                       :disabled="scope.row.state !== 1">
                            </el-button>
                        </el-tooltip>
                    </el-button-group>
                </template>
            </el-table-column>

            <el-table-column property="state" label="审核状态" align="center" width="130">
                <template slot-scope="scope">
                    <span :class="{ 'fill-out-green' : scope.row.state === 1, 'unfilled-red' : scope.row.state === 2, 'wait-blue' : scope.row.state === 0 }" v-text="stateFormatter(scope.row.state)"></span>
                </template>
            </el-table-column>
            <el-table-column property="audited_at" label="审核时间" align="center" width="180" ></el-table-column>
            <el-table-column label="操作" align="center" min-width="220">
                <template slot-scope="scope">
                    <el-button-group style="">
                        <el-button type="primary" size="small"  @click.native="handleJumpUpdate(scope.row.id)" :disabled="scope.row.state === 2">编辑</el-button>
                        <!--<el-button type="danger" size="small" @click.native="handleAudit(scope.row.id)" :disabled="scope.row.state !== 0" >审核</el-button>-->
                        <el-button type="danger" size="small" @click.native="handleAudit(scope.row.id)" :disabled="scope.row.state !== 0">审核</el-button>
                        <el-button type="success" size="small" @click.native="handleDetail(scope.row.id)" >查看</el-button>
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
  import { getJsonAndCheckSuccess, reportErrorMessage } from '../../ApiUtil';

  export default {
    name: 'productList',
    data() {
      return {
        tableData: [],
        category: [],
        activeForm: {
          all: true,
          isOnSale: false,
          noOnSale: false,
          wait: false,
          noPass: false,
        },
        form: {
          title: '',
          no: '',
          category: '',
          dateRange: {
            key: 'begin_at end_at',
            type: 'datePair',
          },
        },
        countList: {
          allCount: 0,
          onSaleCount: 0,
          waitCount: 0,
          noPassCount: 0,
        },
        pageCount: 0,
        currentPage: 1,
        pageSize: 20,
        msg: '',
        dialogAddShopAdminFormVisible: false,
        dialogUpdateShopAdminFormVisible: false,
        states: [{ id: 0, name: '待审核' }, { id: 1, name: '通过' }, { id: 2, name: '未通过' }],
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
      this.getCategory();
    },
    methods: {
      getCategory() {
        this.$http.get(`${apiBase}need`)
          .then(getJsonAndCheckSuccess)
          .then((response) => {
            this.category = response.result;
          }).catch(reportErrorMessage(this));
      },
      // 清空按钮 表单
      cleanActive() {
        this.clearFilter();
      },
      getData() {
        const params = {
          limit: this.pageSize,
          offset: this.pageSize * (this.currentPage - 1),
        };
        if (this.activeForm.noPass) {
          params.state = 2;
        }
        if (this.activeForm.wait) {
          params.state = 0;
        }
        if (this.activeForm.isOnSale) {
          params.on_sale = 1;
        }
        if (this.activeForm.noOnSale) {
          params.on_sale = 0;
        }
        if (this.form.title !== '') {
          params.title = this.form.title;
        }
        if (this.form.no !== '') {
          params.no = this.form.no;
        }
        if (this.form.category) {
          params.category = this.form.category;
        }
        if (this.form.dateRange && this.form.dateRange[0] && this.form.dateRange[1]) {
          params.start_at = Date.parse(this.form.dateRange[0]) / 1000;
          params.end_at = Date.parse(this.form.dateRange[1]) / 1000;
        }
        this.$http.get(`${apiBase}pro-list`, { params })
          .then(response => response.json())
          .then((json) => {
            if (json.status === 'SUCCESS') {
              this.tableData = json.results;
              this.pageCount = json.count;
              this.countList = json.countList;
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
          title: '',
          no: '',
          category: '',
          dateRange: {
            key: 'begin_at end_at',
            type: 'datePair',
          },
        };
        this.getData();
      },
      clearActiveForm() {
        this.activeForm = {
          all: false,
          isOnSale: false,
          noOnSale: false,
          wait: false,
          noPass: false,
        };
      },
      stateFormatter(v) {
        switch (v) {
          case 0: return '待审核';
          case 1: return '通过';
          case 2: return '未通过';
          default: return '';
        }
      },
      handleJumpAdd() {
        this.$router.push({
          path: '/pro-add',
        });
      },
      handleAudit(id) {
        this.$router.push({
          path: `/pro-audit/${id}`,
        });
      },
      handleDetail(id) {
        this.$router.push({
          path: `/pro-detail/${id}`,
        });
      },
      handleJumpUpdate(id) {
        this.$router.push({
          path: `/pro-update/${id}`,
        });
      },
      handleOnSale(id) {
        this.$http.put(`${apiBase}pro-on-sale/${id}`)
          .then(response => response.json())
          .then((json) => {
            if (json.status !== 'SUCCESS') {
              return Promise.reject(json.error_message);
            }
            this.$message({
              type: 'success',
              message: '操作成功！',
            });
            this.getData();
            return {};
          }).catch(reportErrorMessage(this));
      },
    },
  };
</script>
<style>
    .fill-out-green{
        color:green
    }
    .unfilled-red{
        color: red;
    }
    .wait-blue{
        color: blue;
    }
</style>