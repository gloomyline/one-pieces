<template>
	<div>
        <el-form :model="form" :rules="rules" ref="productForm" label-width="120px">
          <el-form-item label="借款名称" prop="title" :rules="[{ required: true, message: '借款名称不能为空' }]">
            <el-input auto-complete="off" v-model="form.title" placeholder="请输入借款名称" style="width:300px;"></el-input>
          </el-form-item>
          <el-form-item label="借款金额" required>
            <el-input auto-complete="off" v-model="form.min_quota" placeholder="借款最低额度" style="width:110px;"></el-input>
             &nbsp;&nbsp;元&nbsp;&nbsp; -&nbsp;&nbsp;
            <el-input auto-complete="off" v-model="form.max_quota" placeholder="借款最高额度" style="width:110px;"></el-input>&nbsp;&nbsp;元
          </el-form-item>
          <el-form-item label="借款期限" required>
            <el-input auto-complete="off" v-model="form.min_period" placeholder="借款周期下限" style="width:110px;"></el-input>
             &nbsp;&nbsp;天&nbsp;&nbsp; -&nbsp;&nbsp;
            <el-input auto-complete="off" v-model="form.max_period" placeholder="借款周期上限" style="width:110px;"></el-input>&nbsp;&nbsp;天
          </el-form-item>
          <el-form-item label="年化利率" prop="annualized_interest_rate" :rules="[{ required: true, message: '年化利率不能为空' }]">
            <el-input auto-complete="off" v-model="form.annualized_interest_rate" placeholder="请输入年化利率" style="width:300px;"></el-input>
          </el-form-item>
          <el-form-item label="还款方式" prop="repayment_way" :rules="[{ required: true, message: '还款方式不能为空' }]">
            <el-select v-model="form.repayment_way" placeholder="还款方式" clearable  style="width:300px;">
              <el-option label="到期本息"  value="1">到期本息</el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="信审费率" prop="trial_rate" :rules="[{ required: true, message: '信审费率不能为空' }]">
            <el-input auto-complete="off" v-model="form.trial_rate" placeholder="请输入信审费率" style="width:300px;"></el-input>
          </el-form-item>
          <el-form-item label="服务费率" prop="service_rate" :rules="[{ required: true, message: '服务费率不能为空' }]">
            <el-input auto-complete="off" v-model="form.service_rate" placeholder="请输入服务费率" style="width:300px;"></el-input>
          </el-form-item>
          <el-form-item label="手续费率" prop="poundage" :rules="[{ required: true, message: '手续费率不能为空' }]">
            <el-input auto-complete="off" v-model="form.poundage" placeholder="请输入手续费率" style="width:300px;"></el-input>
          </el-form-item>
          <el-form-item label="逾期费率" prop="overdue_rate" :rules="[{ required: true, message: '逾期费率不能为空' }]">
            <el-input auto-complete="off" v-model="form.overdue_rate" placeholder="请输入逾期费率" style="width:300px;"></el-input>
          </el-form-item>
          <el-form-item label="逾期最大天数" prop="limit_overdue_days" :rules="[{ required: true, message: '逾期最大天数不能为空' }]">
            <el-input auto-complete="off" v-model="form.limit_overdue_days" placeholder="请输入逾期最大天数" style="width:300px;"></el-input>&nbsp;&nbsp;天
          </el-form-item>
          <el-form-item>
  	        <el-button type="primary" @click.native="handleSubmit">保存</el-button>
  	        <el-button type="primary" @click="$router.back()">取消</el-button>
  	      </el-form-item>
        </el-form>
    </div> 
</template>
<script>
import apiBase from '../../apiBase';
import { getJsonAndCheckSuccess, reportErrorMessage } from '../../ApiUtil';

export default {
  name: 'productForm',
  data() {
    return {
      id: this.$route.params.id,
      form: {
        title: '',
        min_quota: '',
        max_quota: '',
        min_period: '',
        max_period: '',
        annualized_interest_rate: '',
        repayment_way: '',
        trial_rate: '',
        service_rate: '',
        poundage: '',
        overdue_rate: '',
        limit_overdue_days: '',
      },
    };
  },
  mounted() {
    if (this.id) {
      this.$http.get(`${apiBase}product-detail/${this.id}`)
          .then(getJsonAndCheckSuccess)
          .then((json) => {
            this.form = json.results;
          }).catch(reportErrorMessage(this));
    }
  },
  methods: {
    handleSubmit() {
      const params = {
        title: this.form.title,
        min_quota: this.form.min_quota,
        max_quota: this.form.max_quota,
        min_period: this.form.min_period,
        max_period: this.form.max_period,
        annualized_interest_rate: this.form.annualized_interest_rate,
        repayment_way: this.form.repayment_way,
        trial_rate: this.form.trial_rate,
        service_rate: this.form.service_rate,
        poundage: this.form.poundage,
        overdue_rate: this.form.overdue_rate,
        limit_overdue_days: this.form.limit_overdue_days,
      };
      if (this.id) {
        this.$http.put(`${apiBase}update-product/${this.id}`, params)
          .then(getJsonAndCheckSuccess)
          .then(() => {
            this.$message({
              type: 'success',
              message: '修改成功！',
            });
            this.$router.push({ path: '/products' });
          }).catch(reportErrorMessage(this));
      } else {
        this.$http.post(`${apiBase}add-product`, params)
          .then(getJsonAndCheckSuccess)
          .then(() => {
            this.$message({
              type: 'success',
              message: '新增成功！',
            });
            this.$router.push({ path: '/products' });
          }).catch(reportErrorMessage(this));
      }
    },
  },
};
</script>