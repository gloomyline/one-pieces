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
          <el-form-item prop="staff">
            <el-select v-model="form.staff" placeholder="催收员" clearable>
              <el-option-group
                      v-for="group in options"
                      :key="group.label"
                      :label="group.label">
                <el-option
                        v-for="item in group.options"
                        :key="item.value"
                        :label="item.admin_name"
                        :value="item.admin_id">
                </el-option>
              </el-option-group>
            </el-select>
          </el-form-item>
          <el-form-item label="计划还款时间">
            <el-date-picker v-model="form.dateRange" type="daterange"></el-date-picker>
          </el-form-item>
          <el-form-item>
            <el-button type="primary" @click.native="filter">查找</el-button>
            <el-button type="primary" @click.native="clearFilter">清空筛选</el-button>
            <el-button type="success" @click.native="$router.back(-1)">返回上一页</el-button>
          </el-form-item>
        </el-form>
      </el-col>
    </el-row>
    <el-table :data="tableData" stripe border style="width: 100%;">
      <el-table-column type="expand">
        <template slot-scope="props">
          <el-form label-position="left" inline class="demo-table-expand">
            <el-form-item label="订单编号">
              <span>{{ props.row.encoding }}</span>
            </el-form-item>
            <el-form-item label="借款类型">
              <span>{{ typeFormatter(props.row.type) }}</span>
            </el-form-item>
            <el-form-item label="商户名称">
              <span>{{ props.row.shop_name }}</span>
            </el-form-item>
            <el-form-item label="商品名称">
              <span>{{ props.row.product_name }}</span>
            </el-form-item>
            <el-form-item label="借款金额(元)">
              <span>{{ props.row.arrival_amount }}</span>
            </el-form-item>
            <el-form-item label="本期应还(元)">
              <span>{{ props.row.monthly }}</span>
            </el-form-item>
            <el-form-item label="本期实际应还(元)">
              <span>{{ props.row.total_repayment_amount }}</span>
            </el-form-item>
            <el-form-item label="本期实际应还(元)">
              <span>{{ props.row.total_repayment_amount }}<em>(本期实际应还+逾期费用)</em></span>
            </el-form-item>
          </el-form>
        </template>
      </el-table-column>
      <el-table-column property="id" label="ID" align="center" width="70"></el-table-column>
	  <!--<el-table-column property="encoding" label="订单编号" align="center" min-width="220"></el-table-column>-->
      <el-table-column property="real_name" label="真实姓名" align="center" min-width="120"></el-table-column>
      <el-table-column property="mobile" label="手机号" align="center" width="150"></el-table-column>
      <el-table-column property="principal" label="本期逾期本金（元）" align="center" min-width="150"></el-table-column>
      <el-table-column property="period" label="期数(月)" align="center" min-width="80"></el-table-column>
      <el-table-column property="lending_at" label="放款时间" align="center"  width="180"></el-table-column>
      <el-table-column property="planned_repayment_at" label="计划还款时间" align="center" min-width="120"></el-table-column>
	  <el-table-column property="overdue_amount" label="逾期费用" align="center" min-width="100"></el-table-column>
	  <el-table-column property="overdue_days" label="逾期天数" align="center" min-width="100">
        <template slot-scope="scope">
          <span>{{ scope.row.overdue_days }}天</span>
        </template>
      </el-table-column>
	  <el-table-column property="admin_name" label="催收人员" align="center" min-width="120"></el-table-column>
	  <el-table-column property="created_at" label="分配时间" align="center" min-width="180"></el-table-column>
      <el-table-column property="state" label="催收结果" align="center" :formatter="stateFormatter" min-width="100"></el-table-column>
      <el-table-column label="操作" align="center" width="100">
        <template slot-scope="scope">
          <el-button-group style="">
            <el-button type="primary" size="small"  @click.native="showDialog(scope.row.id)">查看</el-button>
          </el-button-group>
        </template>
      </el-table-column>
    </el-table>
    <page :total="pageCount" :current-page="currentPage" :page-size="pageSize" @current-change="handleCurrentChange"></page>
    <div>
      <el-dialog title="催收记录" :visible.sync="dialogTableVisible">
        <div>共催收<span style="color: #00a65a">{{ urge_count }}</span>次</div>
        <el-table :data="gridData">
          <el-table-column
                  type="index"
                  width="50">
          </el-table-column>
          <el-table-column property="created_at" label="催收日期" width="180"></el-table-column>
          <el-table-column property="urge_way" label="催收方式" width="100">
            <template slot-scope="scope">
              <span>{{ scope.row.urge_way | urgeWayFilter }}</span>
            </template>
          </el-table-column>
          <el-table-column property="urge_result" label="催收结果" width="130">
            <template slot-scope="scope">
              <span>{{ scope.row.urge_result | urgeResultFilter }}</span>
            </template>
          </el-table-column>
          <el-table-column property="planned_repayment_at" label="预计还款时间" width="150" ></el-table-column>
          <el-table-column property="content" label="催收情况说明"></el-table-column>
        </el-table>
        <span slot="footer" class="dialog-footer">
        <el-button @click.native="dialogTableVisible = false">关 闭</el-button>
        </span>
        <p style="margin-left:50px; line-height: 25px;" v-html="msg"></p>
      </el-dialog>
    </div>
    <el-dialog
            title="提示"
            :visible.sync="dialogVisible">
      <span>暂无记录</span>
      <span slot="footer" class="dialog-footer">
        <el-button @click="dialogVisible = false">取 消</el-button>
        <el-button type="primary" @click="dialogVisible = false">确 定</el-button>
     </span>
    </el-dialog>
  </div>
