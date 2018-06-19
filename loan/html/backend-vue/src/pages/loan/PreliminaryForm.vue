<template>
	  <div>
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
                      <div class="basicStyle" style=''>信用等级</div>
                  </el-col>
                  <el-col :span="6">
                      <div class="basicStyle" style=''><span style="color: limegreen">暂无</span></div>
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
                          <el-button type="success" size="small" @click.native="handelJumpEdu(borrower.mobile)" v-if="auth.is_edu_auth === 1">查看报告</el-button>
                          <span class="unfilled-red" v-else="auth.is_edu_auth === 0">未填写</span>
                      </div>
                  </el-col>
                  <el-col :span="6">
                      <div class="basicStyle" style=''>淘宝账号</div>
                  </el-col>
                  <el-col :span="6">
                      <div class="basicStyle" style=''>
                          <el-button type="success" size="small" @click.native="handelJumpTaobao(borrower.mobile)" v-if="auth.is_taobao_auth === 1">查看报告</el-button>
                          <span class="unfilled-red" v-else="auth.is_taobao_auth === 0">未填写</span>
                      </div>
                  </el-col>
              </el-row>
              <el-row>
                  <el-col :span="6">
                      <div class="basicStyle" style=''>常用信用卡</div>
                  </el-col>
                  <el-col :span="6">
                      <div class="basicStyle" style=''>{{ auth.bankcard }}</div>
                  </el-col>
                  <el-col :span="6">
                      <div class="basicStyle" style=''>京东账号</div>
                  </el-col>
                  <el-col :span="6">
                      <div class="basicStyle" style=''>
                          <el-button type="success" size="small" @click.native="handelJumpJd(borrower.mobile)" v-if="auth.is_jd_auth === 1">查看报告</el-button>
                          <span class="unfilled-red" v-else="auth.is_jd_auth === 0">未填写</span>
                      </div>
                  </el-col>
              </el-row>
              <el-row>
                  <el-col :span="6">
                      <div class="basicStyle" style=''>微信账号</div>
                  </el-col>
                  <el-col :span="6">
                      <div class="basicStyle" style=''>{{ auth.wechat }}</div>
                  </el-col>
                  <el-col :span="6">
                      <div class="basicStyle" style=''>QQ账号</div>
                  </el-col>
                  <el-col :span="6">
                      <div class="basicStyle" style=''>{{ auth.qq }}</div>
                  </el-col>
              </el-row>
              <el-row>
                  <el-col :span="6">
                      <div class="basicStyle" style=''>网银流水</div>
                  </el-col>
                  <el-col :span="6">
                      <div class="basicStyle" style=''>
                          <el-button type="success" size="small" @click.native="handleJumpEbank(borrower.mobile)" v-if="auth.is_ebank_auth === 1">查看报告</el-button>
                          <span class="unfilled-red" v-else="auth.is_ebank_auth === 0">未填写</span>
                      </div>
                  </el-col>
                  <el-col :span="6">
                      <div class="basicStyle" style=''>信用卡账单</div>
                  </el-col>
                  <el-col :span="6">
                      <div class="basicStyle" style=''>
                          <el-button type="success" size="small" @click.native="handleJumpBill(borrower.mobile)" v-if="auth.is_bill_auth === 1">查看报告</el-button>
                          <span class="unfilled-red" v-else="auth.is_bill_auth === 0">未填写</span>
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

          <!-- begin: 逾期记录 -->
          <el-row class='backStyle' style=''>
              <el-row>
                  <el-col :span="24">
                      <div class="basicTitle" style=''> 逾期记录</div>
                  </el-col>
              </el-row>
              <el-row>
                  <el-row style="height: 41px;overflow: hidden">
                      <el-col class='el-col-custmize' style="width: 24%">
                          <div class="basicStyle">项目编号</div>
                      </el-col>
                      <el-col class='el-col-custmize' style="">
                          <div class="basicStyle">借款金额(元)</div>
                      </el-col>
                      <el-col class='el-col-custmize' style="">
                          <div class="basicStyle">应还金额(元)</div>
                      </el-col>
                      <el-col class='el-col-custmize' style="">
                          <div class="basicStyle">逾期费用(元)</div>
                      </el-col>
                      <el-col class='el-col-custmize' style="width: 8%">
                          <div class="basicStyle">逾期天数</div>
                      </el-col>
                      <el-col class='el-col-custmize'>
                          <div class="basicStyle">开始逾期日期</div>
                      </el-col>
                      <el-col class='el-col-custmize' style="width: 8%">
                          <div class="basicStyle">是否还款</div>
                      </el-col>
                      <el-col class='el-col-custmize' style="">
                          <div class="basicStyle">已还金额(元)</div>
                      </el-col>
                      <el-col class='el-col-custmize' style="">
                          <div class="basicStyle">还款时间</div>
                      </el-col>
                  </el-row>

                  <el-row v-for="over of overdue" style="height: 41px;overflow: hidden ;border-bottom: 1px solid #868686;border-top: none">
                      <el-row style="border-bottom: 1px solid #868686">
                          <el-col class='el-col-custmize' style="width: 24%">
                              <div class="basicStyle" style='border-bottom:none'>{{ over.encoding }}</div>
                          </el-col>
                          <el-col class='el-col-custmize'>
                              <div class="basicStyle" style='border-bottom:none'>{{ over.quota }}</div>
                          </el-col>
                          <el-col class='el-col-custmize'>
                              <div class="basicStyle"style='border-bottom:none'>{{ over.need_amount }}</div>
                          </el-col>
                          <el-col class='el-col-custmize'>
                              <div class="basicStyle" style='border-bottom:none'>{{ over.overdue }}</div>
                          </el-col>
                          <el-col class='el-col-custmize' style="width: 8%">
                              <div class="basicStyle" style='border-bottom:none'>{{ over.over_day }}</div>
                          </el-col>
                          <el-col class='el-col-custmize'>
                              <div class="basicStyle" style='border-bottom:none'>{{ over.begin_over_at}}</div>
                          </el-col>
                          <el-col class='el-col-custmize' style="width: 8%">
                              <div class="basicStyle" style='border-bottom:none'>{{ over.state }}</div>
                          </el-col>
                          <el-col class='el-col-custmize'>
                              <div class="basicStyle" style='border-bottom:none'>{{ over.repayed_amount }}</div>
                          </el-col>
                          <el-col class='el-col-custmize'>
                              <div class="basicStyle" style='height: 41px;overflow: hidden;border-bottom: none'>{{ over.repayment_at }}</div>
                          </el-col>
                      </el-row>

                  </el-row>
              </el-row>
          </el-row>
          <!-- end: 逾期记录 -->

      <!-- begin: 借款信息 -->
      <el-row class='backStyle' style=''>
           <el-row>
              <el-col :span="24">
                <div class="basicTitle" style=''> 借款信息</div>
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
                <div class="basicStyle" style=''>借款金额</div>
              </el-col>
              <el-col :span="6">
                <div class="basicStyle" style=''>{{ loan.quota }}</div>
              </el-col>
           </el-row>
           <el-row>
              <el-col :span="6">
                <div class="basicStyle" style=''>借款期限(天)</div>
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
                <div class="basicStyle" style=''>{{ loan.annualized_interest_rate * 100 }}</div>
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
                <div class="basicStyle" style=''>信审费(元)</div>
              </el-col>
              <el-col :span="6">
                <div class="basicStyle" style=''>{{ loan.trial_rate  * loan.quota}}</div>
              </el-col>
              <el-col :span="6">
                <div class="basicStyle" style=''>服务费(元)</div>
              </el-col>
              <el-col :span="6">
                <div class="basicStyle" style=''>{{ loan.service_rate  * loan.quota}}</div>
              </el-col>
           </el-row>
           <el-row>
              <el-col :span="6">
                <div class="basicStyle" style=''>手续费(元)</div>
              </el-col>
              <el-col :span="6">
                <div class="basicStyle" style=''>{{ loan.poundage * loan.quota}}</div>
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
                <div class="basicStyle" style=''>实际还款时间</div>
              </el-col>
              <el-col :span="6">
                <div class="basicStyle" style=''>{{ loan.repayment_at }}</div>
              </el-col>
              <el-col :span="6">
                <div class="basicStyle" style=''>实际还款金额(元)</div>
              </el-col>
              <el-col :span="6">
                <div class="basicStyle" style=''>{{ loan.repayed_amount }}</div>
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
                  <div class="basicStyle" style=''>还款渠道</div>
              </el-col>
              <el-col :span="6">
                  <div class="basicStyle" style=''>{{ loan.repayment_channel }}</div>
              </el-col>
          </el-row>
      </el-row>
      <!-- end: 借款信息 -->
 
       <!-- begin: 审核意见 -->
      <el-row class='backStyle auditContent'>
        <span>审核意见</span>
        <el-form :model="auditForm" ref="auditForm" label-width="120px">
          <el-form-item label="借款确认" required>
            <label>本次借款金额为 <a>{{ loan.quota }}</a> 元，借款期限 <a>{{ loan.period }}</a> 天，借款息费为 <a>{{ loan.interest }}</a> 元</label>
          </el-form-item>
          <el-form-item label="初审结果" required>
               <el-radio-group v-model="form.audit_result">
                <el-radio :label="1">通过</el-radio>
                <el-radio :label="0">不通过</el-radio>
              </el-radio-group>
          </el-form-item>
          
          <el-form-item label="初审意见" required>
            <el-input type="textarea"  :rows="2" placeholder="请输入意见内容" v-model="form.audit_opinion" style="width:400px;"></el-input>
          </el-form-item>

          <el-form-item>
            <el-button type="primary" @click="handleSubmit('checks')">提交</el-button>
            <el-button type="primary" @click="$router.back()">返回</el-button>
          </el-form-item>
        </el-form>
      </el-row>
      <!-- end: 审核意见 -->
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
        audit_result: 1,
      },
      borrower: {
        real_name: '',
        identity_no: '',
        mobile: '',
        created_at: '',
        live_area: '',
        live_addr: '',
        live_time: '',
        sex: '',
        age: '',
        linkman_at: '',
        state: '',
        success_count: '',
        success_amount: '',
        success_repay_count: '',
        success_repay_amount: '',
        invite_num: '',
        brokerage_odd: '',
        position: '',
        bio_assay: '',
        credit_report: '',
      },
      auth: {
        qq: '',
        wechat: '',
        education: '',
        zhima_credit: '',
        carriers: '',
        phone_book: '',
        reg_time: '',
        is_jd_auth: '',
        is_taobao_auth: '',
        is_edu_auth: '',
        is_identity_auth: '',
        is_phone_auth: '',
      },
      work: {
        industry: '',
        position: '',
        company_name: '',
        company_area: '',
        company_addr: '',
        company_tel: '',
      },
      relation: {
        linkman_relation_fir: '',
        linkman_name_fir: '',
        linkman_tel_fir: '',
        linkman_relation_sec: '',
        linkman_name_sec: '',
        linkman_tel_sec: '',

      },
      bank: {
        bank_name: '',
        bank_addr: '',
        bank_no: '',
        reserved_mobile: '',
      },
      loan: {
        encoding: '',
        quota: '',
        period: '',
        repayment_way: '',
        annualized_interest_rate: '',
        lending_at: '',
        trial_rate: '',
        service_rate: '',
        poundage: '',
        interest: '',
        need_amount: '',
        planned_repayment_at: '',
        repayment_at: '',
        repayed_amount: '',
      },
      checks: {
        preliminary_officer: '',
        preliminary_opinion: '',
        check_at: '',
        check_result: '',
      },
      reviews: {
        review_officer: '',
        review_opinion: '',
        review_at: '',
        review_result: '',
      },
      repayment: {
        encoding: '',
        need_amount: '',
        repayment_way: '',
        repayment_channel: '',
        state: '',
        repayment_at: '',
      },
      overdue: {
        encoding: '',
        quota: '',
        need_amount: '',
        overdue: '',
        over_day: '',
        begin_over_at: '',
        state: '',
        repayed_amount: '',
        surplus_refund_amount: '',
        repayment_at: '',
      },
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
        }).catch(reportErrorMessage(this));
  },
  methods: {
    mobileJump(v) {
      this.$router.push({
        path: '/auth-mobile',
        query: { mobile: v },
      });
    },
    handleJump(id) {
      this.$router.push({
        path: `/checks-audit/${id}`,
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
            this.$router.push({ path: '/checks' });
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
    repaymentWayFilter(val) { // 还款方式
      switch (val) {
        case '1':
        case 1 : return '到期本息';
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
  },
};
</script>
<style scoped>
  .backStyle{
   background:#F2F2F2;
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
  .el-row .el-col-6:nth-child(odd) div.basicStyle, .el-row .el-col-4:nth-child(odd) div.basicStyle, .el-row .el-col-custmize:nth-child(odd) div.basicStyle{
      background:#ffffff;
    }
  .el-row .el-col-6:nth-child(even) div.basicStyle, .el-row .el-col-4:nth-child(even) div.basicStyle, .el-row .el-col-custmize:nth-child(even) div.basicStyle{
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
  .el-row .el-col-6:not(:first-child) div.basicStyle, .el-row .el-col-4:not(:first-child) div.basicStyle, .el-row .el-col-custmize:not(:first-child) div.basicStyle{
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
</style>