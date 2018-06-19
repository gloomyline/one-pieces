<template>
    <div style="background-color: #f2f2f2;padding: 20px;overflow: auto;">
        <el-button type="success" style="margin-bottom: 5px" @click.native="$router.back(-1)">返回上一页</el-button>

      <!-- begin: 借款人信息 -->
      <el-row class='backStyle' style=''>
          <el-row>
              <el-col :span="24">
                  <div class="basicTitle" style=''> 借款人信息</div>
              </el-col>
          </el-row>
          <el-row>
              <el-col :span="6">
                  <div class="basicStyle" style=''>姓名</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>{{ borrower.real_name }}</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>身份证号</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>{{ borrower.identity_no }}</div>
              </el-col>
          </el-row>
          <el-row>
              <el-col :span="6">
                  <div class="basicStyle" style=''>手机号</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>{{ borrower.mobile }}</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>注册时间</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>{{ borrower.created_at }}</div>
              </el-col>
          </el-row>
          <el-row>
              <el-col :span="6">
                  <div class="basicStyle" style=''>所属地区</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>{{ borrower.live_area }}</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>详细地址</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>{{ borrower.live_addr }}</div>
              </el-col>
          </el-row>
          <el-row>
              <el-col :span="6">
                  <div class="basicStyle" style=''>居住时长</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>{{ borrower.live_time }}</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>性别</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>{{ borrower.sex | sexFilter }}</div>
              </el-col>
          </el-row>
          <el-row>
              <el-col :span="6">
                  <div class="basicStyle" style=''>年龄</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>{{ borrower.age }}</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>工作岗位</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>{{ work.position }}</div>
              </el-col>
          </el-row>
          <el-row>
              <el-col :span="6">
                  <div class="basicStyle" style=''>用户状态</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>{{ borrower.state | userFilter }}</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>学历</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>{{ auth.education }}</div>
              </el-col>
          </el-row>
          <el-row>
              <el-col :span="6">
                  <div class="basicStyle" style=''>成功借款金额(元)</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>{{ borrower.success_amount }}</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>成功借款次数</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>{{ borrower.success_count }}</div>
              </el-col>
          </el-row>
          <el-row>
              <el-col :span="6">
                  <div class="basicStyle" style=''>成功还款金额(元)</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>{{ borrower.success_repay_amount }}</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>成功还款次数</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>{{ borrower.success_repay_count }}</div>
              </el-col>
          </el-row>
          <el-row>
              <el-col :span="6">
                  <div class="basicStyle" style=''>剩余佣金(元)</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>{{ borrower.brokerage_odd }}</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>邀请人数</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>{{ borrower.invite_num }}</div>
              </el-col>
          </el-row>
          <el-row>
              <el-col :span="6">
                  <div class="basicStyle" style=''>信用分</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>{{ borrower.credit_score }}</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''><el-button type="text" icon="el-icon-warning"  @click.native="CreditScoreTips()">信用等级</el-button></div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>
                    <span>{{borrower.credit_level}}</span>
                    <span style="color: limegreen" v-if="borrower.credit_level == ''">暂无</span>
                  </div>
              </el-col>
          </el-row>
          <el-row>
              <el-col :span="6">
                  <div class="basicStyle" style=''>亲签照</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>
                    <el-button type="text" @click.native.click="pictureVisible = true" v-if="borrower.sign_pic !== ''" style="padding: 0;"><img style="width:30px;height:30px;" :src="borrower.sign_pic"></el-button>
                    <i class="el-icon-picture" v-if="borrower.sign_pic == ''"></i>
                  </div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>信用额度</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>{{ borrower.total_quota }}</div>
              </el-col>
          </el-row>
      </el-row>
      <!-- end: 借款人信息 -->

      <!-- begin:身份证验证-->
      <el-row class='backStyle' style=''>
          <el-row>
              <el-col :span="24">
                  <div class="basicTitle" style=''>身份证验证</div>
              </el-col>
          </el-row>
          <el-row>
              <el-col :span="6">
                  <div class="basicStyle" style=''>真实姓名</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>{{ borrower.real_name }}</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>身份证</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>{{ borrower.identity_no }}</div>
              </el-col>
          </el-row>
          <el-row>
              <el-col :span="6">
                  <div class="basicStyle" style=''>校验项</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>真实姓名、身份证号</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>结果</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>
                      <span v-if="auth.is_identity_auth === 1" style="color: limegreen ">均一致</span>
                      <span v-if="auth.is_identity_auth !== 1" style="color: red ">不一致</span>
                  </div>
              </el-col>
          </el-row>
      </el-row>
      <!-- end:身份证验证-->

      <!-- begin:手机三要素 -->
      <el-row class='backStyle' style=''>
          <el-row>
              <el-col :span="24">
                  <div class="basicTitle" style=''>手机三要素</div>
              </el-col>
          </el-row>
          <el-row>
              <el-col :span="6">
                  <div class="basicStyle" style=''>手机号</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>{{ borrower.mobile }}</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>入网时间</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>{{ auth.reg_time }}</div>
              </el-col>
          </el-row>
          <el-row>
              <el-col :span="6">
                  <div class="basicStyle" style=''>校验项</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>真实姓名、身份证号、手机号</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>结果</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>
                      <span style="color: red" v-if="auth.is_phone_auth === 0 ">匹配失败</span>
                      <span style="color: green" v-else-if="auth.is_phone_auth === 1 ">匹配成功</span>
                      <span style="color: limegreen" v-else-if="auth.is_phone_auth === 2 ">模糊匹配成功</span>
                      <span style="color: limegreen" v-else-if="auth.is_phone_auth === 3 ">未知</span>
                      <span style="color: tomato" v-else-if="auth.is_phone_auth === ''">没有手机认证报告数据</span>
                  </div>
              </el-col>
          </el-row>
          <el-row>
              <el-col :span="6">
                  <div class="basicStyle" style=''>手机通话报告</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''><el-button type="text" @click.native="mobileJump(borrower.mobile)">查看</el-button></div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>运营商报告</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''><el-button type="text" @click.native="mobileJump(borrower.mobile)">查看</el-button></div>
              </el-col>
          </el-row>
      </el-row>
      <!--end：手机三要素-->

      <!--begin:银行卡四要素-->
      <el-row class='backStyle' style=''>
          <el-row>
              <el-col :span="24">
                  <div class="basicTitle" style=''>银行卡四要素</div>
              </el-col>
          </el-row>
          <el-row>
              <el-col :span="6">
                  <div class="basicStyle" style=''>开户行</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>{{ bank.bank_name }}</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>银行卡号</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>{{ bank.bank_no }}</div>
              </el-col>
          </el-row>
          <el-row>
              <el-col :span="6">
                  <div class="basicStyle" style=''>校验项</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>真实姓名、身份证、手机号、银行卡号</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>结果</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>
                      <span v-if="bank.state === 'valid'" style="color: limegreen ">均一致</span>
                      <span v-if="bank.state === 'invalid'" style="color: red ">不一致</span>
                  </div>
              </el-col>
          </el-row>
      </el-row>
      <!--end：银行卡四要素-->

      <!--begin:提升额度-->
      <el-row class='backStyle' style=''>
          <el-row>
              <el-col :span="24">
                  <div class="basicTitle" style=''> 提升额度</div>
              </el-col>
          </el-row>
          <el-row>
              <el-col :span="6">
                  <div class="basicStyle" style=''>学历</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>
                      <el-button type="text" size="small" @click.native="handelJumpEdu(borrower.mobile)" v-if="auth.is_edu_auth === 1">查看报告</el-button>
                      <span class="unfilled-red" v-else="auth.is_edu_auth === 0">暂无</span>
                  </div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>淘宝账号</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>
                      <el-button type="text" size="small" @click.native="handelJumpTaobao(borrower.mobile)" v-if="auth.is_taobao_auth === 1">查看报告</el-button>
                      <span class="unfilled-red" v-else="auth.is_taobao_auth === 0">暂无</span>
                  </div>
              </el-col>
          </el-row>
          <el-row>
              <el-col :span="6">
                  <div class="basicStyle" style=''>央行征信</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>
                      <el-button type="text" size="small" @click.native="handelJumpCredit(borrower.mobile)" v-if="auth.is_credit_auth === 1">查看报告</el-button>
                      <span class="unfilled-red" v-else="auth.is_credit_auth === 0">暂无</span>
                  </div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>京东账号</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>
                      <el-button type="text" size="small" @click.native="handleJumpJd(borrower.mobile)" v-if="auth.is_jd_auth === 1">查看报告</el-button>
                      <span class="unfilled-red" v-else="auth.is_jd_auth === 0">暂无</span>
                  </div>
              </el-col>
          </el-row>
          <el-row>
              <el-col :span="6">
                  <div class="basicStyle" style=''>公积金</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>
                      <el-button type="text" size="small" @click.native="handleJumpHouseFund(borrower.mobile)" v-if="auth.is_housefund_auth === 1">查看报告</el-button>
                      <span class="unfilled-red" v-else="auth.is_housefund_auth === 0">暂无</span>
                  </div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>社保</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>
                      <el-button type="text" size="small" @click.native="handleJumpSocialSecurity(borrower.mobile)" v-if="auth.is_socialsecurity_auth === 1">查看报告</el-button>
                      <span class="unfilled-red" v-else="auth.is_socialsecurity_auth === 0">暂无</span>
                  </div>
              </el-col>
          </el-row>
      </el-row>
      <!--end:提升额度-->

       <!-- begin: 工作信息 -->
      <el-row class='backStyle' style=''>
          <el-row>
              <el-col :span="24">
                  <div class="basicTitle" style=''> 工作信息</div>
              </el-col>
          </el-row>
          <el-row>
              <el-col :span="6">
                  <div class="basicStyle" style=''>从事行业</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>{{ work.industry }}</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>工作岗位</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>{{ work.position }}</div>
              </el-col>
          </el-row>
          <el-row>
              <el-col :span="6">
                  <div class="basicStyle" style=''>单位名称</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>{{ work.company_name }}</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>单位所在地</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>{{ work.company_area }}</div>
              </el-col>
          </el-row>
          <el-row>
              <el-col :span="6">
                  <div class="basicStyle" style=''>详细信息</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>{{ work.company_addr }}</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>单位电话</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>{{ work.company_tel }}</div>
              </el-col>
          </el-row>
      </el-row>
      <!-- end: 工作信息 -->

        <!-- begin: 人际关系 -->
      <el-row class='backStyle' style=''>
          <el-row>
              <el-col :span="24">
                  <div class="basicTitle" style=''> 人际关系</div>
              </el-col>
          </el-row>
          <el-row>
              <el-col :span="6">
                  <div class="basicStyle" style=''>与本人关系</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>{{ relation.linkman_relation_fir | relationFilter }}</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>姓名</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>{{ relation.linkman_name_fir }}</div>
              </el-col>
          </el-row>
          <el-row>
              <el-col :span="6">
                  <div class="basicStyle" style=''>电话</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>{{ relation.linkman_tel_fir }}</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''></div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''></div>
              </el-col>
          </el-row>
          <el-row>
              <el-col :span="6">
                  <div class="basicStyle" style=''>与本人关系</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>{{ relation.linkman_relation_sec | relationFilter }}</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>姓名</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>{{ relation.linkman_name_sec }}</div>
              </el-col>
          </el-row>
          <el-row>
              <el-col :span="6">
                  <div class="basicStyle" style=''>电话</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>{{ relation.linkman_tel_sec }}</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''></div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''></div>
              </el-col>
          </el-row>
      </el-row>
      <!-- end: 人际关系 -->

      <!-- begin: 借款信息 -->
      <el-row class='backStyle' style=''>
           <el-row>
              <el-col :span="24">
                <div class="basicTitle" style=''> 借款信息</div>
              </el-col>
           </el-row>
           <el-row>
              <el-col :span="6">
                <div class="basicStyle" style=''>商户名称</div>
              </el-col>
              <el-col :span="6">
                <div class="basicStyle" style=''>{{ loan.shop_name }}</div>
              </el-col>
              <el-col :span="6">
                <div class="basicStyle" style=''>商品名称</div>
              </el-col>
              <el-col :span="6">
                <div class="basicStyle" style=''>{{ loan.shop_prod }}</div>
              </el-col>
           </el-row>
           <el-row>
              <el-col :span="6">
                <div class="basicStyle" style=''>消费类型</div>
              </el-col>
              <el-col :span="6">
                <div class="basicStyle" style=''>{{ loan.type | loanTypeFilter}}</div>
              </el-col>
              <el-col :span="6">
                <div class="basicStyle" style=''>消费用途</div>
              </el-col>
              <el-col :span="6">
                <div class="basicStyle" style=''>{{ loan.use }}</div>
              </el-col>
           </el-row>
           <el-row>
              <el-col :span="6">
                <div class="basicStyle" style=''>项目编号</div>
              </el-col>
              <el-col :span="6">
                <div class="basicStyle" style=''>{{ loan.encoding }}</div>
              </el-col>
              <el-col :span="6">
                <div class="basicStyle" style=''>借款金额(元)</div>
              </el-col>
              <el-col :span="6">
                <div class="basicStyle" style=''>{{ loan.quota }}</div>
              </el-col>
           </el-row>
           <el-row>
              <el-col :span="6">
                <div class="basicStyle" style=''>借款期数(月)</div>
              </el-col>
              <el-col :span="6">
                <div class="basicStyle" style=''>{{ loan.period }}</div>
              </el-col>
              <el-col :span="6">
                <div class="basicStyle" style=''>还款方式</div>
              </el-col>
              <el-col :span="6">
                <div class="basicStyle" style=''>{{ loan.repayment_way | repaymentWayFilter}}</div>
              </el-col>
           </el-row>
           <el-row>
              <el-col :span="6">
                <div class="basicStyle" style=''>年化利率(%)</div>
              </el-col>
              <el-col :span="6">
                <div class="basicStyle" style=''>{{ loan.annualized_interest_rate * 100 }}%</div>
              </el-col>
              <el-col :span="6">
                <div class="basicStyle" style=''>借款利息(元)</div>
              </el-col>
              <el-col :span="6">
                <div class="basicStyle" style=''>{{ loan.interest }}</div>
              </el-col>
           </el-row>
           <el-row>
              <el-col :span="6">
                <div class="basicStyle" style=''>信审费(元)</div>
              </el-col>
              <el-col :span="6">
                <div class="basicStyle" style=''>{{ loan.trial_fee}}</div>
              </el-col>
              <el-col :span="6">
                <div class="basicStyle" style=''>服务费(元)</div>
              </el-col>
              <el-col :span="6">
                <div class="basicStyle" style=''>{{ loan.service_fee}}</div>
              </el-col>
           </el-row>
           <el-row>
              <el-col :span="6">
                <div class="basicStyle" style=''>手续费(元)</div>
              </el-col>
              <el-col :span="6">
                <div class="basicStyle" style=''>{{ loan.poundage_fee}}</div>
              </el-col>
              <el-col :span="6">
                <div class="basicStyle" style=''>放款时间</div>
              </el-col>
              <el-col :span="6">
                <div class="basicStyle" style=''>{{ loan.lending_at }}</div>
              </el-col>
           </el-row>
           <el-row>
              <el-col :span="6">
                <div class="basicStyle" style=''>应还总额(元)</div>
              </el-col>
              <el-col :span="6">
                <div class="basicStyle" style=''>{{ loan.need_amount }}</div>
              </el-col>
              <el-col :span="6">
                <div class="basicStyle" style=''>计划还款时间</div>
              </el-col>
              <el-col :span="6">
                <div class="basicStyle" style=''>{{ loan.planned_repayment_at }}</div>
              </el-col>
           </el-row>
           <el-row>
              <el-col :span="6">
                  <div class="basicStyle" style=''>借款状态</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>{{ loan.state | loanStateFilter }}</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>订单确认时间</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>{{ loan.confirmed_at }}</div>
              </el-col>
           </el-row>
      </el-row>
      <!-- end: 借款信息 -->

      <!-- begin: 还款计划 -->
      <el-row class='backStyle' style=''>
           <el-row>
              <el-col :span="24">
                <div class="basicTitle" style=''> 还款计划</div>
              </el-col>
           </el-row>
           <el-row>
              <el-col :span="2" class="plan_column">
                <div class="basicStyle" style=''>期数</div>
              </el-col>
              <el-col :span="3" class="plan_column">
                <div class="basicStyle" style=''>日期</div>
              </el-col>
              <el-col :span="2" class="plan_column">
                <div class="basicStyle" style=''>月供(元)</div>
              </el-col>
              <el-col :span="2" class="plan_column">
                <div class="basicStyle" style=''>本金(元)</div>
              </el-col>
              <el-col :span="2" class="plan_column">
                <div class="basicStyle" style=''>利息(元)</div>
              </el-col>
              <el-col :span="2" class="plan_column">
                <div class="basicStyle" style=''>信审(元)</div>
              </el-col>
              <el-col :span="2" class="plan_column">
                <div class="basicStyle" style=''>服务费(元)</div>
              </el-col>
              <el-col :span="2" class="plan_column">
                <div class="basicStyle" style=''>手续费(元)</div>
              </el-col>
              <el-col :span="2" class="plan_column">
                <div class="basicStyle" style=''>还款类型</div>
              </el-col>
              <el-col :span="2" class="plan_column">
                <div class="basicStyle" style=''>还款金额</div>
              </el-col>
              <el-col :span="3" class="plan_column">
                <div class="basicStyle" style=''>还款时间</div>
              </el-col>
           </el-row>
           <el-row v-for="p in plan">
              <el-col :span="2" class="plan_column">
                <div class="basicStyle" style=''>{{ p.term }}</div>
              </el-col>
              <el-col :span="3" class="plan_column">
                <div class="basicStyle" style=''>{{ p.planned_repayment_at }}</div>
              </el-col>
              <el-col :span="2" class="plan_column">
                <div class="basicStyle" style=''>{{ p.monthly }}</div>
              </el-col>
              <el-col :span="2" class="plan_column">
                <div class="basicStyle" style=''>{{ p.principal }}</div>
              </el-col>
              <el-col :span="2" class="plan_column">
                <div class="basicStyle" style=''>{{ p.interest_fee }}</div>
              </el-col>
              <el-col :span="2" class="plan_column">
                <div class="basicStyle" style=''>{{ p.trial_fee }}</div>
              </el-col>
              <el-col :span="2" class="plan_column">
                <div class="basicStyle" style=''>{{ p.service_fee }}</div>
              </el-col>
              <el-col :span="2" class="plan_column">
                <div class="basicStyle" style=''>{{ p.poundage_fee }}</div>
              </el-col>
              <el-col :span="2" class="plan_column">
                <div class="basicStyle" style=''>{{ p.repayed_type | repayedTypeFilter }}</div>
              </el-col>
              <el-col :span="2" class="plan_column">
                <div class="basicStyle" style=''>{{ p.repayed_amount }}</div>
              </el-col>
              <el-col :span="3" class="plan_column">
                <div class="basicStyle" style=''>{{ p.repayment_at }}</div>
              </el-col>
           </el-row>
      </el-row>
      <!-- end: 还款计划 -->


      <!-- begin: 初审信息 -->      
      <el-row class='backStyle' style=''>
           <el-row>
              <el-col :span="24">
                <div class="basicTitle" style=''> 初审信息</div>
              </el-col>
           </el-row>
           <el-row>
              <el-col :span="6">
                <div class="basicStyle" style=''>初审人员</div>
              </el-col>
              <el-col :span="6">
                <div class="basicStyle" style=''>{{ checks.preliminary_officer }}</div>
              </el-col>
              <el-col :span="6">
                <div class="basicStyle" style=''>初审意见</div>
              </el-col>
              <el-col :span="6">
                <div class="basicStyle" style=''>{{ checks.preliminary_opinion }}</div>
              </el-col>
           </el-row>
           <el-row>
              <el-col :span="6">
                <div class="basicStyle" style=''>初审时间</div>
              </el-col>
              <el-col :span="6">
                <div class="basicStyle" style=''>{{ checks.check_at }}</div>
              </el-col>
              <el-col :span="6">
                <div class="basicStyle" style=''>初审结果</div>
              </el-col>
              <el-col :span="6">
                <div class="basicStyle" style=''>
                    <span v-if="checks.check_result === 1" style="color: limegreen ">{{ checks.check_result | checkResultFilter }}</span>
                    <span class="unfilled-red" v-if="checks.check_result === 2">{{ checks.check_result | checkResultFilter }}</span>
                    <span v-if="checks.check_result !== 2 && checks.check_result !== 1">{{ checks.check_result | checkResultFilter }}</span>
                </div>
              </el-col>
           </el-row>
      </el-row>
      <!-- end: 初审信息 --> 


      <!-- begin: 电审信息 -->      
      <el-row class='backStyle' style=''>
           <el-row>
              <el-col :span="24">
                <div class="basicTitle" style=''> 电审信息</div>
              </el-col>
           </el-row>
           <el-row>
              <el-col :span="6">
                <div class="basicStyle" style=''>电审人员</div>
              </el-col>
              <el-col :span="6">
                <div class="basicStyle" style=''>{{ checks.preliminary_officer }}</div>
              </el-col>
              <el-col :span="6">
                <div class="basicStyle" style=''>电审意见</div>
              </el-col>
              <el-col :span="6">
                <div class="basicStyle" style=''>{{ checks.tel_opinion }}</div>
              </el-col>
           </el-row>
           <el-row>
              <el-col :span="6">
                <div class="basicStyle" style=''>电审时间</div>
              </el-col>
              <el-col :span="6">
                <div class="basicStyle" style=''>{{ checks.check_at }}</div>
              </el-col>
              <el-col :span="6">
                <div class="basicStyle" style=''>电审结果</div>
              </el-col>
              <el-col :span="6">
                <div class="basicStyle" style=''>
                    <span v-if="checks.tel_result === 1" style="color: limegreen ">{{ checks.tel_result | checkResultFilter }}</span>
                    <span class="unfilled-red" v-if="checks.tel_result === 2">{{ checks.tel_result | checkResultFilter }}</span>
                    <span v-if="checks.tel_result !== 2 && checks.tel_result !== 1">{{ checks.tel_result | checkResultFilter }}</span>
                </div>
              </el-col>
           </el-row>
      </el-row>
      <!-- end: 初审信息 --> 

      <!-- begin: 逾期记录 -->
      <el-row class='backStyle' style=''>
          <el-row>
              <el-col :span="24">
                  <div class="basicTitle" style=''> 逾期记录</div>
              </el-col>
          </el-row>
          <el-row style='margin-top:1px;'>
              <el-col :span="1" class="plan_column">
                <div class="basicStyle" style=''>期数</div>
              </el-col>
              <el-col :span="3" class="plan_column">
                <div class="basicStyle" style=''>日期</div>
              </el-col>
              <el-col :span="2" class="plan_column">
                <div class="basicStyle" style=''>借款金额(元)</div>
              </el-col>
              <el-col :span="2" class="plan_column">
                <div class="basicStyle" style=''>应还金额(元)</div>
              </el-col>
              <el-col :span="2" class="plan_column">
                <div class="basicStyle" style=''>逾期费用(元)</div>
              </el-col>
              <el-col :span="2" class="plan_column">
                <div class="basicStyle" style=''>逾期天数</div>
              </el-col>
              <el-col :span="3" class="plan_column">
                <div class="basicStyle" style=''>开始逾期日期</div>
              </el-col>
              <el-col :span="2" class="plan_column">
                <div class="basicStyle" style=''>是否还款</div>
              </el-col>
              <el-col :span="2" class="plan_column">
                <div class="basicStyle" style=''>已还金额(元)</div>
              </el-col>
              <el-col :span="2" class="plan_column">
                <div class="basicStyle" style=''>剩余未还金额(元)</div>
              </el-col>
              <el-col :span="3" class="plan_column">
                <div class="basicStyle" style=''>还款时间</div>
              </el-col>
          </el-row>
          <el-row v-for="o in overdue">
              <el-col :span="1" class="plan_column">
                <div class="basicStyle" style=''>{{ o.term }}</div>
              </el-col>
              <el-col :span="3" class="plan_column">
                <div class="basicStyle" style=''>{{ o.created_at }}</div>
              </el-col>
              <el-col :span="2" class="plan_column">
                <div class="basicStyle" style=''>{{ o.principal }}</div>
              </el-col>
              <el-col :span="2" class="plan_column">
                <div class="basicStyle" style=''>{{ o.need_amount }}</div>
              </el-col>
              <el-col :span="2" class="plan_column">
                <div class="basicStyle" style=''>{{ o.overdue_fee }}</div>
              </el-col>
              <el-col :span="2" class="plan_column">
                <div class="basicStyle" style=''>{{ o.overdue_day }}</div>
              </el-col>
              <el-col :span="3" class="plan_column">
                <div class="basicStyle" style=''>{{ o.begin_over_at }}</div>
              </el-col>
              <el-col :span="2" class="plan_column">
                <div class="basicStyle" style=''>{{ o.state| repayedStateFilter }}</div>
              </el-col>
              <el-col :span="2" class="plan_column">
                <div class="basicStyle" style=''>{{ o.repayed_amount }}</div>
              </el-col>
              <el-col :span="2" class="plan_column">
                <div class="basicStyle" style=''>{{ o.surplus_amount }}</div>
              </el-col>
              <el-col :span="3" class="plan_column">
                <div class="basicStyle" style=''>{{ o.repayment_at }}</div>
              </el-col>
          </el-row>
      </el-row>
      <!-- end: 逾期记录 -->  

      <!-- begin: 反欺诈信息 -->
      <el-row class='backStyle' style=''>
          <el-row>
              <el-col :span="24">
                  <div class="basicTitle" style=''>反欺诈信息</div>
              </el-col>
          </el-row>
          <el-row>
              <el-col :span="4" class='fraud'>
                  <div class="basicStyle" style=''>更新时间</div>
              </el-col>
              <el-col :span="5" class='fraud'>
                  <div class="basicStyle" style=''>风险码</div>
              </el-col>
              <el-col :span="5" class='fraud'>
                  <div class="basicStyle" style=''><el-button type="text" icon="el-icon-warning"  @click.native="fraudTips()">风险详情</el-button></div>
              </el-col>
              <el-col :span="5" class='fraud'>
                  <div class="basicStyle" style=''>风险等级</div>
              </el-col>
              <el-col :span="5" class='fraud'>
                  <div class="basicStyle" style=''>风险类型</div>
              </el-col>
          </el-row>
          <el-row v-for ='f in fraud'>
              <el-col :span="4" class='fraud'>
                  <div class="basicStyle" style=''>{{ f.updated_at }}</div>
              </el-col>
              <el-col :span="5" class='fraud'>
                  <div class="basicStyle" style=''>{{ f.riskCode }}</div>
              </el-col>
              <el-col :span="5" class='fraud'>
                  <div class="basicStyle" style=''>{{ f.riskCode | fraudDetailFilter}}</div>
              </el-col>
              <el-col :span="5" class='fraud'>
                  <div class="basicStyle" style=''>{{ f.riskCodeValue | fraudLevelFilter }}</div>
              </el-col>
              <el-col :span="5" class='fraud'>
                  <div class="basicStyle" style=''>{{ f.riskCode | fraudTypeFilter }}</div>
              </el-col>
          </el-row>
          <el-row style="border:1px solid #868686; border-top:none;text-align:center; background:white;">
              <div class="basicStyle" style=''><el-button type="text"  @click.native="getFraudMsg()">获取/更新反欺诈信息</el-button></div>
          </el-row>
      </el-row>
      <!-- end: 反欺诈信息 -->

      <!-- begin: 审核意见 -->   
      <el-row class='backStyle auditContent'>
        <span>审核意见</span>
        <el-form :model="auditForm" ref="auditForm" label-width="120px">
          <el-form-item label="复审结果" required>
               <el-radio-group v-model="form.audit_result">
                <el-radio :label="1">通过</el-radio>
                <el-radio :label="0">不通过</el-radio>
              </el-radio-group>
          </el-form-item>
          
          <el-form-item label="复审意见" required>
            <el-input type="textarea"  :rows="2" placeholder="请输入意见内容" v-model="form.audit_opinion" style="width:400px;"></el-input>
          </el-form-item>

          <el-form-item>
            <el-button type="primary" @click="handleSubmit('reviews')">提交</el-button>
            <el-button type="primary" @click="$router.back()">返回</el-button>
          </el-form-item>
        </el-form>
      </el-row>
      <!-- end: 审核意见 -->

      <!--begin: 信用分对照对话框 -->
      <el-dialog  title="小贴士" :visible.sync="tipsVisible"  width="30%">  
      <el-row>
        <el-col :span="24"><div class='tipsCaption'>信用分等级对照表</div></el-col>
      </el-row>
      <el-row>
        <el-col :span="8">
          <div class="tipsTitle" style=''>起始分值</div>
        </el-col>
        <el-col :span="8">
          <div class="tipsTitle" style=''>结束分值</div>
        </el-col>
        <el-col :span="8">
          <div class="tipsTitle" style=''>信用等级</div>
        </el-col>
      </el-row>
      <el-row v-for="level in borrower.grade">
        <el-col :span="8">
          <div class="tipsStyle" style=''>{{ level.start }}</div>
        </el-col>
        <el-col :span="8">
          <div class="tipsStyle" style=''>{{ level.end }}</div>
        </el-col>
        <el-col :span="8">
          <div class="tipsStyle" style=''>{{ level.name }}</div>
        </el-col>
      </el-row>
      <span slot="footer" class="dialog-footer">
        <el-button type="primary" @click="tipsVisible = false">确 定</el-button>
      </span>
     </el-dialog>
     <!--end: 信用分对照对话框 -->

      <!--begin: 风险等级对照对话框 -->
      <el-dialog  title="" :visible.sync="fraudVisible"  width="55%">  
      <el-row>
        <el-col :span="24"><div class='tipsCaption'>反欺诈风险对照表</div></el-col>
      </el-row>
      <el-row>
        <el-col :span="3">
          <div class="tipsTitle" style=''>风险类型</div>
        </el-col>
        <el-col :span="3">
          <div class="tipsTitle" style=''>风险码</div>
        </el-col>
        <el-col :span="3">
          <div class="tipsTitle" style=''>风险详情</div>
        </el-col>
        <el-col :span="4">
          <div class="tipsTitle" style=''>命中时的风险等级</div>
        </el-col>
        <el-col :span="11">
          <div class="tipsTitle" style=''>说明</div>
        </el-col>
      </el-row>
      <el-row class="fraudWrapper">
        <el-row  style='border-bottom:1px solid #868686;'>
          <el-col :span="3" class="fraudType">
            <div style="line-height: 560px;">帐号风险</div>
          </el-col>
          <el-col :span="21">
            <el-row class="fraudDetail">
              <el-col :span="3" class="fraudCell">
                1
              </el-col>
              <el-col :span="4" class="fraudCell">
                信贷中介
              </el-col>
              <el-col :span="4" class="fraudCell">
                <div>1:低风险 <br/> 2:中风险 <br/> 3:高风险</div>
              </el-col>
              <el-col :span="13" class="fraudCell">
                <div>涉嫌从事包装客户资料，伪造客户资料，冒用客户资料，套取机构风险政策等职业的用户或者机构成员</div>
              </el-col>
            </el-row>
            <el-row class="fraudDetail">
              <el-col :span="3" class="fraudCell">
                2
              </el-col>
              <el-col :span="4" class="fraudCell">
                不法分子
              </el-col>
              <el-col :span="4" class="fraudCell">
                <div>1:低风险 <br/> 2:中风险 <br/> 3:高风险</div>
              </el-col>
              <el-col :span="13" class="fraudCell">
                <div>互联网行为涉嫌色情、赌博、毒品等违法行为</div>
              </el-col>
            </el-row>
            <el-row class="fraudDetail">
              <el-col :span="3" class="fraudCell">
                3
              </el-col>
              <el-col :span="4" class="fraudCell">
                虚假资料
              </el-col>
              <el-col :span="4" class="fraudCell">
                <div>1:低风险 <br/> 2:中风险 <br/> 3:高风险</div>
              </el-col>
              <el-col :span="13" class="fraudCell">
                <div>输入信息和虚假身份信息提交强相关，或者有恶意申请/操作记录，或者个人信息疑似泄漏、冒用、伪造等</div>
              </el-col>
            </el-row>
            <el-row class="fraudDetail">
              <el-col :span="3" class="fraudCell">
                4
              </el-col>
              <el-col :span="4" class="fraudCell">
                羊毛党
              </el-col>
              <el-col :span="4" class="fraudCell">
                <div>1:低风险 <br/> 2:中风险 <br/> 3:高风险</div>
              </el-col>
              <el-col :span="13" class="fraudCell">
                <div>在网贷、电商、O2O 等平台有薅羊毛行为的用户</div>
              </el-col>
            </el-row>
            <el-row class="fraudDetail">
              <el-col :span="3" class="fraudCell">
                5
              </el-col>
              <el-col :span="4" class="fraudCell">
                身份认证失败
              </el-col>
              <el-col :span="4" class="fraudCell">
                <div><br/> 2:中风险 <br/></div>
              </el-col>
              <el-col :span="13" class="fraudCell">
                <div>身份信息对（身份证、手机号、姓名）涉嫌伪造</div>
              </el-col>
            </el-row>
            <el-row class="fraudDetail">
              <el-col :span="3" class="fraudCell">
                6
              </el-col>
              <el-col :span="4" class="fraudCell">
                疑似恶意欺诈
              </el-col>
              <el-col :span="4" class="fraudCell">
                <div>1:低风险 <br/> 2:中风险 <br/> 3:高风险</div>
              </el-col>
              <el-col :span="13" class="fraudCell">
                <div>存在骗贷行为</div>
              </el-col>
            </el-row>
            <el-row class="fraudDetail">
              <el-col :span="3" class="fraudCell">
                7
              </el-col>
              <el-col :span="4" class="fraudCell">
                失信名单
              </el-col>
              <el-col :span="4" class="fraudCell">
                <div><br/> 3:高风险 <br/></div>
              </el-col>
              <el-col :span="13" class="fraudCell">
                <div>失信名单</div>
              </el-col>
            </el-row>
            <el-row class="fraudDetail">
              <el-col :span="3" class="fraudCell">
               8
              </el-col>
              <el-col :span="4" class="fraudCell">
                异常支付行为
              </el-col>
              <el-col :span="4" class="fraudCell">
                <div>1:低风险 <br/> 2:中风险 <br/> 3:高风险</div>
              </el-col>
              <el-col :span="13" class="fraudCell">
                <div>支付行为异常包括支付频次、额度、场景等方面有过异常的</div>
              </el-col>
            </el-row>
          </el-col>
        </el-row>
        <el-row>
          <el-col :span="3" class="fraudType">
            <div style="line-height: 140px;">异常环境</div>
          </el-col>
          <el-col :span="21">
            <el-row class="fraudDetail">
              <el-col :span="3" class="fraudCell">
                301
              </el-col>
              <el-col :span="4" class="fraudCell">
                恶意环境
              </el-col>
              <el-col :span="4" class="fraudCell">
                <div>1:低风险 <br/> 2:中风险 <br/> 3:高风险</div>
              </el-col>
              <el-col :span="13" class="fraudCell">
                <div>设备和IP命中黑数据库，包括使用虚拟机、代理设备、代理 IP、猫池等。</div>
              </el-col>
            </el-row>
            <el-row class="fraudDetail">
              <el-col :span="3" class="fraudCell">
                503
              </el-col>
              <el-col :span="4" class="fraudCell">
                其他异常行为
              </el-col>
              <el-col :span="4" class="fraudCell">
                <div>1:低风险 <br/> 2:中风险 <br/> 3:高风险</div>
              </el-col>
              <el-col :span="13" class="fraudCell">
                <div>输入信息和以下高风险可能性关联度较高：被盗风险较高、社交圈子不固定、地理圈子变化较大</div>
              </el-col>
            </el-row>
          </el-col>
        </el-row>
      </el-row>
      <span slot="footer" class="dialog-footer">
        <el-button type="primary" @click="fraudVisible = false">确 定</el-button>
      </span>
     </el-dialog>
     <!--end: 风险等级对照对话框 -->
    
    <!--图片放大：begin-->
    <el-dialog :visible.sync="pictureVisible" align="center">
      <img :src="borrower.sign_pic" alt="亲签照" v-if="borrower.sign_pic !== ''">
      <span v-if="borrower.sign_pic === ''">未上传亲签照</span>
    </el-dialog>
    <!--图片放大：end-->     
    </div>
