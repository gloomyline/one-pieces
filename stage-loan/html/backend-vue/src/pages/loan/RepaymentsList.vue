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
                <el-select v-model="form.state" placeholder="还款状态" clearable>
                  <el-option
                    v-for="item in states"
                    :label="item.name"
                    :value="item.id">
                  </el-option>
                </el-select>
              </el-form-item>
              <el-form-item>
                <el-select v-model="form.type" placeholder="消费类型" clearable>
                  <el-option
                    v-for="item in type"
                    :label="item.name"
                    :value="item.id">
                  </el-option>
                </el-select>
              </el-form-item>
              <el-form-item label="实际还款时间">
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
      <el-table-column property="quota" label="借款金额（元）" align="center" min-width="100"></el-table-column>
      <el-table-column property="period" label="借款期数（月）" align="center" min-width="100"></el-table-column>
      <el-table-column property="lending_at" label="放款时间" align="center" min-width="170"></el-table-column>
      <el-table-column property="planned_repayment_at" label="计划还款时间" align="center" min-width="120"></el-table-column>
      <el-table-column property="repayment_at" label="实际还款时间" align="center" min-width="180"></el-table-column>
      <el-table-column property="need_amount" label="应还金额（元）" align="center" min-width="120"></el-table-column>
      <el-table-column property="repayed_amount" label="已还金额（元）" align="center" min-width="120"></el-table-column>
      <el-table-column property="state" label="借款状态" :formatter="stateFormatter" align="center" min-width="100" fixed="right"></el-table-column>
      <el-table-column label="操作" fixed="right" align="center" width="130">
        <template slot-scope="scope">
          <el-button-group>
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
  name: 'repayments',
  data() {
    return {
      form: {
        real_name: '',
        mobile: '',
        state: '',
        dateRange: {
          key: 'begin_at end_at',
          type: 'datePair',
        },
        type: '',
      },
      dialogForm: {

      },
      old_name: '',
      tableData: [],
      pageCount: 0,
      currentPage: 1,
      pageSize: 20,
      dialogFormVisible: false,
      states: [{ id: 'repaying', name: '还款中' }, { id: 'finished', name: '已还完' }],
      type: [{ id: 'all', name: '全部' }, { id: 1, name: '现金分期' }, { id: 2, name: '消费分期' }],
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
      if (this.form.type !== '') {
        params.type = this.form.type;
      }
      this.$http.get(`${apiBase}repayments`, { params })
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
        if (row.state === 'finished') {
          return 'successClass';
        }
        return 'defaultClass';
      }
      return '';
    },
  },
};
</script>
<style type="text/css">
  .el-table .el-table__row .defaultClass {
    color:#4F7DFF;
  }
  .el-table .el-table__row .failureClass {
    color:#FD971F;
  }
  .el-table .el-table__row .successClass {
    color:limegreen;
  }
  .el-table .el-table__row .overdueClass {
    color:red;
  }
</style>