</template>
<script>
import apiBase from '../../apiBase';
import Page from '../../components/Page';
import { getJsonAndCheckSuccess, reportErrorMessage } from '../../ApiUtil';

export default {
  name: 'overdue',
  data() {
    return {
      tableData: [],
      gridData: [],
      form: {
        mobile: '',
        real_name: '',
        staff: '',
        dateRange: {
          key: 'begin_at end_at',
          type: 'datePair',
        },
      },
      urge_count: '',
      options: [],
      pageCount: 0,
      currentPage: 1,
      pageSize: 20,
      msg: '',
      dialogTableVisible: false,
      dialogVisible: false,
      states: [{ id: 1, name: '等待催收' }, { id: 2, name: '催收未还款' }, { id: 3, name: '已还款' }, { id: 4, name: '坏账' }],
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
      if (this.form.mobile !== '') {
        params.mobile = this.form.mobile;
      }
      if (this.form.staff !== '') {
        params.staff = this.form.staff;
      }
      if (this.form.real_name !== '') {
        params.real_name = this.form.real_name;
      }
      this.$http.get(`${apiBase}urge-lists`, { params })
      .then(response => response.json())
      .then((json) => {
        if (json.status === 'SUCCESS') {
          this.tableData = json.results;
          this.pageCount = json.count;
        }
      }).catch(() => {
        this.$message('系统错误');
      });
      this.$http.get(`${apiBase}staffs`)
      .then(getJsonAndCheckSuccess)
      .then((response) => {
        this.options = response.results;
      }).catch(reportErrorMessage(this));
    },
    handleCurrentChange(page) {
      this.currentPage = page;
      this.getData();
    },
    showDialog(id) {
      this.$http.get(`${apiBase}urge-log/${id}`)
      .then(response => response.json())
      .then((json) => {
        if (json.status === 'SUCCESS') {
          this.dialogTableVisible = true;
          this.gridData = json.results;
          this.urge_count = json.count;
        } else {
          this.dialogVisible = true;
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
      this.form = {
        mobile: '',
        real_name: '',
        dateRange: '',
      };
      this.getData();
    },
    stateFormatter(row) {
      return this.states.find(v => v.id === row.state).name;
    },
    typeFormatter(v) {
      switch (v) {
        case 1: return '现金分期';
        case 2: return '消费分期';
        default : return '';
      }
    },
  },
  filters: {
    urgeWayFilter(val) { // 催收方式
      switch (val) {
        case 1 :
          return '短信';
        case 2 :
          return '电话';
        case 3 :
          return '上门';
        case 4 :
          return '第三方';
        default:
          return '无';
      }
    },
    urgeResultFilter(val) {
      switch (val) {
        case 1 :
          return '客户承诺还款';
        case 2 :
          return '客户无法还款';
        case 3 :
          return '催款成功';
        case 4 :
          return '客户失联';
        case 5 :
          return '坏账';
        default:
          return '';
      }
    },
  },
};
</script>
<style>
  .demo-table-expand {
    font-size: 0;
  }
  .demo-table-expand label {
    width: 200px;
    color: #99a9bf;
  }
  .demo-table-expand .el-form-item {
    margin-right: 0;
    margin-bottom: 0;
    width: 50%;
  }
</style>