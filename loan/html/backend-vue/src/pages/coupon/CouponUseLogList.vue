<template>
  <div>
    <el-row>
      <el-col :span="24">
        <el-form :inline="true">
          <el-form-item prop="name">
            <el-select v-model="form.name" placeholder="代金券名称" clearable>
              <el-option v-for="item in names" :label="item.name" :value="item.id"></el-option>
            </el-select>
          </el-form-item>
          <el-form-item prop="state">
            <el-select v-model="form.state" placeholder="状态" clearable>
              <el-option v-for="item in states" :label="item.name" :value="item.id">
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
      <el-table-column property="id" label="ID" align="center" min-width="70"></el-table-column>
	  <el-table-column property="mobile" label="用户名" align="center" min-width="130"></el-table-column>
      <el-table-column property="coupon_name" label="代金券名称" align="center" min-width="150">
        <template slot-scope="scope">
          <span>{{ scope.row.coupon_name | couponNameFilter }}</span>
        </template>
      </el-table-column>
      <el-table-column property="coupon_type" label="奖励类型" align="center" min-width="150">
        <template slot-scope="scope">
          <span>{{ scope.row.coupon_type | couponTypeFilter }}</span>
        </template>
      </el-table-column>
      <el-table-column property="coupon_amount" label="金额" align="center"></el-table-column>
      <el-table-column property="coupon_code" label="代金券编号" align="center" width="180"></el-table-column>
      <el-table-column property="state" label="代金券状态" align="center" :formatter="stateFormatter" min-width="100"></el-table-column>
      <el-table-column property="assign_at" label="赠送时间" align="center" width="180"></el-table-column>
	  <el-table-column property="end_at" label="截止时间" :render-header="renderHeader" align="center" :formatter="endAtFormatter" width="180"></el-table-column>
	  <el-table-column property="used_at" label="使用时间" align="center" :formatter="usedAtFormatter" width="180"></el-table-column>
	  <el-table-column property="validity_period" label="有效期(天)" align="center" min-width="80">
        <template slot-scope="scope">
          <span>{{ scope.row | validityPeriodFilter }}</span>
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
  name: 'coupon',
  data() {
    return {
      tableData: [],
      gridData: [],
      form: {
        state: '',
        name: '',
      },
      api: {
        staffs: [],
      },
      pageCount: 0,
      currentPage: 1,
      pageSize: 20,
      msg: '',
      showVal: '',
      states: [{ id: 0, name: '未使用' }, { id: 1, name: '已使用' }],
      names: [{ id: 1, name: '还款抵扣券' }, { id: 2, name: '现金奖励' }],
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
      if (this.form.name !== '') {
        params.coupon_name = this.form.name;
      }
      if (this.form.state !== '') {
        params.state = this.form.state;
      }
      this.$http.get(`${apiBase}coupon-log`, { params })
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
        name: '',
        state: '',
      };
      this.getData();
    },
    stateFormatter(row) {
      return this.states.find(v => v.id === row.state).name;
    },
    endAtFormatter(row) {
      return row.end_at === '' ? '--' : row.end_at;
    },
    usedAtFormatter(row) {
      return row.used_at === '' ? '--' : row.used_at;
    },
    renderHeader(createElement) {
      return createElement(
        'div', {
          class: 'renderTableHead',
        }, [
          createElement(
            'span', {
              attrs: {
                type: 'text',
              },
            }, [
              '截止时间',
            ]
          ),
          createElement(
            'el-button', {
              attrs: {
                type: 'text',
              },
              on: {
                click: this.showEndAtMsg,
              },
            }, [
              '?',
            ]
          ),
        ]
      );
    },
    showEndAtMsg() {
      this.$message('还款抵用券的截止时间为该券的使用截止时间，现金奖励的截止时间为活动的截止时间');
    },
  },
  filters: {
    validityPeriodFilter(row) {
      switch (row.is_expired) {
        case 0 :
          return '已过期';
        case 1 :
          return row.validity_period === 0 ? '--' : row.validity_period;
        default:
          return '无';
      }
    },
    couponNameFilter(val) {
      switch (val) {
        case 1 :
          return '还款抵扣券';
        case 2 :
          return '现金奖励';
        default:
          return '无';
      }
    },
    couponTypeFilter(val) {
      switch (val) {
        case 1 :
          return '注册成功';
        case 2 :
          return '邀请好友注册成功';
        case 3 :
          return '邀请好友借款成功';
        default:
          return '';
      }
    },
  },
};
</script>