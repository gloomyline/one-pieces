<template>
    <div>
        <el-row>
            <el-col :span="24">
                <el-form :inline="true">
                    <el-form-item>
                        <el-input v-model="form.product_name" placeholder="商品名称"></el-input>
                    </el-form-item>
                    <el-form-item label="提交时间范围:">
                        <el-date-picker v-model="form.dateRange" type="daterange" align="right" ></el-date-picker>
                    </el-form-item>
                    <el-form-item>
                        <el-select v-model="form.order_state" placeholder="订单状态" clearable>
                            <el-option
                                    v-for="item in orderStates"
                                    :label="item.name"
                                    :value="item.id">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item>
                        <el-select v-model="form.state" placeholder="借款状态" clearable>
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
                    </el-form-item>
                </el-form>
            </el-col>
        </el-row>
        <el-table :data="tableData" stripe border style="width: 100%;">
            <el-table-column property="id" label="ID" align="center" width="80"></el-table-column>
            <el-table-column property="encoding" label="借款编号" align="center" width="220"></el-table-column>
            <el-table-column property="real_name" label="真实姓名" align="center" width="120"></el-table-column>
            <el-table-column property="mobile" label="手机号" align="center" width="120"></el-table-column>
            <el-table-column property="product_name" label="商品名称" align="center" width="150"></el-table-column>
            <el-table-column property="total_price" label="价格" align="center" width="150"></el-table-column>
            <el-table-column property="period" label="分期期数(月)" align="center" width="110">
                <template slot-scope="scope">
                    <span v-text="scope.row.period + '期'"></span>
                </template>
            </el-table-column>
            <el-table-column property="created_at" label="提交时间" align="center" width="180"></el-table-column>
            <el-table-column property="order_state" label="订单状态" align="center" width="100" :formatter="orderStateFormatter"></el-table-column>
           <!-- <el-table-column property="check_at" label="初审时间" align="center" width="180"></el-table-column>-->
            <el-table-column property="review_at" label="审核时间" align="center" width="180"></el-table-column>
            <el-table-column property="state" label="借款状态" align="center" width="100" :formatter="stateFormatter"></el-table-column>
            <el-table-column property="lending_at" label="放款时间" align="center" width="180"></el-table-column>
            <el-table-column property="arrival_amount" label="放款金额(元)" align="center" width="110"></el-table-column>
            <el-table-column label="操作" align="center" min-width="220">
                <template slot-scope="scope">
                    <el-button-group style="">
                        <el-button type="primary" size="small"  @click.native="handleShowConfirmDialog(scope.row.id)" v-if="scope.row.state === 'confirming'">确认订单</el-button>
                        <el-button type="danger" size="small" @click.native="handleShowReceivingDialog(scope.row.id)" v-if="scope.row.lending_at  && scope.row.state !== 'confirming'" >确认收货</el-button><!--放款成功后-->
                        <el-button type="success" size="small" @click.native="handleDetail(scope.row.id)" >查看</el-button>
                    </el-button-group>
                </template>
            </el-table-column>
        </el-table>
        <page :total="pageCount" :current-page="currentPage" :page-size="pageSize" @current-change="handleCurrentChange"></page>
        <!--商户订单确认-->
        <div>
            <el-dialog :visible.sync="confirmVisible" title='确认订单'>
                <el-form :model="confirmForm" ref="confirmForm" label-width="120px" label-position="left">
                    <el-form-item label="商品确认"  required>
                        <el-radio-group v-model="confirmForm.state">
                            <el-radio :label="1">确认通过</el-radio>
                            <el-radio :label="2">确认未通过</el-radio>
                        </el-radio-group>
                    </el-form-item>
                    <el-form-item label="意见" prop="opinion" :rules="[{ required: true, message: '意见不能为空' }]">
                        <el-input
                                type="textarea"
                                :rows="4"
                                :maxlength="200"
                                style="width: 80%"
                                placeholder="请输入内容"
                                v-model="confirmForm.opinion">
                        </el-input>
                    </el-form-item>
                </el-form>
                <span slot="footer" class="dialog-footer">
                  <el-button type="primary" @click.native="handleSubmit">确定</el-button>
                  <el-button @click.native="confirmVisible = false">取消</el-button>
                </span>
            </el-dialog>
        </div>
        <!--收货确认-->
        <div>
            <el-dialog :visible.sync="receivingVisible" title='确认收货'>
                <el-form :model="receivingForm" ref="receivingForm" label-width="120px" label-position="left">
                    <el-form-item label="买家收货确认"  required>
                        <el-radio-group v-model="receivingForm.state">
                            <el-radio :label="3">已收货</el-radio>
                            <el-radio :label="4">未收货</el-radio>
                        </el-radio-group>
                    </el-form-item>
                    <el-form-item label="意见" prop="opinion" :rules="[{ required: true, message: '意见不能为空' }]">
                        <el-input
                                type="textarea"
                                :rows="4"
                                :maxlength="200"
                                style="width: 80%"
                                placeholder="请输入内容"
                                v-model="receivingForm.opinion">
                        </el-input>
                    </el-form-item>
                </el-form>
                <span slot="footer" class="dialog-footer">
                  <el-button type="primary" @click.native="handleSubmitReceivingForm">确定</el-button>
                  <el-button @click.native="receivingVisible = false">取消</el-button>
                </span>
            </el-dialog>
        </div>
        <!--查看订单-->
        <div>
            <el-dialog :visible.sync="orderDetailVisible" title='订单详情'>
                <el-row>
                    <el-col :span="24"><el-col :span="5">借款编号</el-col><el-col :span="19">{{ orderDetail.encoding }}</el-col></el-col>
                    <el-col :span="24"><el-col :span="5">真实姓名</el-col><el-col :span="19">{{ orderDetail.real_name }}</el-col></el-col>
                    <el-col :span="24"><el-col :span="5">手机号码</el-col><el-col :span="19">{{ orderDetail.mobile }}</el-col></el-col>
                    <el-col :span="24"><el-col :span="5">商品总价（元）</el-col><el-col :span="19">{{ orderDetail.quota }}</el-col></el-col>
                    <!--商品信息-->
                    <el-col :span="24" style="border-bottom: dotted 1px"></el-col>
                    <el-col :span="24" style="border-bottom: dotted 1px" v-if="orderDetail.order" v-for="(item, k) in orderDetail.order">
                        <el-col :span="5">商品名称{{k+1}}</el-col><el-col :span="19">{{ item.title }}</el-col>
                        <el-col :span="5">规格</el-col><el-col :span="19">{{ item.spec }}</el-col>
                        <el-col :span="5">单价</el-col><el-col :span="19">{{ item.price }}</el-col>
                        <el-col :span="5">数量</el-col><el-col :span="19">{{ item.quantity }}</el-col>
                    </el-col>
                    <el-col :span="24"><el-col :span="5">提交时间</el-col><el-col :span="19">{{ orderDetail.created_at }}</el-col></el-col>
                    <el-col :span="24"><el-col :span="5">分期期数（月）</el-col><el-col :span="19">{{ orderDetail.period }}期</el-col></el-col>
                    <el-col :span="24"><el-col :span="5">订单状态</el-col><el-col :span="19" v-text="orderStateFormatter(orderDetail)"></el-col></el-col>
                    <el-col :span="24"><el-col :span="5">审核时间</el-col><el-col :span="19">{{ orderDetail.audited_at }}</el-col></el-col>
                    <el-col :span="24"><el-col :span="5">借款状态</el-col><el-col :span="19" v-text="stateFormatter(orderDetail)"></el-col></el-col>
                    <el-col :span="24"><el-col :span="5">放款时间</el-col><el-col :span="19" v-text="orderDetail.lending_at ? orderDetail.lending_at : '----'"></el-col></el-col>
                    <el-col :span="24"><el-col :span="5">放款金额（元）</el-col><el-col :span="19" v-text="orderDetail.lending_at? orderDetail.arrival_amount : '----'"></el-col></el-col>
                    <el-col :span="24"></el-col>
                </el-row>
                <span slot="footer" class="dialog-footer">
                  <el-button type="primary" @click.native="orderDetailVisible = false">关闭</el-button>
                </span>
            </el-dialog>
        </div>
    </div>
