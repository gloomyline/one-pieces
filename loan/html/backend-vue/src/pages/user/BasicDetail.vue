<template>
  <el-row >
    <el-col>
      <el-button type="info" style="margin-bottom: 5px" @click.native="$router.back(-1)">返回上一页</el-button>
      <el-button type="success" @click.native="handleDetail">基本信息</el-button>
      <el-button @click.native="handleLoanlog">借还记录</el-button>
    </el-col>
    <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">会员注册信息</div></el-col>
    <el-col :span="24" class="line-text">
      <el-col :span="3">真实姓名</el-col><el-col :span="9" v-text="data.real_name ? data.real_name:'未填写'"></el-col>
      <el-col :span="3">手机号码</el-col><el-col :span="9" v-text="data.mobile ? data.mobile:'未填写'"></el-col>
    </el-col>
    <el-col :span="24" class="line-text">
      <el-col :span="3">身份证号</el-col><el-col :span="9" v-text="data.identity_no ? data.identity_no:'未填写'"></el-col>
      <el-col :span="3">银行卡</el-col><el-col :span="9" v-text="data.bank_no ? data.bank_no:'未填写'"></el-col>
    </el-col>
    <el-col :span="24" class="line-text">
      <el-col :span="3">学历</el-col><el-col :span="9" v-text="data.education ? data.education : '未填写'"></el-col>
      <el-col :span="3">工作岗位</el-col><el-col :span="9" v-text="data.position ? data.position : '未填写'"></el-col>
    </el-col>
    <el-col :span="24" class="line-text">
      <el-col :span="3">年龄</el-col><el-col :span="9" v-text="data.age ? data.age : '未填写'"></el-col>
      <el-col :span="3">性别</el-col><el-col :span="9" v-text="sexFormatter(data.sex)"></el-col>
    </el-col>
    <el-col :span="24" class="line-text">
      <el-col :span="3">详细地址</el-col><el-col :span="9" v-text="data.live_addr ? data.live_addr:'未填写'"></el-col>
      <el-col :span="3">注册时间</el-col><el-col :span="9" v-text="data.created_at ? data.created_at:'未填写'"></el-col>
    </el-col>

    <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">身份认证信息</div></el-col>
    <el-col :span="24" class="line-text">
      <el-col :span="3">真实姓名</el-col><el-col :span="9" v-text="data.real_name ? data.real_name:'未填写'"></el-col>
      <el-col :span="3">身份证</el-col><el-col :span="9" v-text="data.identity_no ? data.identity_no:'未填写'"></el-col>
    </el-col>
    <el-col :span="24" class="line-text">
      <el-col :span="3">身份证正面</el-col><el-col :span="9">无图片</el-col>
      <el-col :span="3">身份证反面</el-col><el-col :span="9">无图片</el-col>
    </el-col>

    <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">借款资质信息</div></el-col>
    <el-col :span="24" class="line-text">
      <el-col :span="3">个人信息</el-col><el-col :span="9" v-text="data.is_profile_auth === 1 ? '已认证' : '未认证'"></el-col>
      <el-col :span="3">银行卡</el-col><el-col :span="9" v-text="data.is_bankcard_auth === 1 ? '已认证' : '未认证'"></el-col>
    </el-col>
    <el-col :span="24" class="line-text">
      <el-col :span="3">芝麻认证</el-col><el-col :span="9">暂无</el-col>
      <el-col :span="3">手机认证</el-col><el-col :span="9" v-text="data.is_phone_auth === 1 ? '已认证' : '未认证'"></el-col>
    </el-col>
    <el-col :span="24" class="line-text">
      <el-col :span="3">运营商报告</el-col><el-col :span="9"><el-button type="text"  @click.native="mobileJump(data.mobile)">查看报告</el-button></el-col>
    </el-col>

    <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">提升额度</div></el-col>
    <el-col :span="24" class="line-text">
      <el-col :span="3">淘宝账号</el-col>
      <el-col :span="9">
        <el-button type="text" @click.native="handleJumpTaobao(data.mobile)" v-if="data.is_taobao_auth === 1">查看报告</el-button>
        <span class="unfilled-red" v-else="data.is_taobao_auth === 0">未填写</span>
      </el-col>
      <el-col :span="3">京东账号</el-col>
      <el-col :span="9">
        <el-button type="text" @click.native="handleJumpJd(data.mobile)" v-if="data.is_jd_auth === 1">查看报告</el-button>
        <span class="unfilled-red" v-else="data.is_jd_auth === 0">未填写</span>
      </el-col>
    </el-col>
    <el-col :span="24" class="line-text">
      <el-col :span="3">QQ账号</el-col><el-col :span="9"  :class="{'unfilled-red' : data.qq == ''}" v-text="data.qq ? data.qq:'未填写'"></el-col>
      <el-col :span="3">微信账号</el-col><el-col :span="9"  :class="{'unfilled-red' : data.wechat == ''}" v-text="data.wechat ? data.wechat:'未填写'"></el-col>
    </el-col>
    <el-col :span="24" class="line-text">
      <el-col :span="3">学历认证</el-col>
      <el-col :span="9">
        <el-button type="text" @click.native="handleJumpBill(data.mobile)" v-if="data.is_bill_auth === 1">查看报告</el-button>
        <span class="unfilled-red" v-else="data.is_bill_auth=== 0">未填写</span>
      </el-col>
      <el-col :span="3">常用信用卡</el-col><el-col :span="9" :class="{'unfilled-red' : data.credit_card == ''}" v-text="data.credit_card ? data.credit_card : '未填写'"></el-col>
    </el-col>
    <el-col :span="24" class="line-text">
      <el-col :span="3">网银流水</el-col><el-col :span="9">
      <el-button type="text" @click.native="handleJumpEbank(data.mobile)" v-if="data.is_ebank_auth === 1">查看报告</el-button>
      <span class="unfilled-red" v-else="data.is_ebank_auth === 0">未填写</span>
    </el-col>
      <el-col :span="3">信用卡账单</el-col><el-col :span="9">
      <el-button type="text" @click.native="handleJumpBill(data.mobile)" v-if="data.is_bill_auth === 1">查看报告</el-button>
      <span class="unfilled-red" v-else="data.is_bill_auth === 0">未填写</span>
    </el-col>
    </el-col>
    <el-col :span="24" class="line-text">
      <el-col :span="3">芝麻分</el-col><el-col :span="9" v-text="">暂无</el-col>
    </el-col>

    <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">推广信息</div></el-col>
    <el-col :span="24" class="line-text">
      <el-col :span="3">邀请人数</el-col><el-col :span="9">0</el-col>
      <el-col :span="3">奖励佣金</el-col><el-col :span="9">0</el-col>
    </el-col>
    <el-col :span="24" class="line-text">
      <el-col :span="3">剩余佣金</el-col><el-col :span="9">0</el-col>
      <el-col :span="3">已提佣金</el-col><el-col :span="9">0</el-col>
    </el-col>
  </el-row>
