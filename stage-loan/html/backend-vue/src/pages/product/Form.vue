<template>
	<div>
        <el-form :model="form" :rules="rules" ref="productForm" label-width="150px">
          <el-form-item label="借款名称" prop="title" :rules="[{ required: true, message: '借款名称不能为空'}, { min: 0, max: 10, message: '借款名称不超过10个字符'}]">
            <el-input auto-complete="off" v-model="form.title" placeholder="请输入借款名称" style="width:300px;" :maxlength="10"></el-input>
          </el-form-item>
          <el-form-item label="产品类型">
            <span>{{ form.type | filterCategories }}</span>
          </el-form-item>
          <el-form-item label="借款金额" prop="min_quota" :rules="[{ required: true, message: '借款金额填写不能为空'}]">
            <el-input auto-complete="off" v-model="form.min_quota" placeholder="借款最低额度" style="width:110px;"></el-input>
             &nbsp;&nbsp;元&nbsp;&nbsp; -&nbsp;&nbsp;
            <el-input auto-complete="off" v-model="form.max_quota" placeholder="借款最高额度" style="width:110px;"></el-input>&nbsp;&nbsp;元
          </el-form-item>
          <el-form-item label="借款期数(月)" prop="period" :rules="[{ required: true, message: '请勾选借款期数'}]">
            <el-checkbox-group v-model="form.period">
              <el-checkbox label="1">1期</el-checkbox>
              <el-checkbox label="3">3期</el-checkbox>
              <el-checkbox label="6">6期</el-checkbox>
              <el-checkbox label="9">9期</el-checkbox>
              <el-checkbox label="12">12期</el-checkbox><br/>
              <el-checkbox label="15">15期</el-checkbox>
              <el-checkbox label="18">28期</el-checkbox>
              <el-checkbox label="21">21期</el-checkbox>
              <el-checkbox label="24">24期</el-checkbox>
              <el-checkbox label="36">36期</el-checkbox>
            </el-checkbox-group>
          </el-form-item>
          <el-form-item label="年化利率" prop="annualized_interest_rate" :rules="[{ required: true, message: '年化利率不能为空'}]">
            <el-input auto-complete="off" type="number" :step="0.001" :min="0" v-model.number="form.annualized_interest_rate" placeholder="请输入年化利率" style="width:300px;"></el-input>
          </el-form-item>
          <el-form-item label="还款方式" prop="repayment_way" :rules="[{ required: true, message: '还款方式不能为空'}]">
            <el-select v-model="form.repayment_way" placeholder="还款方式" clearable  style="width:300px;">
              <el-option label="等本等息"  value="1">等本等息</el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="信审费率" prop="trial_rate" :rules="[{ required: true, message: '信审费率不能为空'}]">
            <el-input auto-complete="off" type="number" :step="0.001" :min="0" v-model="form.trial_rate" placeholder="请输入信审费率" style="width:300px;"></el-input>
          </el-form-item>
          <el-form-item label="服务费率" prop="service_rate" :rules="[{ required: true, message: '服务费率不能为空'}]">
            <el-input auto-complete="off" type="number" :step="0.001" :min="0" v-model="form.service_rate" placeholder="请输入服务费率" style="width:300px;"></el-input>
          </el-form-item>
          <el-form-item label="手续费率" prop="poundage" :rules="[{ required: true, message: '手续费率不能为空'}]">
            <el-input auto-complete="off" type="number" :step="0.001" :min="0" v-model="form.poundage" placeholder="请输入手续费率" style="width:300px;"></el-input>
          </el-form-item>
          <el-form-item label="提前还款手续费率" prop="prepayment_poundage" :rules="[{ required: true, message: '提前还款手续费率不能为空'}]">
            <el-input auto-complete="off" type="number" :step="0.001" :min="0" v-model="form.prepayment_poundage" placeholder="请输入提前还款手续费率" style="width:300px;"></el-input>(提前还款手续费=本金*提前还款手续费率)
          </el-form-item>
          <el-form-item label="提前还款手续费上限" prop="prepayment_poundage_max" :rules="[{ required: true, message: '提前还款手续费上限不能为空'}]">
            <el-input auto-complete="off" type="number" :step="1" :min="0" v-model="form.prepayment_poundage_max" placeholder="请输入提前还款手续费上限" style="width:300px;"></el-input>
          </el-form-item>
          <el-form-item label="逾期费率" prop="overdue_rate" :rules="[{ required: true, message: '逾期费率不能为空'}]">
            <el-input auto-complete="off" type="number" :step="0.001" :min="0" v-model="form.overdue_rate" placeholder="请输入逾期费率" style="width:300px;"></el-input>
          </el-form-item>
          <el-form-item label="逾期最大天数" prop="limit_overdue_days" :rules="[{ required: true, message: '逾期最大天数不能为空'}]">
            <el-input auto-complete="off"  type="number" :step="1" :min="0" v-model="form.limit_overdue_days" placeholder="请输入逾期最大天数" style="width:300px;"></el-input>&nbsp;&nbsp;天
          </el-form-item>
          <el-form-item label="用途" prop="use" required>
            <el-tag
                    :key="tag"
                    v-for="tag in form.use"
                    closable
                    type="danger"
                    :disable-transitions="false"
                    @close="handleClose(tag)">
              {{tag}}
            </el-tag>
            <el-input
                    class="input-new-tag"
                    v-if="inputVisible"
                    v-model="inputValue"
                    :maxlength="6"
                    ref="saveTagInput"
                    size="small"
                    @keyup.enter.native="handleInputConfirm"
                    @blur="handleInputConfirm"
            >
            </el-input>
            <el-button v-else class="button-new-tag" size="small" @click="showInput">添加</el-button>

          </el-form-item>
          <el-form-item label="状态" prop="active" required>
            <el-radio v-model="form.active" label="1">开启</el-radio>
            <el-radio v-model="form.active" label="0">关闭</el-radio>
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
        type: '',
        min_quota: '',
        max_quota: '',
        period: [],
        annualized_interest_rate: '',
        repayment_way: '',
        trial_rate: '',
        service_rate: '',
        poundage: '',
        prepayment_poundage: '',
        prepayment_poundage_max: '',
        overdue_rate: '',
        limit_overdue_days: '',
        use: [],
        active: 0,
      },
      inputVisible: false,
      inputValue: '',
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
        periods: this.form.period,
        annualized_interest_rate: this.form.annualized_interest_rate,
        repayment_way: this.form.repayment_way,
        trial_rate: this.form.trial_rate,
        service_rate: this.form.service_rate,
        poundage: this.form.poundage,
        prepayment_poundage: this.form.prepayment_poundage,
        prepayment_poundage_max: this.form.prepayment_poundage_max,
        overdue_rate: this.form.overdue_rate,
        limit_overdue_days: this.form.limit_overdue_days,
        use: this.form.use,
        active: this.form.active,
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
    handleClose(tag) {
      this.form.use.splice(this.form.use.indexOf(tag), 1);
    },
    showInput() {
      this.inputVisible = true;
      this.$nextTick(() => {
        this.$refs.saveTagInput.$refs.input.focus();
      });
    },
    handleInputConfirm() {
      const inputValue = this.inputValue;
      if (inputValue) {
        this.form.use.push(inputValue);
      }
      this.inputVisible = false;
      this.inputValue = '';
    },
  },
  filters: {
    filterCategories(v) {
      switch (v) {
        case '1' : return '现金分期';
        case '2' : return '消费分期';
        default : return '';
      }
    },
  },
};
</script>

<style type="text/css">
  .el-tag + .el-tag {
    margin-left: 10px;
  }
  .button-new-tag {
    margin-left: 10px;
    height: 32px;
    line-height: 30px;
    padding-top: 0;
    padding-bottom: 0;
  }
  .input-new-tag {
    width: 90px;
    margin-left: 10px;
    vertical-align: bottom;
  }
</style>