</template>
<script>
  import apiBase from '../../apiBase';
  import Page from '../../components/Page';
  import { reportErrorMessage } from '../../ApiUtil';

  export default {
    name: 'orderList',
    data() {
      return {
        tableData: [],
        form: {
          product_name: '',
          dateRange: {
            key: 'begin_at end_at',
            type: 'datePair',
          },
          order_state: '',
          state: '',
        },
        confirmForm: {
          id: '',
          state: 1,
          opinion: '',
        },
        receivingForm: {
          id: '',
          state: 3,
          opinion: '',
        },
        orderDetail: {},
        pageCount: 0,
        currentPage: 1,
        pageSize: 20,
        msg: '',
        confirmVisible: false,
        receivingVisible: false,
        orderDetailVisible: false,
        states: [{ id: 1, name: '待审核' }, { id: 2, name: '审核通过' }, { id: 3, name: '审核失败' }, { id: 4, name: '已放款' }, { id: 5, name: '未放款' }],
        orderStates: [{ id: 1, name: '确认通过' }, { id: 2, name: '确认未通过' }, { id: 3, name: '已收货' }, { id: 4, name: '未收货' }],
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
        if (this.form.product_name !== '') {
          params.product_name = this.form.product_name;
        }
        if (this.form.state !== '') {
          params.state = this.form.state;
        }
        if (this.form.order_state !== '') {
          params.order_state = this.form.order_state;
        }
        if (this.form.dateRange && this.form.dateRange[0] && this.form.dateRange[1]) {
          params.start_at = Date.parse(this.form.dateRange[0]) / 1000;
          params.end_at = Date.parse(this.form.dateRange[1]) / 1000;
        }
        this.$http.get(`${apiBase}order-list`, { params })
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
          product_name: '',
          state: '',
          order_state: '',
          dateRange: {
            key: 'begin_at end_at',
            type: 'datePair',
          },
        };
        this.getData();
      },
      clearConfirmForm() {
        this.confirmForm = {
          id: '',
          state: 1,
          opinion: '',
        };
      },
      clearReceivingForm() {
        this.receivingForm = {
          id: '',
          state: 3,
          opinion: '',
        };
      },
      handleSubmit() {
        const params = {
          state: this.confirmForm.state,
          opinion: this.confirmForm.opinion,
        };
        this.$http.post(`${apiBase}confirm-order/${this.confirmForm.id}`, params)
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
            this.confirmVisible = false;
            return {};
          }).catch(reportErrorMessage(this));
      },
      handleSubmitReceivingForm() { // 确认收货
        const params = {
          state: this.receivingForm.state,
          opinion: this.receivingForm.opinion,
        };
        this.$http.put(`${apiBase}receiving/${this.receivingForm.id}`, params)
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
            this.receivingVisible = false;
            return {};
          }).catch(reportErrorMessage(this));
      },
      handleShowConfirmDialog(id) { // 确认订单
        this.clearConfirmForm();
        this.confirmForm.id = id;
        this.confirmVisible = true;
      },
      handleShowReceivingDialog(id) { // 确认订单
        this.clearReceivingForm();
        this.receivingForm.id = id;
        this.receivingVisible = true;
      },
      stateFormatter(v) {
        switch (v.state) {
          case 'auditing':
          case 'reviewing': return '待审核'; // 1
          case 'review_success' :
          case 'confirming' :
          case 'confirm_success' :
          case 'confirm_failure' : return '审核通过'; // 2
          case 'audit_failure' :
          case 'review_failure' : return '审核失败'; // 3
          case 'repaying' :
          case 'finished' :
          case 'overdue' : return '已放款'; // 4
          case 'granting' : return '未放款';
          default: return '';
        }
      },
      orderStateFormatter(row) {
        if (row.order_state) {
          switch (row.order_state) {
            case 0 : return '待确认';
            case 1 : return '确认通过';
            case 2 : return '确认失败';
            case 3 : return '已收货';
            case 4 : return '未收货';
            default : return '';
          }
        } else {
          return '待确认';
        }
      },
      handleDetail(id) {
        this.orderDetail = {};
        this.orderDetailVisible = true;
        this.$http.get(`${apiBase}order-detail/${id}`)
          .then(response => response.json())
          .then((json) => {
            if (json.status === 'SUCCESS') {
              this.orderDetail = json.results;
            }
          }).catch(() => {
            this.$message('系统错误');
          });
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