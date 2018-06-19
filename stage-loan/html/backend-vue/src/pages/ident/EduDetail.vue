<template>
  <el-row>
    <el-col>
      <el-button type="info" style="margin-bottom: 5px" @click.native="$router.back(-1)">返回上一页</el-button>
    </el-col>
    <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">学籍信息</div></el-col>
    <el-col :span="24"  class="eduInfo">
       <el-col :span="12">
         <el-col :span="24"  class="singleInfo">
            <el-col :span="6">姓名</el-col><el-col :span="18" v-text="studentStatusInfo.name ? studentStatusInfo.name:'未知'"></el-col>
         </el-col>
         <el-col :span="24"  class="singleInfo">
            <el-col :span="6">性别</el-col><el-col :span="18" v-text="studentStatusInfo.sex ? studentStatusInfo.sex:'未知'"></el-col>
         </el-col>
         <el-col :span="24"  class="singleInfo">
            <el-col :span="6">民族</el-col><el-col :span="18" v-text="studentStatusInfo.nation ? studentStatusInfo.nation:'未知'"></el-col>
         </el-col>
         <el-col :span="24"  class="singleInfo">
            <el-col :span="6">出生日期</el-col><el-col :span="18" v-text="studentStatusInfo.dateBirth ? studentStatusInfo.dateBirth:'未知'"></el-col>
         </el-col>
         <el-col :span="24"  class="singleInfo" style="border-bottom: none;">
            <el-col :span="6">身份证号</el-col><el-col :span="18" v-text="studentStatusInfo.identityNo ? studentStatusInfo.identityNo:'未知'"></el-col>
         </el-col>
       </el-col>
       <el-col :span="12">
         <el-col :span="24">
            <el-col :span="6">个人照片</el-col>
            <el-col :span="18">
              <img class='photos' :src="studentStatusInfo.personalPhotos">
            </el-col>
         </el-col>
       </el-col>      
    </el-col>
    <el-col :span="24"  class="eduInfo">
      <el-col :span="3">考生号</el-col><el-col :span="9" v-text="studentStatusInfo.stuNumber ? studentStatusInfo.stuNumber:'未知'"></el-col>
      <el-col :span="3">学号</el-col><el-col :span="9" v-text="studentStatusInfo.candidateNumber ? studentStatusInfo.candidateNumber:'未知'"></el-col>
    </el-col>
    <el-col :span="24"  class="eduInfo">
      <el-col :span="3">院校名称</el-col><el-col :span="9" v-text="studentStatusInfo.schoolName ? studentStatusInfo.schoolName:'未知'"></el-col>
      <el-col :span="3">分院</el-col><el-col :span="9" v-text="studentStatusInfo.branch ? studentStatusInfo.branch:'未知'"></el-col>
    </el-col>
    <el-col :span="24"  class="eduInfo">
      <el-col :span="3">系(所、函授站)</el-col><el-col :span="9" v-text="studentStatusInfo.department ? studentStatusInfo.department:'未知'"></el-col>
      <el-col :span="3">专业名称</el-col><el-col :span="9" v-text="studentStatusInfo.specialitieName ? studentStatusInfo.specialitieName:'未知'"></el-col>
    </el-col>
    <el-col :span="24"  class="eduInfo">
      <el-col :span="3">班级</el-col><el-col :span="9" v-text="studentStatusInfo.classes ? studentStatusInfo.classes:'未知'"></el-col>
      <el-col :span="3">层次</el-col><el-col :span="9" v-text="studentStatusInfo.arrangement ? studentStatusInfo.arrangement:'未知'"></el-col>
    </el-col>
    <el-col :span="24"  class="eduInfo">
      <el-col :span="3">学制</el-col><el-col :span="9" v-text="studentStatusInfo.educationalSystem ? studentStatusInfo.educationalSystem:'未知'"></el-col>
      <el-col :span="3">学历类别</el-col><el-col :span="9" v-text="studentStatusInfo.educationType ? studentStatusInfo.educationType:'未知'"></el-col>
    </el-col>
    <el-col :span="24"  class="eduInfo">
      <el-col :span="3">学习形式</el-col><el-col :span="9" v-text="studentStatusInfo.learningForm ? studentStatusInfo.learningForm:'未知'"></el-col>
      <el-col :span="3">入学日期</el-col><el-col :span="9" v-text="studentStatusInfo.timeEnrollment ? studentStatusInfo.timeEnrollment:'未知'"></el-col>
    </el-col>
    <el-col :span="24"  class="eduInfo">
      <el-col :span="3">离校日期</el-col><el-col :span="9" v-text="studentStatusInfo.dateGraduation ? studentStatusInfo.dateGraduation:'未知'"></el-col>
      <el-col :span="3">学籍状态</el-col><el-col :span="9" v-text="studentStatusInfo.enrollmentStatus ? studentStatusInfo.enrollmentStatus:'未知'"></el-col>
    </el-col>


    <el-col :span="24" class="bg-blue" style="margin-top: 10px"><div class="grid-content " data-height="50">学历信息</div></el-col>
    <el-col :span="24" class="eduInfo">
       <el-col :span="12">
         <el-col :span="24"  class="singleInfo">
            <el-col :span="6">姓名</el-col><el-col :span="18" v-text="educationInfo.name ? educationInfo.name:'未知'"></el-col>
         </el-col>
         <el-col :span="24"  class="singleInfo">
            <el-col :span="6">性别</el-col><el-col :span="18" v-text="educationInfo.sex ? educationInfo.sex:'未知'"></el-col>
         </el-col>
         <el-col :span="24"  class="singleInfo">
            <el-col :span="6">身份证号</el-col><el-col :span="18" v-text="educationInfo.identityNo ? educationInfo.identityNo:'未知'"></el-col>
         </el-col>
         <el-col :span="24"  class="singleInfo">
            <el-col :span="6">出生日期</el-col><el-col :span="18" v-text="educationInfo.dateBirth ? educationInfo.dateBirth:'未知'"></el-col>
         </el-col>
         <el-col :span="24"  class="singleInfo" style="border-bottom: none;">
            <el-col :span="6">入学日期</el-col><el-col :span="18" v-text="educationInfo.timeEnrollment ? educationInfo.timeEnrollment:'未知'"></el-col>
         </el-col>
       </el-col>
       <el-col :span="12">
         <el-col :span="24">
            <el-col :span="6">个人照片</el-col>
            <el-col :span="18">
              <img class='photos' :src="educationInfo.personalPhotos">
            </el-col>
         </el-col>
       </el-col>      
    </el-col>
    <el-col :span="24"  class="eduInfo">
      <el-col :span="3">毕业时间</el-col><el-col :span="9" v-text="educationInfo.dateGraduation ? studentStatusInfo.dateGraduation:'未知'"></el-col>
      <el-col :span="3">学历类别</el-col><el-col :span="9" v-text="educationInfo.educationType ? studentStatusInfo.educationType:'未知'"></el-col>
    </el-col>
    <el-col :span="24"  class="eduInfo">
      <el-col :span="3">层次</el-col><el-col :span="9" v-text="educationInfo.arrangement ? educationInfo.arrangement:'未知'"></el-col>
      <el-col :span="3">院校名称</el-col><el-col :span="9" v-text="educationInfo.schoolName ? educationInfo.schoolName:'未知'"></el-col>
    </el-col>
    <el-col :span="24"  class="eduInfo">
      <el-col :span="3">院校所在地</el-col><el-col :span="9" v-text="educationInfo.schoolDistrict ? educationInfo.schoolDistrict:'未知'"></el-col>
      <el-col :span="3">专业名称</el-col><el-col :span="9" v-text="educationInfo.specialitieName ? educationInfo.specialitieName:'未知'"></el-col>
    </el-col>
    <el-col :span="24"  class="eduInfo">
      <el-col :span="3">学习形式</el-col><el-col :span="9" v-text="educationInfo.learningForm ? educationInfo.learningForm:'未知'"></el-col>
      <el-col :span="3">证书编号</el-col><el-col :span="9" v-text="educationInfo.certificateNumber ? educationInfo.certificateNumber:'未知'"></el-col>
    </el-col>
    <el-col :span="24"  class="eduInfo">
      <el-col :span="3">毕结业结论</el-col><el-col :span="9" v-text="educationInfo.graduationStatus ? educationInfo.graduationStatus:'未知'"></el-col>
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
      studentStatusInfo: {
        name: '',
        sex: '',
        personalPhotos: 'data:image/png;base64,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',
        nation: '',
        dateBirth: '',
        identityNo: '',
        stuNumber: '',
        candidateNumber: '',
        schoolName: '',
        department: '',
        specialitieName: '',
        classes: '',
        arrangement: '',
        educationalSystem: '',
        educationType: '',
        learningForm: '',
        timeEnrollment: '',
        dateGraduation: '',
        enrollmentStatus: '',
      },
      educationInfo: {
        name: '',
        sex: '',
        identityNo: '',
        personalPhotos: 'data:image/png;base64,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',
        dateBirth: '',
        timeEnrollment: '',
        dateGraduation: '',
        educationType: '',
        arrangement: '',
        schoolName: '',
        schoolDistrict: '',
        specialitieName: '',
        learningForm: '',
        certificateNumber: '',
        graduationStatus: '',
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
      this.$http.get(`${apiBase}limu-info/${this.id}`)
        .then(getJsonAndCheckSuccess)
        .then((response) => {
          if (response.results) {
            this.studentStatusInfo = response.results.studentStatusInfo;
            this.educationInfo = response.results.educationInfo;
          }
        }).catch(reportErrorMessage(this));
    },
  },
  filters: {
    notNullFilter(v) {
      if (v) {
        return v;
      }
      return '无';
    },
  },
};
</script>
<style>
 .photos {
   width:127px;
   height:160px;
 }
 .singleInfo, .eduInfo {
   margin-top: 10px;
   border-bottom: #bcbcbc solid 1px;
 }
 .singleInfo {
   margin-left:-5px;
 }
</style>