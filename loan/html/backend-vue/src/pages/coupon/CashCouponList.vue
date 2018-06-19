<template>
  <div>
    <el-row>
      <el-col :span="24">
        <el-form :inline="true">
          <el-form-item prop="name">
            <el-select v-model="form.name" placeholder="代金券名称" clearable>
              <el-option
                      v-for="item in names"
                      :label="item.name"
                      :value="item.id">
              </el-option>
            </el-select>
          </el-form-item>
          <el-form-item prop="type">
            <el-select v-model="form.type" placeholder="奖励类型" clearable>
              <el-option 
                v-for="item in types"
                :label="item.name" 
                :value="item.id">
              </el-option>
            </el-select>
          </el-form-item>
          <el-form-item prop="state">
            <el-select v-model="form.state" placeholder="状态" clearable>
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
            <el-button type="success" @click.native="handleAdd">添加</el-button>
          </el-form-item>
        </el-form>
      </el-col>
    </el-row>
    <el-table :data="tableData" stripe border style="width: 100%;">
      <el-table-column property="id" label="ID" align="center" min-width="60"></el-table-column>
      <el-table-column property="coupon_name" label="代金券名称" align="center" min-width="150" :formatter="nameFormatter"></el-table-column>-->
      <el-table-column property="coupon_type" label="奖励类型" align="center" min-width="150" :formatter="typeFormatter"></el-table-column>
      <el-table-column property="coupon_amount" label="金额" align="center" min-width="100"></el-table-column>
      <el-table-column property="min_repayment" label="还款金额下限" align="center" min-width="100" :formatter="minRepaymentFormatter"></el-table-column>
      <el-table-column property="state" label="状态" align="center" min-width="80" :formatter="stateFormatter"></el-table-column>
      <el-table-column property="begin_at" label="开始时间" align="center" width="180"></el-table-column>
      <el-table-column property="end_at" label="截止时间" :render-header="renderHeader" align="center" width="180" :formatter="endAtFormatter"></el-table-column>
      <el-table-column property="validity_period" label="有效期(天)" align="center" min-width="80">
        <template slot-scope="scope">
          <span>{{ scope.row | validityPeriodFilter }}</span>
        </template>
      </el-table-column>
      <el-table-column label="操作" align="center" width="180">
        <template slot-scope="scope">
          <el-button-group style="">
            <el-button type="primary" size="small"  @click.native="updateItem(scope.row.id)">编 辑</el-button>
            <el-button type="danger" size="small"  @click.native="delItem(scope.row.id)">删 除</el-button>
          </el-button-group>
        </template>
      </el-table-column>
    </el-table>
    <page :total="pageCount" :current-page="currentPage" :page-size="pageSize" @current-change="handleCurrentChange"></page>

    <el-dialog title="添加" :visible.sync="dialogAddFormVisible">
      <el-form :model="addForm" ref="addForm">
        <el-form-item label="代金券名称" prop="coupon_name" :label-width="formLabelWidth" :rules="[{ required: true, message: '请选择代金券名称'}]">
          <el-select v-model="addForm.coupon_name" placeholder="请选择" @change="changeCouponName(Number(addForm.coupon_name))" clearable>
            <el-option v-for="(item,index) in names" :label="item.name" :value="item.id"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="奖励类型" prop="coupon_type" :label-width="formLabelWidth" :rules="[{ required: true, message: '请选择奖励类型'}]">
          <el-select v-model="addForm.coupon_type" placeholder="请选择" clearable>
            <el-option v-for="(item,index) in types" :label="item.name" :value="item.id"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="金额" :label-width="formLabelWidth" prop="coupon_amount" :rules="[{ required: true, message: '请输入金额'}]">
          <el-input type="number" v-model="addForm.coupon_amount" placeholder="请输入一个正整数" style="width: 220px" @blur="validityAmount(addForm.coupon_amount)">
            <template slot="append">元</template>
          </el-input>
        </el-form-item>
        <el-form-item label="开始时间" :label-width="formLabelWidth" prop="begin_at" :rules="[{ required: true, message: '开始时间不能为空'}]">
          <div class="block">
            <el-date-picker
                    v-model="addForm.begin_at"
                    type="date"
                    @change="changeBeginAt(addForm.begin_at)"
                    placeholder="选择日期"
                    style="width: 220px">
            </el-date-picker>
          </div>
        </el-form-item>
        <el-form-item label="截止时间" :label-width="formLabelWidth" prop="end_at" :rules="[{ required: true, message: '截止时间不能为空'}]">
          <div class="block">
            <el-date-picker
                    v-model="addForm.end_at"
                    type="date"
                    placeholder="选择日期"
                    :picker-options="pickerOptions"
                    style="width: 220px"
                    :disabled="canPickDate">
            </el-date-picker>
          </div>
        </el-form-item>
        <el-form-item label="还款金额下限" :label-width="formLabelWidth" prop="min_repayment" :rules="[{ required: true, message: '还款金额下限不能为空'}]" v-if="seen">
          <el-input type="number" v-model="addForm.min_repayment" placeholder="请输入一个正整数" style="width: 220px"  @blur="validityAmount(addForm.min_repayment)">
            <template slot="append">元</template>
          </el-input>
        </el-form-item>
        <el-form-item label="状态" :label-width="formLabelWidth" required>
          <el-radio-group v-model="addForm.state">
            <el-radio :label="1">开启</el-radio>
            <el-radio :label="0">关闭</el-radio>
          </el-radio-group>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogAddFormVisible = false">取 消</el-button>
        <el-button type="primary" @click="handleSubmit">提 交</el-button>
      </div>
    </el-dialog>

    <el-dialog title="编辑" :visible.sync="dialogUpdateFormVisible">
      <el-form :model="addForm" ref="updateForm">
        <el-form-item label="代金券名称" :label-width="formLabelWidth" prop="coupon_name" :rules="[{ required: true, message: '请选择代金券名称'}]">
          <el-select v-model="addForm.coupon_name" placeholder="请选择" @change="changeCouponName(addForm.coupon_name)" clearable disabled>
            <el-option v-for="(item,index) in names" :label="item.name" :value="item.id"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="奖励类型" :label-width="formLabelWidth" required>
          <el-select v-model="addForm.coupon_type" placeholder="请选择" clearable disabled>
            <el-option v-for="(item,index) in types" :label="item.name" :value="item.id"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="金额" :label-width="formLabelWidth" required prop="coupon_amount">
          <el-input auto-complete="off" type="number" v-model="addForm.coupon_amount" placeholder="请输入一个正整数" style="width: 220px" @blur="validityAmount(addForm.coupon_amount)" v-if="addForm.count === 0">
            <template slot="append">元</template>
          </el-input>
          <el-input auto-complete="off" type="number" v-model="addForm.coupon_amount" placeholder="请输入一个正整数" style="width: 220px" @blur="validityAmount(addForm.coupon_amount)" v-if="addForm.count !== 0" disabled>
            <template slot="append">元</template>
          </el-input>
        </el-form-item>
        <el-form-item label="开始时间" :label-width="formLabelWidth" required>
          <div class="block">
            <el-date-picker
                    v-model="addForm.begin_at"
                    type="date"
                    @change="changeBeginAt(addForm.begin_at)"
                    placeholder="选择日期"
                    style="width: 220px"
                    disabled>
            </el-date-picker>
          </div>
        </el-form-item>
        <el-form-item label="截止时间" :label-width="formLabelWidth" required v-if="addForm.end_at !== ''">
          <div class="block">
            <el-date-picker
                    v-model="addForm.end_at"
                    type="date"
                    placeholder="选择日期"
                    :picker-options="pickerOptions"
                    style="width: 220px"
                    disabled>
            </el-date-picker>
          </div>
        </el-form-item>
        <el-form-item label="还款金额下限" :label-width="formLabelWidth" prop="min_repayment" required v-if="seen">
          <el-input type="number" v-model="addForm.min_repayment" placeholder="请输入一个正整数" style="width: 220px"  @blur="validityAmount(addForm.min_repayment)"  v-if="addForm.count === 0">
            <template slot="append">元</template>
          </el-input>
          <el-input type="number" v-model="addForm.min_repayment" placeholder="请输入一个正整数" style="width: 220px"  @blur="validityAmount(addForm.min_repayment)"  v-if="addForm.count !== 0" disabled>
            <template slot="append">元</template>
          </el-input>
        </el-form-item>
        <el-form-item label="状态" :label-width="formLabelWidth" required>
          <el-radio-group v-model="addForm.state">
            <el-radio :label="1">开启</el-radio>
            <el-radio :label="0">关闭</el-radio>
          </el-radio-group>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogUpdateFormVisible = false">取 消</el-button>
        <el-button type="primary" @click="handleSubmitUpdate">提 交</el-button>
      </div>
    </el-dialog>

  </div>
