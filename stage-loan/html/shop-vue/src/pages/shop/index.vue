<template>
    <div style="overflow: auto">
        <el-row :gutter="30"  style="min-width: 1000px; max-width:1200px">
            <el-col :span="24"><h4 style="padding-left: 15px">商户名称：{{ body.shop_name }}</h4><!--<el-col :lg="6">商户名称</el-col><el-col :lg="18">{{ body.shop_name }}</el-col>--></el-col>
            <el-col :span="24"><h4 style="padding-left: 15px">审核状态：{{ stateFormatter(body.state) }}</h4></el-col>
            <el-col :span="24"><h4 style="padding-left: 15px">商户号：{{ body.shop_no }}</h4></el-col>
            <el-col :span="24" style="overflow: auto;margin-top: 20px">
                <el-col :span="24">
                    <el-col :span="8" class="statistic-box">
                        <div>授信额度(元)</div>
                        <div class="money-box">{{ body.total_quota }}</div>
                    </el-col>
                    <el-col :span="8" class="statistic-box">
                        <div>今日限额(元)</div>
                        <div class="money-box">{{ body.daily_limit_quota }}</div>
                    </el-col>
                    <el-col :span="8" class="statistic-box" style="border-right: 1px solid #cccccc">
                        <div>单笔限额(元)</div>
                        <div class="money-box">{{ body.single_limit_quota }}</div>
                    </el-col>
                </el-col>
                <el-col :span="24">
                    <el-col :span="8" class="statistic-box" style="border-bottom: 1px solid #cccccc">
                        <div>今日销售金额(元)</div>
                        <div class="money-box">{{ body.today_sale }}</div>
                    </el-col>
                    <el-col :span="8" class="statistic-box" style="border-bottom: 1px solid #cccccc">
                        <div>今日销售笔数(笔)</div>
                        <div class="money-box">{{ body.today_count }}</div>
                    </el-col>
                    <el-col :span="8" class="statistic-box" style="border-right: 1px solid #cccccc; border-bottom: 1px solid #cccccc">
                        <div>累计销售金额(元)</div>
                        <div class="money-box">{{ body.statistics_sale }}</div>
                    </el-col>
                </el-col>
            </el-col>
            <el-col :span="24" style="margin-top: 20px">
                <el-col :span="18">
                    <el-card class="box-card">
                        <div slot="header" class="clearfix">
                            <span style="font-size: 16px">商品总览</span>
                        </div>
                        <el-col :span="8" class="text item-box">
                            <div class="num">{{ body.on_sale_count }}</div>
                            <div>已上架</div>
                        </el-col>
                        <el-col :span="8" class="text item-box">
                            <div class="num">{{ (body.all_count - body.on_sale_count) >= 0 ? (body.all_count - body.on_sale_count)  : 0 }}</div>
                            <div>已下架</div>
                        </el-col>
                        <el-col :span="8" class="text item-box">
                            <div class="num">{{ body.all_count }}</div>
                            <div>全部商品</div>
                        </el-col>
                        <div style="clear: both"></div>
                    </el-card>
                </el-col>
                <el-col :span="6">
                    <el-card class="box-card">
                        <div slot="header" class="clearfix">
                            <span style="font-size: 16px">用户总览</span>
                        </div>
                        <el-col :span="24" class="text item-box">
                            <div class="num">{{ body.user_count }}</div>
                            <div>用户总数</div>
                        </el-col>
                        <div style="clear: both"></div>
                    </el-card>
                </el-col>
            </el-col>
        </el-row>
    </div>
</template>

<script>
  import apiBase from '../../apiBase';

  export default {
    name: 'shopIndex',
    components: {
    },
    data() {
      return {
        body: {
          shop_name: '',
          state: 0,
          shop_no: '',
          total_quota: 0,
          daily_limit_quota: 0,
          single_limit_quota: 0,
          today_sale: 0,
          today_count: 0,
          statistics_sale: 0,
          all_count: 0,
          on_sale_count: 0,
          user_count: 0,
        },
      };
    },
    mounted() {
      this.getData();
    },
    methods: {
      getData() {
        this.$http.get(`${apiBase}index`)
          .then(response => response.json())
          .then((json) => {
            if (json.status === 'SUCCESS') {
              this.body = json.results;
            }
          }).catch(() => {
            this.$message('系统错误');
          });
      },
      auditingJump() {
        this.$router.push({
          path: '/checks',
          query: { state: 'auditing' },
        });
      },
      quotaAuditJump() {
        this.$router.push({
          path: '/quota-audit',
        });
      },
      stateFormatter(state) {
        switch (state) {
          case 0: return '待审核';
          case 1: return '审核通过';
          case 2: return '审核未通过';
          default: return '';
        }
      },
    },
    filters: {
      nullFilter(v) {
        return v === null ? 0 : v;
      },
    },
  };
</script>
<style type="text/css">
    .statistic-box {
        height:148px;
        background-color: #f2f2f2;
        border-top: 1px solid #cccccc;
        border-left: 1px solid #cccccc;
    }
    .statistic-box .money-box {
        line-height: 120px;
        text-align: center;
        font-size: 30px;
        font-weight: bolder
    }
    .box-card .item-box{
        height: 100px;
        text-align: center;
    }
    .item-box .num {
        color: red;
        font-weight: bolder;
        font-size: 24px;
        line-height: 60px
    }
</style>