<template>
  <div>
    <el-row>
          <el-col :span="24">
            <el-form :inline="true">
              <el-form-item>
                <el-input v-model="form.real_name" placeholder="真实姓名"></el-input>
              </el-form-item>
              <el-form-item>
                <el-input v-model="form.mobile" placeholder="手机号"></el-input>
              </el-form-item>
              <el-form-item>
                <el-select v-model="form.state" placeholder="状态" clearable :disabled="selectFlag">
                  <el-option
                    v-for="item in states"
                    :label="item.name"
                    :value="item.id">
                  </el-option>
                </el-select>
              </el-form-item>
              <el-form-item label="申请时间">
                <el-date-picker v-model="form.dateRange" type="daterange"></el-date-picker>
              </el-form-item>
              <el-form-item>
                <el-button type="primary" @click.native="filter">查找</el-button>
                <el-button type="primary" @click.native="clearFilter">清空筛选</el-button>
              </el-form-item>
            </el-form>
          </el-col>
        </el-row>
    <el-table :data="tableData" stripe border style="width: 100%;" :cell-class-name="stateCellClass">
      <el-table-column property="id" label="ID" align="center" min-width="80"></el-table-column>
      <el-table-column property="encoding" label="借款编号" align="center" min-width="235"></el-table-column>
      <el-table-column property="type"  :formatter="typeFormatter" label="消费类型" align="center" min-width="120"></el-table-column>
      <el-table-column property="shop_name" label="商户名称" align="center" min-width="235"></el-table-column>
      <el-table-column property="product_name" label="商品名称" align="center" min-width="235"></el-table-column>
      <el-table-column property="real_name" label="真实姓名" align="center" min-width="120"></el-table-column>
      <el-table-column property="mobile" label="手机号" align="center" min-width="150"></el-table-column>
      <el-table-column property="quota" label="借款金额（元）" align="center" min-width="120"></el-table-column>
      <el-table-column property="period" label="借款期数（月）" align="center" min-width="120"></el-table-column>
      <el-table-column property="created_at" label="申请时间" align="center" min-width="170"></el-table-column>
      <el-table-column property="state" label="借款状态" min-width="100" :formatter="stateFormatter" align="center"></el-table-column>
      <el-table-column property="preliminary_officer" label="初审人员" align="center" min-width="120"></el-table-column>
      <el-table-column label="操作" fixed="right" align="center" width="150">
        <template slot-scope="scope">
          <el-button-group>
            <el-button type="primary" size="small" @click.native="handleJump(scope.row.id, 'checks')"  v-if="scope.row.state === 'auditing'">初审</el-button>
            <el-button type="primary" size="small" @click.native="handleDetail(scope.row.id)">查看</el-button>
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
  name: 'checks',
  data() {
    return {
      state: this.$route.query.state,
      form: {
        real_name: '',
        mobile: '',
        state: '',
        dateRange: {
          key: 'begin_at end_at',
          type: 'datePair',
        },
      },
      dialogForm: {

      },
      old_name: '',
      tableData: [],
      pageCount: 0,
      currentPage: 1,
      pageSize: 20,
      selectFlag: false,
      dialogFormVisible: false,
      states: [{ id: 'all', name: '全部' }, { id: 'auditing', name: '待初审' }, { id: 'audit_failure', name: '初审失败' }],
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
      const params = this.getParams();
      params.limit = this.pageSize;
      params.offset = this.pageSize * (this.currentPage - 1);
      this.$http.get(`${apiBase}checks`, { params })
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
        real_name: '',
        mobile: '',
        state: '',
        dateRange: '',
      };
      this.getData();
    },
    stateFormatter(row) {
      return this.states.find(v => v.id === row.state).name;
    },
    getParams() {
      const params = {};
      if (this.form.dateRange && this.form.dateRange[0] && this.form.dateRange[1]) {
        params.start_at = Date.parse(this.form.dateRange[0]) / 1000;
        params.end_at = Date.parse(this.form.dateRange[1]) / 1000;
      }
      if (this.form.real_name !== '') {
        params.real_name = this.form.real_name;
      }
      if (this.form.mobile !== '') {
        params.mobile = this.form.mobile;
      }
      if (this.form.state !== '') {
        params.state = this.form.state;
      }
      if (this.state) {
        this.form.state = this.state;
        this.selectFlag = true;
        params.state = this.state;
      }
      return params;
    },
    handleJump(id, current) {
      if (current === 'checks') {
        this.$router.push({
          path: `/checks-audit/${id}`,
        });
      } else {
        this.$router.push({
          path: `/reviews-audit/${id}`,
        });
      }
    },
    handleDetail(id) {
      this.$router.push({
        path: `/loan-detail/${id}`,
      });
    },
    typeFormatter(row) {
      switch (row.type) {
        case 1 : return '现金分期';
        case 2 : return '消费分期';
        default : return '其他方式';
      }
    },
    stateCellClass({ row, column }) {
      if (column.property === 'state') {
        if (row.state === 'audit_failure') {
          return 'failureClass';
        }
        return 'defaultClass';
      }
      return '';
    },
  },
};
</script>