</template>
<script>
import apiBase from '../../apiBase';
import { getJsonAndCheckSuccess, reportErrorMessage } from '../../ApiUtil';

export default {
  name: 'auditForm',
  data() {
    return {
      id: this.$route.params.id,
      form: {
        audit_opinion: '',
      },
      borrower: {},
      auth: {},
      work: {},
      relation: {},
      bank: {},
      loan: {},
      checks: {},
      reviews: {},
      repayments: {},
      overdue: {},
      plan: {},
      tipsVisible: false,
      fraudVisible: false,
      pictureVisible: false,
    };
  },
  mounted() {
    this.$http.get(`${apiBase}loan-detail/${this.id}`)
        .then(getJsonAndCheckSuccess)
        .then((json) => {
          this.borrower = json.results.borrower;
          this.auth = json.results.auth;
          this.work = json.results.work;
          this.relation = json.results.relation;
          this.bank = json.results.bank;
          this.loan = json.results.loan;
          this.checks = json.results.checks;
          this.reviews = json.results.reviews;
          this.repayments = json.results.repayments;
          this.overdue = json.results.overdue;
          this.plan = json.results.plan;
          this.fraud = json.results.fraud;
        }).catch(reportErrorMessage(this));
  },
  methods: {
    handleJump(id) {
      this.$router.push({
        path: `/checks-audit/${id}`,
      });
    },
    mobileJump(v) {
      this.$router.push({
        path: '/auth-mobile',
        query: { mobile: v },
      });
    },
    handleSubmit(state) {
      this.form.current_state = state;
      this.$http.put(`${apiBase}set-loan-state/${this.id}`, this.form)
          .then(getJsonAndCheckSuccess)
          .then(() => {
            this.$message({
              type: 'success',
              message: '审核成功！',
            });
            this.$router.push({ path: '/reviews' });
          }).catch(reportErrorMessage(this));
    },
    handelJumpEdu(v) { // 学历认证
      this.$router.push({
        path: '/auth-edu',
        query: { mobile: v },
      });
    },
    handelJumpTaobao(v) { // 淘宝
      this.$router.push({
        path: '/auth-taobao',
        query: { mobile: v },
      });
    },
    handelJumpJd(v) { // 京东
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
    handleJumpSocialSecurity(v) { // 社保认证
      this.$router.push({
        path: '/auth-social-security',
        query: { mobile: v },
      });
    },
    handleJumpHouseFund(v) { // 公积金
      this.$router.push({
        path: '/auth-house-fund',
        query: { mobile: v },
      });
    },
    handleJumpCredit(v) { // 央行征信
      this.$router.push({
        path: '/auth-credit',
        query: { mobile: v },
      });
    },
    CreditScoreTips() {
      this.tipsVisible = true;
    },
    fraudTips() {
      this.fraudVisible = true;
    },
    getFraudMsg() {
      const params = {
        user_id: this.borrower.user_id,
      };
      if (this.fraud[0]) {
        this.$http.post(`${apiBase}update-anti-fraud`, params)
          .then(getJsonAndCheckSuccess)
          .then((json) => {
            this.fraud = json.results.fraud;
          }).catch(reportErrorMessage(this));
      } else {
        this.$http.post(`${apiBase}create-anti-fraud`, params)
        .then(getJsonAndCheckSuccess)
        .then((json) => {
          this.fraud = json.results.fraud;
        }).catch(reportErrorMessage(this));
      }
    },
  },
  filters: {
    userFilter(val) { // 用户状态
      switch (val) {
        case 1 : return '注册未申请';
        case 2 : return '正常';
        case 3 : return '逾期用户';
        case 4 : return '黑名单';
        default: return '非法用户';
      }
    },
    loanTypeFilter(val) { // 借款类型
      switch (val) {
        case '1':
        case 1 : return '现金分期';
        case 2 : return '消费分期';
        default: return '其他方式';
      }
    },
    repaymentWayFilter(val) { // 还款方式
      switch (val) {
        case '1':
        case 1 : return '等本等息';
        default: return '其他方式';
      }
    },
    loanStateFilter(val) { // 借款状态
      switch (val) {
        case 'auditing' : return '待初审';
        case 'audit_failure' : return '初审失败';
        case 'audit_success' : return '初审成功';
        case 'reviewing' : return '待复审';
        case 'review_failure' : return '复审失败';
        case 'review_success' : return '复审成功';
        case 'granting' : return '放款中';
        case 'repaying' : return '还款中';
        case 'finished' : return '已完成';
        case 'overdue' : return '逾期';
        default: return '非法状态';
      }
    },
    checkResultFilter(val) { // 初审结果
      switch (val) {
        case 0 : return '待审核';
        case 1: return '通过';
        case 2 : return '不通过';
        default: return '非法状态';
      }
    },
    sexFilter(v) { // 性别
      switch (v) {
        case 0:
          return '未知';
        case 1:
          return '男';
        case 2:
          return '女';
        default:
          return '';
      }
    },
    relationFilter(v) { // 关系
      switch (v) {
        case '0' : return '同事';
        case '1' : return '兄弟';
        case '2' : return '父母';
        case '3' : return '姐妹';
        case '4' : return '朋友';
        default : return '';
      }
    },
    repayedTypeFilter(v) { // 还款类型
      switch (v) {
        case 0 : return '未还款';
        case 1 : return '正常还款';
        case 2 : return '提前还款';
        default : return '';
      }
    },
    repayedStateFilter(v) { // 还款类型
      switch (v) {
        case 'repaying' :
        case 'overdue' : return '否';
        case 'finished' : return '是';
        default : return '';
      }
    },
    fraudDetailFilter(v) {
      switch (v) {
        case 1 : return '信贷中介';
        case 2 : return '不法分子';
        case 3 : return '虚假资料';
        case 4 : return '羊毛党';
        case 5 : return '身份认证失败';
        case 6 : return '疑似恶意欺诈';
        case 7 : return '失信名单';
        case 8 : return '异常支付行为';
        case 301 : return '恶意环境';
        case 503 : return '其他异常行为';
        default : return '';
      }
    },
    fraudLevelFilter(v) {
      switch (v) {
        case 1 : return '低风险';
        case 2 : return '中风险';
        case 3 : return '高风险';
        default : return '';
      }
    },
    fraudTypeFilter(v) {
      switch (v) {
        case 1 :
        case 2 :
        case 3 :
        case 4 :
        case 5 :
        case 6 :
        case 7 :
        case 8 : return '账号风险';
        case 301 :
        case 503 : return '异常环境';
        default : return '';
      }
    },
  },
};
</script>

<style scoped>
  .backStyle{
   background:#F2F2F2;
   min-width:1300px;
  }
  .el-row .el-col div.basicStyle{
      min-height:41px;
      margin:auto;
      text-align:center;
      line-height:40px;
      border:1px solid #868686; 
      border-top:none;     
  }
  .backStyle .el-row:nth-child(2){
      border-top:1px solid #868686;    
  }
  .el-row .el-col-6:nth-child(odd) div.basicStyle, .el-row .el-col-4:nth-child(odd) div.basicStyle, .el-row .el-col-custmize:nth-child(odd) div.basicStyle, .el-row .plan_column:nth-child(odd) div.basicStyle, .el-row .fraud:nth-child(odd) div.basicStyle{
      background:#ffffff;
    }
  .el-row .el-col-6:nth-child(even) div.basicStyle, .el-row .el-col-4:nth-child(even) div.basicStyle, .el-row .el-col-custmize:nth-child(even) div.basicStyle, .el-row .plan_column:nth-child(even) div.basicStyle, .el-row .fraud:nth-child(even) div.basicStyle{
     background:#E4E4E4;
  }
  .basicTitle{
     background:#797979;
     height:55px;
     margin-top:15px;
     margin-bottom:15px;
     color:white;
     text-indent:15px;
     line-height:55px;
     font-weight:bold;
     font-size:16px;
  }
  .el-row .el-col-6:not(:first-child) div.basicStyle, .el-row .el-col-4:not(:first-child) div.basicStyle, .el-row .el-col-custmize:not(:first-child) div.basicStyle, .el-row .plan_column:not(:first-child) div.basicStyle, .el-row .fraud:not(:first-child) div.basicStyle{
    border-left:none;
  }
  .el-row .el-col-custmize{
    width: 10%;
    float: left;
    box-sizing: border-box;
    padding:0;
  }
  .auditContent{
      border:1px solid #868686; 
      border-top:none;   
  }
  .auditContent span{
     margin-left:15px;
     line-height:55px;
     font-weight:bold;
     font-size:22px;
  }
  .tips span {
    display: table-cell;
    width: 33.33%;
    float: left;
  }
  .tipsTitle {
     background:#797979;
     height:55px;
     color:white;
     text-align:center;
     line-height:55px;
     font-weight:bold;
     font-size:16px;
  }
  .tipsStyle {
    background:#F2F2F2;
    min-height:41px;
    margin:auto;
    text-align:center;
    line-height:40px;
    border:1px solid #868686; 
    border-left:none;
    border-top:none;
  }
  .el-col-8:nth-child(1) .tipsStyle{
     border-left:1px solid #868686; 
  }
  .tipsStyle .el-row:nth-child(2){
      border-top:1px solid #868686;    
  }
  .tipsCaption {
    font-size: 24px;
    text-align: center;
    margin-top:-20px;
  }
  .fraudWrapper {
     background: #F2F2F2;
     border:1px solid #868686;
     border-top:none; 
  }
  .fraudDetail {
    border-left:1px solid #868686;
    border-bottom:1px solid #868686;
  }
  .fraudDetail:last-child {
    border-bottom:none;
  }
  .fraudCell {
    text-align: center;
    min-height: 70px;
    border-right: 1px solid #868686;
  }
  .fraudCell:last-child {
    border-right:none;
  }
  .fraudCell:first-child, .fraudCell:nth-child(2) {
    line-height: 70px;
  }
  .fraudType {
    text-align: center;
  }
</style>