</template>
<script>
import apiBase from '../../apiBase';
import Page from '../../components/Page';
import { reportErrorMessage, getJsonAndCheckSuccess } from '../../ApiUtil';

export default {
  name: 'Coupon',
  data() {
    return {
      formLabelWidth: '150px',
      tableData: [],
      pickerOptions: '',
      form: {
        name: '',
        type: '',
        state: '',
      },
      updateId: 0,
      addForm: {
        coupon_name: '',
        coupon_type: '',
        coupon_amount: '',
        state: 1,
        min_repayment: '',
        begin_at: '',
        end_at: '',
        count: 0,
      },
      pageCount: 0,
      currentPage: 1,
      pageSize: 20,
      msg: '',
      seen: false,
      cashSeen: false,
      canPickDate: true,
      dialogAddFormVisible: false,
      dialogUpdateFormVisible: false,
      types: [{ id: 1, name: '注册成功' }, { id: 2, name: '邀请好友注册成功' }, { id: 3, name: '邀请好友借款成功' }],
      states: [{ id: 1, name: '开启' }, { id: 0, name: '关闭' }],
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
        params.name = this.form.name;
      }
      if (this.form.type !== '') {
        params.type = this.form.type;
      }
      if (this.form.state !== '') {
        params.state = this.form.state;
      }
      this.$http.get(`${apiBase}coupon`, { params })
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
    validityAmount(val) {
      const va = Number(val);
      if (!Number.isInteger(va) || va < 0) {
        this.$message('金额只能为正整数');
      }
    },
    changeBeginAt(val) { // 设置截止时间的起始值
      if (val !== '') {
        this.canPickDate = false;
      } else {
        this.canPickDate = true;
      }
      this.pickerOptions = {
        disabledDate(time) {
          return time.getTime() < Date.parse(val);
        },
      };
    },
    changeCouponName(value) {
      if (value === 1) { // 还款抵扣券，有还款下限
        this.seen = true;
        this.cashSeen = true;
      }
      if (value === 2) { // 现金奖励没有还款下限
        this.seen = false;
        this.cashSeen = false;
      }
    },
    handleCurrentChange(page) {
      this.currentPage = page;
      this.getData();
    },
    handleAdd() {
      this.dialogAddFormVisible = true;
      this.clearAddForm(); // 重置表单
    },
    handleSubmit() {
      const amount = Number(this.addForm.coupon_amount);
      if (!Number.isInteger(amount) || amount < 0) {
        this.$message('金额只能为正整数');
        return;
      }
      const params = {
        coupon_name: this.addForm.coupon_name,
        coupon_type: this.addForm.coupon_type,
        coupon_amount: this.addForm.coupon_amount,
        state: this.addForm.state,
      };
      if (this.addForm.begin_at) {
        params.begin_at = Date.parse(this.addForm.begin_at) / 1000;
      }
      if (this.addForm.end_at) {
        params.end_at = Date.parse(this.addForm.end_at) / 1000;
      }
      if (this.addForm.coupon_name === 1) { // 还款抵扣券
        const minRepayment = Number(this.addForm.min_repayment);
        if (!Number.isInteger(minRepayment) || minRepayment < 0) {
          this.$message('还款下限金额只能为正整数');
          return;
        }
        params.min_repayment = this.addForm.min_repayment;
      }

      this.$http.post(`${apiBase}add-cash-coupon`, params)
      .then(response => response.json())
      .then((json) => {
        if (json.status !== 'SUCCESS') {
          return Promise.reject(json.error_message);
        }
        this.$message({
          type: 'success',
          message: '添加成功！',
        });
        this.getData();
        this.dialogAddFormVisible = false;
        return {};
      }).catch(reportErrorMessage(this));
    },
    handleSubmitUpdate() { // 需要传入条目id
      const amount = Number(this.addForm.coupon_amount);
      if (!Number.isInteger(amount) || amount < 0) {
        this.$message('金额只能为正整数');
        return;
      }
      const params = {
        id: this.updateId,
        coupon_amount: this.addForm.coupon_amount,
        state: this.addForm.state,
      };
      if (this.addForm.coupon_name === 1) { // 还款抵扣券
        params.min_repayment = this.addForm.min_repayment;
      }
      this.$http.put(`${apiBase}update-coupon`, params)
        .then(response => response.json())
        .then((json) => {
          if (json.status !== 'SUCCESS') {
            return Promise.reject(json.error_message);
          }
          this.$message({
            type: 'success',
            message: '编辑成功！',
          });
          this.getData();
          this.dialogUpdateFormVisible = false;
          return {};
        }).catch(reportErrorMessage(this));
    },
    filter() {
      this.currentPage = 1;
      this.getData();
    },
    clearFilter() {
      this.form = {
        name: '',
        type: '',
        state: '',
      };
      this.getData();
    },
    clearAddForm() {
      this.addForm = {
        coupon_name: '',
        coupon_type: '',
        coupon_amount: '',
        state: 1,
        min_repayment: '',
        begin_at: '',
        end_at: '',
      };
    },
    delItem(id) {
      this.$confirm('确定删除该代金券?', '提示', {
        type: 'warning',
      }).then(() => {
        this.$http.post(`${apiBase}del-coupon`, { coupon_id: id })
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
      this.updateId = id;
      this.dialogUpdateFormVisible = true;
      this.$http.get(`${apiBase}get-coupon/${id}`)
        .then(getJsonAndCheckSuccess)
        .then((json) => {
          this.addForm = json.results;
          this.changeCouponName(this.addForm.coupon_name);
        }).catch(reportErrorMessage(this));
    },
    stateFormatter(row) {
      return row.state === 0 ? '关闭' : '开启';
    },
    typeFormatter(row) {
      return this.types.find(v => v.id === row.coupon_type).name;
    },
    nameFormatter(row) {
      return this.names.find(v => v.id === row.coupon_name).name;
    },
    minRepaymentFormatter(row) {
      return row.min_repayment === 0 ? '--' : row.min_repayment;
    },
    endAtFormatter(row) {
      return row.end_at === '' ? '--' : row.end_at;
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
  },
};
</script>