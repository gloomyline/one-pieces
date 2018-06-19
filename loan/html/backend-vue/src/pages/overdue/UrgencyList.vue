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
            <el-select v-model="form.state" placeholder="催收结果" clearable>
              <el-option
                      v-for="item in states"
                      :label="item.name"
                      :value="item.id">
              </el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="计划还款时间">
            <el-date-picker v-model="form.dateRange" type="daterange" align="right" ></el-date-picker>
          </el-form-item>
          <el-form-item>
            <el-button type="primary" @click.native="filter">查找</el-button>
            <el-button type="primary" @click.native="clearFilter">清空筛选</el-button>
          </el-form-item>
        </el-form>
      </el-col>
    </el-row>
    <el-table :data="tableData" stripe border style="width: 100%;">
      <el-table-column property="id" label="ID" align="center" width="70"></el-table-column>
	  <el-table-column property="encoding" label="订单编号" align="center" min-width="150"></el-table-column>
      <el-table-column property="real_name" label="真实姓名" align="center" min-width="120"></el-table-column>
      <el-table-column property="mobile" label="手机号" align="center" min-width="150"></el-table-column>
      <el-table-column property="arrival_amount" label="借款金额" align="center" min-width="100"></el-table-column>
      <el-table-column property="period" label="借款期限" align="center" min-width="80">
        <template slot-scope="scope">
          <span>{{ scope.row.period }}天</span>
        </template>
      </el-table-column>
      <el-table-column property="lending_at" label="放款时间" align="center"  width="180"></el-table-column>
      <el-table-column property="planned_repayment_at" label="计划还款时间" align="center" min-width="120"></el-table-column>
	  <el-table-column property="overdue_amount" label="逾期费用" align="center" min-width="100"></el-table-column>
	  <el-table-column property="overdue_days" label="逾期天数" align="center" min-width="90">
        <template slot-scope="scope">
          <span>{{ scope.row.overdue_days }}天</span>
        </template>
      </el-table-column>
	  <el-table-column property="created_at" label="分配时间" align="center" width="120"></el-table-column>
      <el-table-column property="state" label="催收结果" align="center" :formatter="stateFormatter" min-width="100"></el-table-column>
      <el-table-column label="操作" align="center" width="150">
        <template slot-scope="scope">
          <el-button-group style="">
            <el-button type="primary" size="small"  @click.native="showFormDialog(scope.row.id, scope.row.urge_count, scope.row.loan_id)" v-if="scope.row.state != 4 && scope.row.state != 3">催收</el-button>
            <el-button type="primary" size="small"  @click.native=""  v-if="scope.row.state == 4 || scope.row.state == 3" :disabled="true">催收</el-button>
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
            :visible.sync="dialogVisible"
            :before-close="handleClose">
      <span>暂无记录</span>
      <span slot="footer" class="dialog-footer">
        <el-button @click="dialogVisible = false">取 消</el-button>
        <el-button type="primary" @click="dialogVisible = false">确 定</el-button>
     </span>
    </el-dialog>

    <el-dialog title="催收" :visible.sync="dialogUrgeFormVisible">
      <el-form :model="urgeForm" labelWidth="150px" labelPosition="left">
        <div style="font-size: 15px;color: rgb(72, 87, 106)" class="label">第 <span style="color: #00a65a">{{ urge_count }}</span> 次催收</div>
        <el-form-item label="催收方式" :label-width="formLabelWidth" required>
          <el-radio-group v-model="urgeForm.urge_way">
            <el-radio :label="1">短信</el-radio>
            <el-radio :label="2">电话</el-radio>
            <el-radio :label="3">上门</el-radio>
            <el-radio :label="4">第三方</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="催收结果" :label-width="formLabelWidth" required>
          <el-radio-group v-model="urgeForm.urge_result">
            <el-radio :label="1">客户承诺还款</el-radio>
            <el-radio :label="2">客户无法还款</el-radio>
            <el-radio :label="3">催款成功</el-radio>
            <el-radio :label="4">客户失联</el-radio>
            <el-radio :label="5">坏账</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="预计还款日期" :label-width="formLabelWidth">
          <div class="block">
            <el-date-picker
                    v-model="urgeForm.planned_repayment_at"
                    type="date"
                    placeholder="选择日期">
            </el-date-picker>
          </div>
        </el-form-item>
        <el-form-item label="通话记录前十联系人" :label-width="formLabelWidth">
          <el-popover
                  ref="popover"
                  placement="right"
                  trigger="click">

            <el-table ref="multipleTable" :data="callRecords" tooltip-effect="dark" style="width: 100%" @selection-change="handleSelectionChange">
              <el-table-column type="selection" width="55"></el-table-column>
              <el-table-column property="mobileNo" label="与本机通话号码" min-width="150" align="center"></el-table-column>
              <el-table-column property="callCount" label="与本机通话次数" min-width="160" align="center"></el-table-column>
              <el-table-column property="send_state" label="发送状态" min-width="100" align="center">
                <template slot-scope="scope">
                  <span>{{ scope.row.send_state | sendStateFilter }}</span>
                </template>
              </el-table-column>
            </el-table>
            <div style="margin-top: 5px">短信类型： 逾期群发通知</div>
            <div>短信内容： 【悟空贷】【#name#】于【#date#】申请的【#account#】元，已过期【#day#】天，请转告其尽快通过平台进行操作处理。</div>
            <div style="margin-top: 20px">
              <el-button type="success" @click="handleSendSMS">确认发送短信</el-button>
              <el-button @click="toggleSelection();">取消选择</el-button>
            </div>
          </el-popover>

          <el-button v-popover:popover @click.native="handleShowCallRecords">查 看</el-button>
        </el-form-item>
        <el-form-item label="催款说明" :label-width="formLabelWidth">
          <el-input
                  type="textarea"
                  :rows="4"
                  placeholder="请输入内容"
                  v-model="urgeForm.content">
          </el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogUrgeFormVisible = false">取 消</el-button>
        <el-button type="primary" @click="handleUrge">提 交</el-button>
      </div>
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
      callRecords: [],
      form: {
        mobile: '',
        real_name: '',
        state: '',
        dateRange: {
          key: 'begin_at end_at',
          type: 'datePair',
        },
      },
      api: {
        staffs: [],
      },
      urgeForm: {
        urge_id: '',
        urge_way: '',
        urge_result: '',
        planned_repayment_at: '',
        content: '',
      },
      loan_id: 0,
      urge_count: 0,
      pageCount: 0,
      currentPage: 1,
      pageSize: 20,
      msg: '',
      dialogVisible: false,
      dialogUrgeFormVisible: false,
      dialogTableVisible: false,
      multipleSelection: [],
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
      if (this.form.state !== '') {
        params.state = this.form.state;
      }
      if (this.form.real_name !== '') {
        params.real_name = this.form.real_name;
      }
      this.$http.get(`${apiBase}urgency`, { params })
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
    showFormDialog(id, count, loanId) {
      this.urgeForm = {
        urge_id: '',
        urge_way: '',
        urge_result: '',
        planned_repayment_at: '',
        content: '',
      };
      this.loan_id = loanId;
      this.urgeForm.urge_id = id;
      this.urge_count = Number(count) + 1;
      this.dialogUrgeFormVisible = true;
    },
    handleShowCallRecords() {
      this.$http.get(`${apiBase}call-records/${this.loan_id}`)
        .then(getJsonAndCheckSuccess)
        .then((response) => {
          this.callRecords = response.results;
        }).catch(reportErrorMessage(this));
    },
    handleSendSMS() { // 群发催收短息
      if (this.multipleSelection.length === 0) {
        this.$message.error('您没有勾选任何需要发送短信的手机号码！');
        return;
      }
      const params = {
        mobiles: this.multipleSelection,
        loan_id: this.loan_id,
      };
      this.$http.post(`${apiBase}overdue-mass`, params)
        .then(response => response.json())
        .then((json) => {
          if (json.status !== 'SUCCESS') {
            return Promise.reject(json.error_message);
          }
          this.$message({
            type: 'success',
            message: '发送成功！',
          });
          this.handleShowCallRecords();
          return {};
        }).catch(reportErrorMessage(this));
    },
    handleUrge() {
      const params = {
        urge_id: this.urgeForm.urge_id,
        urge_way: this.urgeForm.urge_way,
        urge_result: this.urgeForm.urge_result,
        content: this.urgeForm.content,
      };
      if (this.urgeForm.planned_repayment_at) {
        params.planned_repayment_at = Date.parse(this.urgeForm.planned_repayment_at) / 1000;
      }
      this.$http.post(`${apiBase}urge`, params)
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
        this.dialogUrgeFormVisible = false;
        return {};
      }).catch(reportErrorMessage(this));
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
    toggleSelection(rows) {
      if (rows) {
        rows.forEach(row =>
          this.$refs.multipleTable.toggleRowSelection(row)
        );
      } else {
        this.$refs.multipleTable.clearSelection();
      }
    },
    handleSelectionChange(val) {
      console.log(val);
      this.multipleSelection = [];
      val.forEach((v, k) => {
        this.multipleSelection[k] = v.mobileNo;
      });
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
    sendStateFilter(v) {
      switch (v) {
        case 1: return '发送成功';
        case 2: return '发送失败';
        case 0: return '--------';
        default: return '';
      }
    },
  },
};
</script>