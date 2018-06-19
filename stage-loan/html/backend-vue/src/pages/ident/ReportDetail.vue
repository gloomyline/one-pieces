<template>
  <el-row>
    <el-col>
      <el-button type="info" style="margin-bottom: 5px" @click.native="$router.back(-1)">返回上一页</el-button>
      <el-button :class="{'el-button--success':btnReportType}"  @click.native="handleReport">运营商报告</el-button>
      <el-button  :class="{'el-button--success':btnDataType}"  @click.native="handleDataContent">运营商原始数据</el-button>
    </el-col>
    <div v-show="reportShow">
      <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">报告情况</div></el-col>
      <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
        <el-col :span="3">数据来源</el-col><el-col :span="9" v-text="report.dataSource ? report.dataSource:'未知'"></el-col>
        <el-col :span="3">报告时间</el-col><el-col :span="9" v-text="report.reportTime ? report.reportTime:'未知'"></el-col>
      </el-col>
      <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
        <el-col :span="3">报告编号</el-col><el-col :span="9" v-text="report.reportNo ? report.reportNo:'未知'"></el-col>
      </el-col>

      <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">基本信息</div></el-col>
      <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
        <el-col :span="3">姓名</el-col><el-col :span="9" v-text="basicInfo.name ? basicInfo.name:'未知'"></el-col>
        <el-col :span="3">身份证号</el-col><el-col :span="9" v-text="basicInfo.identityNo ? basicInfo.identityNo:'未知'"></el-col>
      </el-col>
      <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
        <el-col :span="3">性别</el-col><el-col :span="9" v-text="basicInfo.gender ? basicInfo.gender:'未知'"></el-col>
        <el-col :span="3">年龄</el-col><el-col :span="9" v-text="basicInfo.age ? basicInfo.age:'未知'"></el-col>
      </el-col>
      <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
        <el-col :span="3">手机号</el-col><el-col :span="9" v-text="basicInfo.mobile ? basicInfo.mobile:'未知'"></el-col>
        <el-col :span="3">入网时间</el-col><el-col :span="9" v-text="basicInfo.regTime ? basicInfo.regTime:'未知'"></el-col>
      </el-col>
      <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
        <el-col :span="3">出生地</el-col><el-col :span="9" v-text="basicInfo.nativeAddress ? basicInfo.nativeAddress:'未知'"></el-col>
      </el-col>

      <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">紧急联系人信息</div></el-col>
      <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
        <el-col :span="3">姓名</el-col><el-col :span="9" v-text="contactInfo.name ? contactInfo.name:'无'"></el-col>
        <el-col :span="3">手机号</el-col><el-col :span="9" v-text="contactInfo.mobile ? contactInfo.mobile:'无'"></el-col>
      </el-col>
      <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
        <el-col :span="3">身份证号</el-col><el-col :span="9" v-text="contactInfo.identityNo ? contactInfo.identityNo:'无'"></el-col>
        <el-col :span="3">与本人关系</el-col><el-col :span="9" v-text="contactInfo.relationship ? contactInfo.relationship:'无'"></el-col>
      </el-col>
      <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
        <el-col :span="3">通话次数</el-col><el-col :span="9" v-text="contactInfo.callCnt ? contactInfo.callCnt:'无'"></el-col>
        <el-col :span="3">通话时长（s）</el-col><el-col :span="9" v-text="contactInfo.callTime ? contactInfo.callTime:'无'"></el-col>
      </el-col>
      <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
        <el-col :span="3">通话频度排名</el-col><el-col :span="9" v-text="contactInfo.callRank ? contactInfo.callRank:'无'"></el-col>
        <el-col :span="3">是否命中风险清单</el-col><el-col :span="9" v-text="contactInfo.isHitRiskList == 1 ? '命中':'未命中'"></el-col>
      </el-col>

      <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">关联信息</div></el-col>
      <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
        <el-col :span="3">关联身份证信息</el-col><el-col :span="9" v-text="relationInfo.identiyNos ? relationInfo.identiyNos:'无'"></el-col>
        <el-col :span="3">关联手机号信息</el-col><el-col :span="9" v-text="relationInfo.mobiles ? relationInfo.mobiles:'无'"></el-col>
      </el-col>
      <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
        <el-col :span="3">关联家庭地址信息</el-col><el-col :span="9" v-text="relationInfo.homeAddresses ? relationInfo.homeAddresses:'无'"></el-col>
      </el-col>

      <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">用户画像</div></el-col>
      <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px;padding-right:0px;">
        <el-col :span="3">风险概况</el-col>
        <el-col :span="21" style="margin-top: -10px;">
              <el-col :span="24" style="border-bottom: #bcbcbc solid 1px">
                 <el-col :span="3">命中风险清单次数</el-col><el-col :span="9" v-text="personas.riskProfile.riskListCnt ? personas.riskProfile.riskListCnt:'无'"></el-col>
                 <el-col :span="3">信贷逾期次数</el-col><el-col :span="9" v-text="personas.riskProfile.overdueLoanCnt ? personas.riskProfile.overdueLoanCnt:'无'"></el-col>
              </el-col>
              <el-col :span="24" style="">
                 <el-col :span="3">多头借贷次数</el-col><el-col :span="9" v-text="personas.riskProfile.multiLendCnt ? personas.riskProfile.multiLendCnt:'无'"></el-col>
                 <el-col :span="3">风险通话次数</el-col><el-col :span="9" v-text="personas.riskProfile.riskCallCnt ? personas.riskProfile.riskCallCnt:'无'"></el-col>
              </el-col>
        </el-col>
      </el-col>
      <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px;padding-right:0px;">
        <el-col :span="3">社交概况</el-col>
        <el-col :span="21" style="margin-top: -10px;">
              <el-col :span="24" style="border-bottom: #bcbcbc solid 1px">
                 <el-col :span="3">最常联系人区域</el-col><el-col :span="9" v-text="personas.socialContactProfile.freContactArea ? personas.socialContactProfile.freContactArea:'无'"></el-col>
                 <el-col :span="3">联系人号码总数</el-col><el-col :span="9" v-text="personas.socialContactProfile.contactNumCnt ? personas.socialContactProfile.contactNumCnt:'无'"></el-col>
              </el-col>
              <el-col :span="24" style="">
                 <el-col :span="3">互通号码数</el-col><el-col :span="9" v-text="personas.socialContactProfile.interflowContactCnt ? personas.socialContactProfile.interflowContactCnt:'无'"></el-col>
                 <el-col :span="3">联系人风险名单总数</el-col><el-col :span="9" v-text="personas.socialContactProfile.contactRishCnt ? personas.socialContactProfile.contactRishCnt:'无'"></el-col>
              </el-col>
        </el-col>
      </el-col>
      <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px;padding-right:0px;">
        <el-col :span="3">通话概况</el-col>
        <el-col :span="21" style="margin-top: -10px;">
              <el-col :span="24" style="margin-top: 0px;border-bottom: #bcbcbc solid 1px">
                 <el-col :span="3">日均通话次数</el-col><el-col :span="9" v-text="personas.callProfile.avgCallCnt ? personas.callProfile.avgCallCnt:'无'"></el-col>
                 <el-col :span="3">日均通话时长（m）</el-col><el-col :span="9" v-text="personas.callProfile.avgCallTime ? personas.callProfile.avgCallTime:'无'"></el-col>
              </el-col>
              <el-col :span="24" style="border-bottom: #bcbcbc solid 1px">
                 <el-col :span="3">静默次数</el-col><el-col :span="9" v-text="personas.callProfile.silenceCnt ? personas.callProfile.silenceCnt:'无'"></el-col>
                 <el-col :span="3">夜间通话次数</el-col><el-col :span="9" v-text="personas.callProfile.nightCallCnt ? personas.callProfile.nightCallCnt:'无'"></el-col>
              </el-col>
              <el-col :span="24" style="">
                 <el-col :span="3">夜间平均通话时长</el-col><el-col :span="9" v-text="personas.callProfile.nightAvgCallTime ? personas.callProfile.nightAvgCallTime:'无'"></el-col>
              </el-col>
        </el-col>
      </el-col>
      <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px;padding-right:0px;">
        <el-col :span="3">消费概况</el-col>
        <el-col :span="21" style="margin-top: -10px;">
              <el-col :span="24" style="">
                 <el-col :span="3">月均消费</el-col><el-col :span="9" v-text="personas.consumptionProfile.avgFeeMonth ? personas.consumptionProfile.avgFeeMonth:'无'"></el-col>
              </el-col>
        </el-col>
      </el-col>

      <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">基本信息检测</div></el-col>
      <div v-for="(checks, index) in basicInfoCheck">
        <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
            <el-col :span="4">{{ checks.desc }}</el-col><el-col :span="8"  v-text="checks.resultDesc ? checks.resultDesc:'未知结果'"></el-col>
        </el-col>
      </div>
      <el-col :span="24" style="border-bottom: #bcbcbc solid 1px" v-if="!basicInfoCheck[0]"><el-col :span="3">无</el-col></el-col>


      <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">风险清单检测</div></el-col>
      <div v-for="(checks, index) in riskListCheck">
        <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
          <el-col :span="4">{{ checks.desc }}</el-col><el-col :span="8">{{ checks.result|resultFilter }}</el-col>
        </el-col>
      </div>
      <el-col :span="24" style="border-bottom: #bcbcbc solid 1px" v-if="!riskListCheck[0]"><el-col :span="3">无</el-col></el-col>


      <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">信贷逾期检查</div></el-col>
      <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px;padding-right:0px;" v-for="checks in overdueLoanCheck">
        <el-col :span="3">{{ checks.desc|notNullFilter }}</el-col>
        <el-col :span="21" style="margin-top: -10px;" v-if="checks.desc">
          <div  v-for="(de, index) in checks.details">
            <el-col :span="24" style="border-bottom: #bcbcbc solid 1px; border-top: #bcbcbc solid 1px" v-if="index>0">
                <el-col :span="3">逾期金额（范围）</el-col><el-col :span="9" v-text="de.overdueAmt ? de.overdueAmt:'无'"></el-col>
                <el-col :span="3">逾期天数（范围）</el-col><el-col :span="9" v-text="de.overdueDays ? de.overdueDays:'无'"></el-col>
            </el-col>
            <el-col :span="24" style="border-bottom: #bcbcbc solid 1px" v-if="index==0">
                <el-col :span="3">逾期金额（范围）</el-col><el-col :span="9" v-text="de.overdueAmt ? de.overdueAmt:'无'"></el-col>
                <el-col :span="3">逾期天数（范围）</el-col><el-col :span="9" v-text="de.overdueDays ? de.overdueDays:'无'"></el-col>
            </el-col>
            <el-col :span="24" style="">
               <el-col :span="3">逾期时间</el-col><el-col :span="9" v-text="de.overdueTime ? de.overdueTime:'无'"></el-col>
            </el-col>
          </div>
        </el-col>
      </el-col>
      <el-col :span="24" style="border-bottom: #bcbcbc solid 1px" v-if="!overdueLoanCheck[0]"><el-col :span="3">无</el-col></el-col>


      <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">多头借贷检查</div></el-col>
      <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px;padding-right:0px;" v-for="checks in multiLendCheck">
        <el-col :span="3">{{ checks.desc|notNullFilter }}</el-col>
        <el-col :span="21" style="margin-top: -10px;" v-if="checks.desc">
          <div v-for="(de, index) in checks.details">
            <el-col :span="24" style=" border-top: #bcbcbc solid 1px" v-if="index>0">
                <el-col :span="3">借贷平台类型</el-col><el-col :span="9" v-text="de.lendType ? de.lendType:'无'"></el-col>
                <el-col :span="3">借贷次数</el-col><el-col :span="9" v-text="de.lendCnt ? de.lendCnt:'无'"></el-col>
            </el-col>
            <el-col :span="24" style="" v-if="index==0">
                <el-col :span="3">借贷平台类型</el-col><el-col :span="9" v-text="de.lendType ? de.lendType:'无'"></el-col>
                <el-col :span="3">借贷次数</el-col><el-col :span="9" v-text="de.lendCnt ? de.lendCnt:'无'"></el-col>
            </el-col>
          </div>
        </el-col>
      </el-col>
      <el-col :span="24" style="border-bottom: #bcbcbc solid 1px" v-if="!multiLendCheck[0]"><el-col :span="3">无</el-col></el-col>


      <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">风险通话检测</div></el-col>
      <el-col :span="24" style="margin-top: 10px;padding-right:0px;" v-for="checks in riskCallCheck">
        <el-col :span="3">{{ checks.desc|notNullFilter }}</el-col>
        <el-col :span="21" style="margin-top: -10px;" v-if="checks.desc">
            <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
              <el-col :span="3">检查项</el-col><el-col :span="9" v-text="checks.desc ? checks.desc:'未知'"></el-col>
              <el-col :span="3">命中描述</el-col><el-col :span="9" v-text="checks.hitDesc ? checks.hitDesc:'未知'"></el-col>
            </el-col>
            <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
              <el-col :span="3">命中次数</el-col><el-col :span="9" v-text="checks.cnt ? checks.cnt:'未知'"></el-col>
              <el-col :span="3">时长（s）</el-col><el-col :span="9" v-text="checks.duration ? checks.duration:'未知'"></el-col>
            </el-col>
            <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
             <el-col :span="3">详情</el-col>
             <el-col :span="21" style="margin-top: -10px;" v-if="checks.desc">
               <div v-for="(de, index) in checks.details">
                 <el-col :span="24" style=" border-top: #bcbcbc solid 1px" v-if = 'index>0'>
                     <el-col :span="3">通话标记</el-col><el-col :span="9" v-text="de.callTag ? de.callTag:'无'"></el-col>
                     <el-col :span="3">通话类型</el-col><el-col :span="9" v-text="de.callType ? de.callType:'无'"></el-col>
                 </el-col>
                 <el-col :span="24" style="" v-else = ''>
                    <el-col :span="3">通话标记</el-col><el-col :span="9" v-text="de.callTag ? de.callTag:'无'"></el-col>
                    <el-col :span="3">通话类型</el-col><el-col :span="9" v-text="de.callType ? de.callType:'无'"></el-col>
                 </el-col>
                 <el-col :span="24" style="border-top: #bcbcbc solid 1px">
                     <el-col :span="3">通话次数</el-col><el-col :span="9" v-text="de.callCnt ? de.callCnt:'无'"></el-col>
                     <el-col :span="3">通话时长（s）</el-col><el-col :span="9" v-text="de.callTime ? de.callTime:'无'"></el-col>
                 </el-col>
               </div>
             </el-col>
            </el-col>
         </el-col>
      </el-col>
      <el-col :span="24" style="border-bottom: #bcbcbc solid 1px" v-if="!riskCallCheck[0]"><el-col :span="3">无</el-col></el-col>


      <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">通话概况</div></el-col>
      <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
        <el-col :span="3">日均通话次数</el-col><el-col :span="9" v-text="callAnalysis.avgCallCnt ? callAnalysis.avgCallCnt:'未知'"></el-col>
        <el-col :span="3">日均通话时长（s）</el-col><el-col :span="9" v-text="callAnalysis.avgCallTime ? callAnalysis.avgCallTime:'未知'"></el-col>
      </el-col>
      <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
        <el-col :span="3">日均主叫次数</el-col><el-col :span="9" v-text="callAnalysis.avgCallingCnt ? callAnalysis.avgCallingCnt:'未知'"></el-col>
        <el-col :span="3">日均主叫时长（s）</el-col><el-col :span="9" v-text="callAnalysis.avgCallingTime ? callAnalysis.avgCallingTime:'未知'"></el-col>
      </el-col>
      <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
        <el-col :span="3">日均被叫次数</el-col><el-col :span="9" v-text="callAnalysis.avgCalledCnt ? callAnalysis.avgCalledCnt:'未知'"></el-col>
        <el-col :span="3">日均被叫时长（s）</el-col><el-col :span="9" v-text="callAnalysis.avgCalledTime ? callAnalysis.avgCalledTime:'未知'"></el-col>
      </el-col>
      <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
        <el-col :span="3">本地通话占比</el-col><el-col :span="9" v-text="callAnalysis.locCallPct ? callAnalysis.locCallPct:'未知'"></el-col>
      </el-col>

      <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">活跃情况</div></el-col>
      <div v-for="(active, index) in activeCallAnalysis">
        <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px;padding-right:0px;">
          <el-col :span="3">{{active.desc}}</el-col>
          <el-col :span="21" style="margin-top: -10px;">
                <el-col :span="24" style="border-bottom: #bcbcbc solid 1px">
                   <el-col :span="3">近1月</el-col><el-col :span="9" v-text="active.lately1m ? active.lately1m:'无'"></el-col>
                   <el-col :span="3">近3月</el-col><el-col :span="9" v-text="active.lately3m ? active.lately3m:'无'"></el-col>
                </el-col>
                <el-col :span="24" style="">
                   <el-col :span="3">近6月</el-col><el-col :span="9" v-text="active.lately6m ? active.lately6m:'无'"></el-col>
                   <el-col :span="3">月均</el-col><el-col :span="9" v-text="active.avgMonth ? active.avgMonth:'无'"></el-col>
                </el-col>
          </el-col>
        </el-col>
      </div>
      <el-col :span="24" style="border-bottom: #bcbcbc solid 1px" v-if="!activeCallAnalysis[0]"><el-col :span="3">无</el-col></el-col>


      <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">静默情况</div></el-col>
      <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
        <el-col :span="3">静默次数</el-col><el-col :span="9" v-text="silenceAnalysis.silenceCnt ? silenceAnalysis.silenceCnt:'未知'"></el-col>
        <el-col :span="3">静默总时长(s)</el-col><el-col :span="9" v-text="silenceAnalysis.silenceTime ? silenceAnalysis.silenceTime:'未知'"></el-col>
      </el-col>
      <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
        <el-col :span="3">最长一次静默开始时间</el-col><el-col :span="9" v-text="silenceAnalysis.longestSilenceStart ? silenceAnalysis.longestSilenceStart:'未知'"></el-col>
        <el-col :span="3">最长一次静默时长(s)</el-col><el-col :span="9" v-text="silenceAnalysis.longestSilenceTime ? silenceAnalysis.longestSilenceTime:'未知'"></el-col>
      </el-col>
      <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
        <el-col :span="3">最近一次静默开始时间</el-col><el-col :span="9" v-text="silenceAnalysis.lastSilenceStart ? silenceAnalysis.lastSilenceStart:'未知'"></el-col>
        <el-col :span="3">最近一次静默时长(s)</el-col><el-col :span="9" v-text="silenceAnalysis.lastSilenceTime ? silenceAnalysis.lastSilenceTime:'未知'"></el-col>
      </el-col>


      <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">通话时间段分析</div></el-col>
      <div v-for="(duration, index) in callDurationAnalysis">
        <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px;padding-right:0px;">
          <el-col :span="3">{{duration.desc}}</el-col>
          <el-col :span="21" style="margin-top: -10px;">
                <el-col :span="24" style="border-bottom: #bcbcbc solid 1px">
                   <el-col :span="3">通话次数</el-col><el-col :span="9" v-text="duration.callCnt ? duration.callCnt:'无'"></el-col>
                   <el-col :span="3">通话号码数</el-col><el-col :span="9" v-text="duration.callNumCnt ? duration.callNumCnt:'无'"></el-col>
                </el-col>
                <el-col :span="24" style="border-bottom: #bcbcbc solid 1px">
                   <el-col :span="3">最常联系号码</el-col><el-col :span="9" v-text="duration.freqContactNum ? duration.freqContactNum:'无'"></el-col>
                   <el-col :span="3">最常联系号码次数</el-col><el-col :span="9" v-text="duration.freqContactNumCnt ? duration.freqContactNumCnt:'无'"></el-col>
                </el-col>
                <el-col :span="24" style="border-bottom: #bcbcbc solid 1px">
                   <el-col :span="3">平均通话时长(s)</el-col><el-col :span="9" v-text="duration.avgCallTime ? duration.avgCallTime:'无'"></el-col>
                   <el-col :span="3">主叫次数</el-col><el-col :span="9" v-text="duration.callingCnt ? duration.callingCnt:'无'"></el-col>
                </el-col>
                <el-col :span="24" style="border-bottom: #bcbcbc solid 1px">
                   <el-col :span="3">主叫时长(s)</el-col><el-col :span="9" v-text="duration.callingTime ? duration.callingTime:'无'"></el-col>
                   <el-col :span="3">被叫次数</el-col><el-col :span="9" v-text="duration.calledCnt ? duration.calledCnt:'无'"></el-col>
                </el-col>
                <el-col :span="24" style="">
                   <el-col :span="3">被叫时长(s)</el-col><el-col :span="9" v-text="duration.calledTime ? duration.calledTime:'无'"></el-col>
                </el-col>
          </el-col>
        </el-col>
      </div>
      <el-col :span="24" style="border-bottom: #bcbcbc solid 1px" v-if="!callDurationAnalysis[0]"><el-col :span="3">无</el-col></el-col>


      <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">消费能力</div></el-col>
      <div v-for="(consumption, index) in consumptionAnalysis">
        <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px;padding-right:0px;">
          <el-col :span="3">{{consumption.desc}}</el-col>
          <el-col :span="21" style="margin-top: -10px;">
                <el-col :span="24" style="border-bottom: #bcbcbc solid 1px">
                   <el-col :span="3">近1月</el-col><el-col :span="9" v-text="consumption.lately1m ? consumption.lately1m:'无'"></el-col>
                   <el-col :span="3">近3月</el-col><el-col :span="9" v-text="consumption.lately3m ? consumption.lately3m:'无'"></el-col>
                </el-col>
                <el-col :span="24" style="">
                   <el-col :span="3">近6月</el-col><el-col :span="9" v-text="consumption.lately6m ? consumption.lately6m:'无'"></el-col>
                   <el-col :span="3">月均</el-col><el-col :span="9" v-text="consumption.avgMonth ? consumption.avgMonth:'无'"></el-col>
                </el-col>
          </el-col>
        </el-col>
      </div>
      <el-col :span="24" style="border-bottom: #bcbcbc solid 1px" v-if="!consumptionAnalysis[0]"><el-col :span="3">无</el-col></el-col>


      <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">出行信息</div></el-col>
      <div v-for="(trip, index) in tripAnalysis">
        <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px;padding-right:0px;">
          <el-col :span="3">{{ trip.departureDate|notNullFilter }} -> {{ trip.destinationPlace|notNullFilter }}</el-col>
          <el-col :span="21" style="margin-top: -10px;">
                <el-col :span="24" style="border-bottom: #bcbcbc solid 1px">
                   <el-col :span="3">出发地</el-col><el-col :span="9" v-text="trip.departurePlace ? trip.departurePlace:'无'"></el-col>
                   <el-col :span="3">目的地</el-col><el-col :span="9" v-text="trip.destinationPlace ? trip.destinationPlace:'无'"></el-col>
                </el-col>
                <el-col :span="24" style="">
                   <el-col :span="3">出发时间</el-col><el-col :span="9" v-text="trip.departureDate ? trip.departureDate:'无'"></el-col>
                   <el-col :span="3">回程时间</el-col><el-col :span="9" v-text="trip.returnDate ? trip.returnDate:'无'"></el-col>
                </el-col>
          </el-col>
        </el-col>
      </div>
      <el-col :span="24" style="border-bottom: #bcbcbc solid 1px" v-if="!tripAnalysis[0]"><el-col :span="3">无</el-col></el-col>


      <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">社交关系概况</div></el-col>
      <div v-for="(social, index) in socialContactAnalysis">
        <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px;padding-right:0px;">
          <el-col :span="3">{{social.desc}}</el-col>
          <el-col :span="21" style="margin-top: -10px;">
                <el-col :span="24" style="">
                   <el-col :span="3">概况</el-col><el-col :span="9" v-text="social.content ? social.content:'无'"></el-col>
                   <el-col :span="3">描述</el-col><el-col :span="9" v-text="social.contentDesc ? social.contentDesc:'无'"></el-col>
                </el-col>
          </el-col>
        </el-col>
      </div>
      <el-col :span="24" style="border-bottom: #bcbcbc solid 1px" v-if="!socialContactAnalysis[0]"><el-col :span="3">无</el-col></el-col>


      <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">通话区域分析</div></el-col>
      <div v-for="(call, index) in callAreaAnalysis">
        <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
          <el-col :span="24" style="border-bottom: #bcbcbc solid 1px">
             <el-col :span="3">通话地</el-col><el-col :span="9" v-text="call.attribution ? call.attribution:'无'"></el-col>
             <el-col :span="3">通话次数</el-col><el-col :span="9" v-text="call.callCnt ? call.callCnt:'无'"></el-col>
          </el-col>
          <el-col :span="24" style="border-bottom: #bcbcbc solid 1px">
             <el-col :span="3">通话号码数</el-col><el-col :span="9" v-text="call.callNumCnt ? call.callNumCnt:'无'"></el-col>
             <el-col :span="3">通话时长</el-col><el-col :span="9" v-text="call.callTime ? call.callTime:'无'"></el-col>
          </el-col>
          <el-col :span="24" style="border-bottom: #bcbcbc solid 1px">
             <el-col :span="3">主叫次数</el-col><el-col :span="9" v-text="call.callingCnt ? call.callingCnt:'无'"></el-col>
             <el-col :span="3">主叫时长(s)</el-col><el-col :span="9" v-text="call.callingTime ? call.callingTime:'无'"></el-col>
          </el-col>
          <el-col :span="24" style="">
             <el-col :span="3">被叫次数</el-col><el-col :span="9" v-text="call.calledCnt ? call.calledCnt:'无'"></el-col>
             <el-col :span="3">被叫时长(s)</el-col><el-col :span="9" v-text="call.calledTime ? call.calledTime:'无'"></el-col>
          </el-col>
        </el-col>
      </div>
      <el-col :span="24" style="border-bottom: #bcbcbc solid 1px" v-if="!callAreaAnalysis[0]"><el-col :span="3">无</el-col></el-col>


      <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">通话联系人分析</div></el-col>
      <div v-for="(contact, index) in contactAnalysis">
        <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px;padding-right:0px;">
          <el-col :span="3">{{contact.callNum}}</el-col>
          <el-col :span="21" style="margin-top: -10px;">
                <el-col :span="24" style="padding-right:0px;">
                   <el-col :span="24" style="border-bottom: #bcbcbc solid 1px">
                      <el-col :span="3">号码</el-col><el-col :span="9" v-text="contact.callNum ? contact.callNum:'无'"></el-col>
                      <el-col :span="3">是否命中风险名单</el-col><el-col :span="9">{{ contact.isHitRiskList|resultFilter }}</el-col>
                   </el-col>
                   <el-col :span="24" style="border-bottom: #bcbcbc solid 1px">
                      <el-col :span="3">电话标记</el-col><el-col :span="9" v-text="contact.callTag ? contact.callTag:'无'"></el-col>
                      <el-col :span="3">归属地</el-col><el-col :span="9" v-text="contact.attribution ? contact.attribution:'无'"></el-col>
                   </el-col>
                   <el-col :span="24" style="border-bottom: #bcbcbc solid 1px">
                      <el-col :span="3">通话次数</el-col><el-col :span="9" v-text="contact.callCnt ? contact.callCnt:'无'"></el-col>
                      <el-col :span="3">通话时长</el-col><el-col :span="9" v-text="contact.callTime ? contact.callTime:'无'"></el-col>
                   </el-col>
                   <el-col :span="24" style="border-bottom: #bcbcbc solid 1px">
                      <el-col :span="3">主叫次数</el-col><el-col :span="9" v-text="contact.callingCnt ? contact.callingCnt:'无'"></el-col>
                      <el-col :span="3">主叫时长(s)</el-col><el-col :span="9" v-text="contact.callingTime ? contact.callingTime:'无'"></el-col>
                   </el-col>
                   <el-col :span="24" style="border-bottom: #bcbcbc solid 1px">
                      <el-col :span="3">被叫次数</el-col><el-col :span="9" v-text="contact.calledCnt ? contact.calledCnt:'无'"></el-col>
                      <el-col :span="3">被叫时长(s)</el-col><el-col :span="9" v-text="contact.calledTime ? contact.calledTime:'无'"></el-col>
                   </el-col>
                   <el-col :span="24" style="">
                      <el-col :span="3">最近一次通话时间</el-col><el-col :span="9" v-text="contact.lastStart ? contact.lastStart:'无'"></el-col>
                      <el-col :span="3">最近一次通话时时长(s)</el-col><el-col :span="9" v-text="contact.lastTime ? contact.lastTime:'无'"></el-col>
                   </el-col>
                </el-col>
          </el-col>
        </el-col>
      </div>
      <el-col :span="24" style="border-bottom: #bcbcbc solid 1px" v-if="!contactAnalysis[0]"><el-col :span="3">无</el-col></el-col>
      <el-col :span="24" class="hasMore">
        <el-button v-show="contactAnalysis.hasMore" @click.prevent="handleHasMore">浏览更多...</el-button>
      </el-col>   
    </div>


    <div v-show="dataShow">
      <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">基本信息</div></el-col>
      <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
        <el-col :span="3">本机号码</el-col><el-col :span="9" v-text="data.basicInfo.mobileNo ? data.basicInfo.mobileNo:'未知'"></el-col>
        <el-col :span="3">姓名</el-col><el-col :span="9" v-text="data.basicInfo.realName ? data.basicInfo.realName:'未知'"></el-col>
      </el-col>
      <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
        <el-col :span="3">入网时间</el-col><el-col :span="9" v-text="data.basicInfo.registerDate ? data.basicInfo.registerDate:'未知'"></el-col>
        <el-col :span="3">身份证号</el-col><el-col :span="9" v-text="data.basicInfo.idCard ? data.basicInfo.idCard:'未知'"></el-col>
      </el-col>
      <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
        <el-col :span="3">地址</el-col><el-col :span="9" v-text="data.basicInfo.address ? data.basicInfo.address:'未知'"></el-col>
        <el-col :span="3">星级</el-col><el-col :span="9" v-text="data.basicInfo.vipLevelstr ? data.basicInfo.vipLevelstr:'未知'"></el-col>
      </el-col>
      <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
        <el-col :span="3">邮箱</el-col><el-col :span="9" v-text="data.basicInfo.email ? data.basicInfo.email:'无'"></el-col>
        <el-col :span="3">可用积分</el-col><el-col :span="9" v-text="data.basicInfo.pointsValuestr ? data.basicInfo.pointsValuestr:'未知'"></el-col>
      </el-col>
      <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
        <el-col :span="3">可用余额</el-col><el-col :span="9" v-text="data.basicInfo.amount ? data.basicInfo.amount:'未知'"></el-col>
      </el-col>


      <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">前 10 通话记录</div></el-col>
      <div v-for="stati in data.stati">
        <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
          <el-col :span="3">与本机通话手机号码</el-col><el-col :span="9" v-text="stati.mobileNo ? stati.mobileNo:'未知'"></el-col>
          <el-col :span="3">与本机通话次数</el-col><el-col :span="9" v-text="stati.callCount ? stati.callCount:'未知'"></el-col>
        </el-col>
      </div>
      <el-col :span="24" style="border-bottom: #bcbcbc solid 1px" v-if="!data.stati[0]"><el-col :span="3">无</el-col></el-col>


      <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">近 6 个月通话记录详单</div></el-col>
      <div v-for="callRecordInfo in data.callRecordInfo">
        <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px;padding-right:0px;">
          <el-col :span="3">{{ callRecordInfo.mobileNo }}</el-col>
          <el-col :span="21" style="margin-top: -10px;">
             <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px"> 
                <el-col :span="3">通话地点</el-col><el-col :span="9" v-text="callRecordInfo.callAddress ? callRecordInfo.callAddress:'未知'"></el-col>
                <el-col :span="3">通话时间</el-col><el-col :span="9" v-text="callRecordInfo.callDateTime ? callRecordInfo.callDateTime:'未知'"></el-col>
             </el-col>
            <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
              <el-col :span="3">通话时长(秒)</el-col><el-col :span="9" v-text="callRecordInfo.callTimeLength ? callRecordInfo.callTimeLength:'未知'"></el-col>
              <el-col :span="3">通话类型</el-col><el-col :span="9" v-text="callRecordInfo.callType ? callRecordInfo.callType:'未知'"></el-col>
            </el-col>
            <el-col :span="24" style="margin-top: 10px;">
              <el-col :span="3">与本机通话手机号码</el-col><el-col :span="9" v-text="callRecordInfo.mobileNo ? callRecordInfo.mobileNo:'未知'"></el-col>
            </el-col>
          </el-col>
        </el-col>
      </div>
      <el-col :span="24" style="border-bottom: #bcbcbc solid 1px" v-if="!data.callRecordInfo[0]"><el-col :span="3">无</el-col></el-col>
      <el-col :span="24" class="hasMore">
        <el-button v-show="data.callRecordInfo.hasMore" @click.prevent="handleHasMoreCallRecord">浏览更多...</el-button>
      </el-col> 


      <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">近 6 个月账单信息</div></el-col>
      <div v-for="bill in data.bill">
        <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px;padding-right:0px;">
          <el-col :span="3">{{ bill.startTime |  notNullFilter}}</el-col>
          <el-col :span="21" style="margin-top: -10px;">
            <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
              <el-col :span="3">本机号码</el-col><el-col :span="9" v-text="bill.mobileNo ? bill.mobileNo:'未知'"></el-col>
              <el-col :span="3">账单月份</el-col><el-col :span="9" v-text="bill.startTime ? bill.startTime:'未知'"></el-col>
            </el-col>
            <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
              <el-col :span="3">套餐消费</el-col><el-col :span="9" v-text="bill.comboCost ? bill.comboCost:'未知'"></el-col>
              <el-col :span="3">总金额</el-col><el-col :span="9" v-text="bill.sumCost ? bill.sumCost:'未知'"></el-col>
            </el-col>
            <el-col :span="24" style="margin-top: 10px;">
              <el-col :span="3">实际费用</el-col><el-col :span="9" v-text="bill.realCost ? bill.realCost:'未知'"></el-col>
            </el-col>
          </el-col>
        </el-col>
      </div>
      <el-col :span="24" style="border-bottom: #bcbcbc solid 1px" v-if="!data.bill[0]"><el-col :span="3">无</el-col></el-col>


      <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">近 6 个月短信信息</div></el-col>
      <div v-for="smsInfo in data.smsInfo">
        <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px;padding-right:0px;">
          <el-col :span="3">{{ smsInfo.sendSmsToTelCode }}</el-col>
          <el-col :span="21" style="margin-top: -10px;">
            <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
              <el-col :span="3">本机号码</el-col><el-col :span="9" v-text="smsInfo.mobileNo ? smsInfo.mobileNo:'未知'"></el-col>
              <el-col :span="3">与本机通话手机号码</el-col><el-col :span="9" v-text="smsInfo.sendSmsToTelCode ? smsInfo.sendSmsToTelCode:'未知'"></el-col>
            </el-col>
            <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
              <el-col :span="3">发送地</el-col><el-col :span="9" v-text="smsInfo.sendSmsAddress ? smsInfo.sendSmsAddress:'未知'"></el-col>
              <el-col :span="3">发送时间</el-col><el-col :span="9" v-text="smsInfo.sendSmsTime ? smsInfo.sendSmsTime:'未知'"></el-col>
            </el-col>
            <el-col :span="24" style="margin-top: 10px;">
              <el-col :span="3">发送类型</el-col><el-col :span="9" v-text="smsInfo.sendType ? smsInfo.sendType:'未知'"></el-col>
            </el-col>
          </el-col>
        </el-col> 
      </div>
      <el-col :span="24" style="border-bottom: #bcbcbc solid 1px" v-if="!data.smsInfo[0]"><el-col :span="3">无</el-col></el-col>
      <el-col :span="24" class="hasMore">
        <el-button v-show="data.smsInfo.hasMore" @click.prevent="handleHasMoreSms">浏览更多...</el-button>
      </el-col> 


      <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">近 6 个月上网信息</div></el-col>
      <div v-for="netInfo in data.netInfo">
        <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px;padding-right:0px;">
          <el-col :span="3">{{ netInfo.place }}</el-col>
          <el-col :span="21" style="margin-top: -10px;">
            <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
              <el-col :span="3">本机号码</el-col><el-col :span="9" v-text="netInfo.mobileNo ? netInfo.mobileNo:'未知'"></el-col>
              <el-col :span="3">上网地点</el-col><el-col :span="9" v-text="netInfo.place ? netInfo.place:'未知'"></el-col>
            </el-col>
            <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
              <el-col :span="3">上网时间</el-col><el-col :span="9" v-text="netInfo.netTime ? netInfo.netTime:'未知'"></el-col>
              <el-col :span="3">上网时长(秒)</el-col><el-col :span="9" v-text="netInfo.onlineTime ? netInfo.onlineTime:'未知'"></el-col>
            </el-col>
            <el-col :span="24" style="margin-top: 10px;">
              <el-col :span="3">上网类型</el-col><el-col :span="9" v-text="netInfo.netType ? netInfo.netType:'未知'"></el-col>
            </el-col>
          </el-col>
        </el-col> 
      </div>
      <el-col :span="24" style="border-bottom: #bcbcbc solid 1px" v-if="!data.netInfo[0]"><el-col :span="3">无</el-col></el-col>


      <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">近 6 个月办理业务信息</div></el-col>
      <div v-for="businessInfo in data.businessInfo">
        <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px;padding-right:0px;">
          <el-col :span="3">{{ businessInfo.businessName }}</el-col>
          <el-col :span="21" style="margin-top: -10px;">
            <el-col :span="24" style="margin-top: 10px;border-bottom: #bcbcbc solid 1px">
              <el-col :span="3">本机号码</el-col><el-col :span="9" v-text="businessInfo.mobileNo ? businessInfo.mobileNo:'未知'"></el-col>
              <el-col :span="3">业务名称</el-col><el-col :span="9" v-text="businessInfo.businessName ? businessInfo.businessName:'未知'"></el-col>
            </el-col>
            <el-col :span="24" style="margin-top: 10px;">
              <el-col :span="3">业务开始时间</el-col><el-col :span="9" v-text="businessInfo.beginTime ? businessInfo.beginTime:'未知'"></el-col>
              <el-col :span="3">业务消费</el-col><el-col :span="9" v-text="businessInfo.cost ? businessInfo.cost:'未知'"></el-col>
            </el-col>
          </el-col>
        </el-col> 
      </div>
      <el-col :span="24" style="border-bottom: #bcbcbc solid 1px" v-if="!data.businessInfo[0]"><el-col :span="3">无</el-col></el-col>

    </div>
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
      initContactAnalysisPage: 0,
      initContactAnalysisSize: 10,
      initCallRecordPage: 0,
      initCallRecordSize: 10,
      initSmsPage: 0,
      initSmsSize: 10,
      report: {
        dataSource: '',
        reportTime: '',
        reportNo: '',
      },
      basicInfo: {
        name: '',
        identityNo: '',
        gender: '',
        age: '',
        mobile: '',
        regTime: '',
        nativeAddress: '',
      },
      contactInfo: {
        name: '',
        mobile: '',
        identityNo: '',
        relationship: '',
        callTime: '',
        callRank: '',
        isHitRiskList: '',
      },
      relationInfo: {
        identiyNos: '',
        mobiles: '',
        homeAddresses: '',
      },
      personas: {
        riskProfile: {
          riskListCnt: '',
          overdueLoanCnt: '',
          multiLendCnt: '',
          riskCallCnt: '',
        },
        socialContactProfile: {
          freContactArea: '',
          contactNumCnt: '',
          interflowContactCnt: '',
          contactRishCnt: '',
        },
        callProfile: {
          avgCallCnt: '',
          avgCallTime: '',
          silenceCnt: '',
          nightCallCnt: '',
          nightAvgCallTime: '',
        },
        consumptionProfile: {
          avgFeeMonth: '',
        },
      },
      basicInfoCheck: [{ item: '', desc: '', result: '', resultDesc: '' }],
      riskListCheck: [{ item: '', desc: '', result: '' }],
      overdueLoanCheck: [{
        item: '',
        desc: '',
        details: [{ overdueAmt: '', overdueDays: '', overdueTime: '' }],
      }],
      multiLendCheck: [{
        item: '',
        desc: '',
        details: [{ lendType: '', lendCnt: '' }],
      }],
      riskCallCheck: [{
        item: '',
        desc: '',
        hitDesc: '',
        cnt: '',
        duration: '',
        details: [{ callTg: '', callType: '', callCnt: '', callTime: '' }],
      }],
      callAnalysis: {
        avgCallCnt: '',
        avgCallTime: '',
        avgCallingCnt: '',
        avgCallingTime: '',
        avgCalledCnt: '',
        avgCalledTime: '',
        locCallPct: '',
      },
      activeCallAnalysis: [{
        item: '',
        desc: '',
        lately1m: '',
        lately3m: '',
        lately6m: '',
        avgMonth: '',
      }],
      silenceAnalysis: {
        silenceCnt: '',
        silenceTime: '',
        longestSilenceStart: '',
        longestSilenceTime: '',
        lastSilenceStart: '',
        lastSilenceTime: '',
      },
      callDurationAnalysis: [{
        item: '',
        desc: '',
        callCnt: '',
        callNumCnt: '',
        freqContactNum: '',
        freqContactNumCnt: '',
        avgCallTime: '',
        callingCnt: '',
        callingTime: '',
        calledCnt: '',
        calledTime: '',
      }],
      consumptionAnalysis: [{
        item: '',
        desc: '',
        lately1m: '',
        lately3m: '',
        lately6m: '',
        avgMonth: '',
      }],
      tripAnalysis: [{
        departureDate: '',
        returnDate: '',
        departurePlace: '',
        destinationPlace: '',
      }],
      socialContactAnalysis: [{
        item: '',
        desc: '',
        content: '',
        contentDesc: '',
      }],
      callAreaAnalysis: [{
        attribution: '',
        callCnt: '',
        callNumCnt: '',
        callTime: '',
        callingCnt: '',
        callingTime: '',
        calledCnt: '',
        calledTime: '',
      }],
      contactAnalysis: [{
        callNum: '',
        isHitRiskList: '',
        callTag: '',
        attribution: '',
        callCnt: '',
        callTime: '',
        callingCnt: '',
        callingTime: '',
        calledCnt: '',
        calledTime: '',
        lastStart: '',
        lastTime: '',
        hasMore: false,
      }],
      reportShow: true,
      dataShow: false,
      btnReportType: true,
      btnDataType: false,
      data: {
        basicInfo: {
          mobileNo: '',
          realName: '',
          registerDate: '',
          idCard: '',
          address: '',
          vipLevelstr: '',
          email: '',
          pointsValuestr: '',
          amount: '',
        },
        stati: [{ mobileNo: '', callCount: '' }],
        callRecordInfo: [{ callAddress: '', callDateTime: '', callTimeLength: '', callType: '', mobileNo: '', hasMore: false }],
        bill: [{ mobileNo: '', startTime: '', comboCost: '', sumCost: '', realCost: '' }],
        smsInfo: [{ mobileNo: '', sendSmsToTelCode: '', sendSmsAddress: '', sendSmsTime: '', sendType: '', hasMore: false }],
        netInfo: [{ mobileNo: '', place: '', netTime: '', onlineTime: '', netType: '' }],
        businessInfo: [{ mobileNo: '', businessName: '', beginTime: '', cost: '' }],
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
      this.$http.get(`${apiBase}mobile-report/${this.id}`)
        .then(getJsonAndCheckSuccess)
        .then((response) => {
          if (response.results.report) {
            this.report = response.results.report.report;
            this.basicInfo = response.results.report.basicInfo;
            this.contactInfo = response.results.report.contactInfo;
            this.relationInfo = response.results.report.relationInfo;
            this.personas = response.results.report.personas;
            this.basicInfoCheck = response.results.report.basicInfoCheck;
            this.riskListCheck = response.results.report.riskListCheck;
            this.overdueLoanCheck = response.results.report.overdueLoanCheck;
            this.multiLendCheck = response.results.report.multiLendCheck;
            this.riskCallCheck = response.results.report.riskCallCheck;
            this.callAnalysis = response.results.report.callAnalysis;
            this.activeCallAnalysis = response.results.report.activeCallAnalysis;
            this.silenceAnalysis = response.results.report.silenceAnalysis;
            this.callDurationAnalysis = response.results.report.callDurationAnalysis;
            this.consumptionAnalysis = response.results.report.consumptionAnalysis;
            this.tripAnalysis = response.results.report.tripAnalysis;
            this.socialContactAnalysis = response.results.report.socialContactAnalysis;
            this.callAreaAnalysis = response.results.report.callAreaAnalysis;
            this.contactAnalysis = response.results.report.contactAnalysis;
            this.contactAnalysis.hasMore = response.results.report.has_more;
          }
          if (response.results.content) {
            this.data = response.results.content;
            this.data.callRecordInfo.hasMore = response.results.content.has_more;
            this.data.smsInfo.hasMore = response.results.content.sms_has_more;
          }
        }).catch(reportErrorMessage(this));
    },
    handleReport() {
      this.reportShow = true;
      this.dataShow = false;
      this.btnReportType = true;
      this.btnDataType = false;
    },
    handleDataContent() {
      this.reportShow = false;
      this.dataShow = true;
      this.btnReportType = false;
      this.btnDataType = true;
    },
    mobileJump(v) {
      this.$router.push({
        path: '/auth-mobile',
        query: { mobile: v },
      });
    },
    handleHasMore() {
      const oldContact = this.contactAnalysis;
      const params = {
        cantact_limit: this.initContactAnalysisSize,
        cantact_offset: this.initContactAnalysisSize + this.initContactAnalysisPage,
      };
      this.$http.get(`${apiBase}mobile-report/${this.id}`, { params })
        .then(getJsonAndCheckSuccess)
        .then((response) => {
          if (response.results.report) {
            this.initContactAnalysisSize = params.cantact_limit;
            this.initContactAnalysisPage = params.cantact_offset;
            this.contactAnalysis = oldContact.concat(response.results.report.contactAnalysis);
            this.contactAnalysis.hasMore = response.results.report.has_more;
          }
        }).catch(reportErrorMessage(this));
    },
    handleHasMoreCallRecord() {
      const old = this.data.callRecordInfo;
      const params = {
        call_record_limit: this.initCallRecordSize,
        call_record_offset: this.initCallRecordSize + this.initCallRecordPage,
      };
      this.$http.get(`${apiBase}mobile-report/${this.id}`, { params })
        .then(getJsonAndCheckSuccess)
        .then((response) => {
          if (response.results.content) {
            this.data.callRecordInfo = old.concat(response.results.content.callRecordInfo);
            this.initCallRecordSize = params.call_record_limit;
            this.initCallRecordPage = params.call_record_offset;
            this.data.callRecordInfo.hasMore = response.results.content.has_more;
          }
        }).catch(reportErrorMessage(this));
    },
    handleHasMoreSms() {
      const oldSms = this.data.smsInfo;
      const params = {
        sms_limit: this.initSmsSize,
        sms_offset: this.initSmsSize + this.initSmsPage,
      };
      this.$http.get(`${apiBase}mobile-report/${this.id}`, { params })
        .then(getJsonAndCheckSuccess)
        .then((response) => {
          if (response.results.content) {
            this.data.smsInfo = oldSms.concat(response.results.content.smsInfo);
            this.initSmsSize = params.sms_limit;
            this.initSmsPage = params.sms_offset;
            this.data.smsInfo.hasMore = response.results.content.sms_has_more;
          }
        }).catch(reportErrorMessage(this));
    },
  },
  filters: {
    resultFilter(v) {
      switch (v) {
        case '0': return '未命中';
        case '1': return '命中';
        default : return '未知';
      }
    },
    notNullFilter(v) {
      if (v) {
        return v;
      }
      return '无';
    },
  },
};
</script>
<style type="text/css">
  .hasMore {
     text-align:center;
  }
</style>