</template>
<script>
  import apiBase from '../../apiBase';
  import { getJsonAndCheckSuccess, reportErrorMessage } from '../../ApiUtil';

  export default {
    name: 'basicDetail',
    data() {
      return {
        id: this.$route.params.id,
        data: {
          real_name: '',
          mobile: '',
          identity_no: '',
          bank_no: '',
          live_addr: '',
          created_at: '',
        },
      };
    },
    watch: {
      active() {
        this.getData();
      },
    },
    mounted() {
      this.getData();
    },
    methods: {
      getData() {
        this.$http.get(`${apiBase}user-detail/${this.id}`)
          .then(getJsonAndCheckSuccess)
          .then((response) => {
            this.data = response.results;
          }).catch(reportErrorMessage(this));
      },
      handleDetail() {
        this.$router.push({
          path: `/user-detail/${this.id}`,
        });
      },
      handleLoanlog() {
        this.$router.push({
          path: `/user-loan/${this.id}`,
        });
      },
      mobileJump(v) {
        this.$router.push({
          path: '/auth-mobile',
          query: { mobile: v },
        });
      },
      handleJumpEdu(v) { // 学历认证
        this.$router.push({
          path: '/auth-edu',
          query: { mobile: v },
        });
      },
      handleJumpTaobao(v) { // 淘宝
        this.$router.push({
          path: '/auth-taobao',
          query: { mobile: v },
        });
      },
      handleJumpJd(v) { // 京东
        this.$router.push({
          path: '/auth-jd',
          query: { mobile: v },
        });
      },
      handleJumpBill(v) { // 信用卡账单
        this.$router.push({
          path: '/auth-bill',
          query: { mobile: v },
        });
      },
      handleJumpEbank(v) { // 网银流水
        this.$router.push({
          path: '/auth-ebank',
          query: { mobile: v },
        });
      },
      sexFormatter(v) {
        switch (v) {
          case 0 : return '未知';
          case 1 : return '男';
          case 2 : return '女';
          default : return '未填写';
        }
      },
    },
  };
</script>
<style type="text/css">
  .el-col-24{padding: 8px;}
  .line-text {
    line-height: 40px;
    margin-top: 10px;
    border-bottom: #bcbcbc solid 1px
  }
</style>