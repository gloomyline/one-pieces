<template>
  <div>
    <el-form :model="form" ref="form" label-width="120px">
      <el-form-item label="用户名" prop="username" :rules="[{ required: true, message: '请输入用户名'}]">
        <el-input type="tel" auto-complete="off" v-model="form.username" placeholder="请输入用户名" style="width:220px;"></el-input>
      </el-form-item>
      <el-form-item label="代金券名称" prop="coupon_id" :rules="[{ required: true, message: '请选择代金券名称'}]">
        <el-select v-model="form.coupon_id" placeholder="选择抵扣券" @change="changeCoupon(form.coupon_id)" clearable>
          <el-option v-for="(item,index) in api.coupons" :label="item.name" :value="item.id"></el-option>
        </el-select>
      </el-form-item>
      <el-form-item label="金额"  required prop="coupon_amount">
        <el-input type="number" v-model="form.coupon_amount" placeholder="请输入一个正整数" style="width: 220px" @blur="validityAmount(form.coupon_amount)" disabled>
          <template slot="append">元</template>
        </el-input>
      </el-form-item>
      <el-form-item label="开始时间" prop="begin_at" required>
        <el-input auto-complete="off" v-model="form.begin_at" placeholder="开始日期" style="width:220px;" disabled></el-input>
      </el-form-item>
      <el-form-item label="截止时间" prop="end_at" required>
        <el-input auto-complete="off" v-model="form.end_at" placeholder="截止日期" style="width:220px;" disabled></el-input>
      </el-form-item>
      <el-form-item label="还款金额下限" prop="min_repayment" required>
        <el-input type="number" v-model="form.min_repayment" placeholder="请输入一个正整数" style="width: 220px"  @blur="validityAmount(form.min_repayment)" disabled>
          <template slot="append">元</template>
        </el-input>
      </el-form-item>
      <el-form-item>
        <el-button type="primary" @click.native="handleSubmit">保存</el-button>
        <el-button type="primary" @click="resetForm('form')">重置</el-button>
      </el-form-item>
      </el-form>
  </div>
</template>
<script>
import apiBase from '../../apiBase';
import { getJsonAndCheckSuccess, reportErrorMessage } from '../../ApiUtil';

export default {
  name: 'ManualAssignForm',
  data() {
    return {
      id: this.$route.params.id,
      api: {
        coupons: [],
      },
      form: {
        username: '', // 用户名
        coupon_name: '',
        coupon_id: '',
        coupon_amount: '', // 还款金额
        begin_at: '',
        end_at: '',
        min_repayment: '',
      },
      canPickDate: true,
    };
  },
  mounted() {
    this.$http.get(`${apiBase}get-repayment-coupon`)
        .then(getJsonAndCheckSuccess)
        .then((response) => {
          this.api.coupons = response.results;
        }).catch(reportErrorMessage(this));
  },
  methods: {
    handleSubmit() {
      const params = {
        username: this.form.username,
        coupon_id: this.form.coupon_id,
      };
      this.$http.post(`${apiBase}manual-assign-coupon`, params)
        .then(getJsonAndCheckSuccess)
        .then(() => {
          this.$message({
            type: 'success',
            message: '派发成功！',
          });
          this.$refs.form.resetFields();
        }).catch(reportErrorMessage(this));
    },
    changeCoupon(id) {
      if (id !== '') {
        const coupon = this.api.coupons.find(v => v.id === id);
        this.form.min_repayment = coupon.min_repayment;
        this.form.begin_at = coupon.begin_at;
        this.form.end_at = coupon.end_at;
        this.form.coupon_amount = coupon.coupon_amount;
      } else {
        this.form.min_repayment = '';
        this.form.begin_at = '';
        this.form.end_at = '';
        this.form.coupon_amount = '';
      }
    },
    checkUser() {

    },
    validityAmount(val) {
      const va = Number(val);
      if (!Number.isInteger(va) || va < 0) {
        this.$message('金额只能为正整数');
      }
    },
    resetForm(formName) {
      this.$refs[formName].resetFields();
    },
  },
};
</script>