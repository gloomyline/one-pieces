<template>
    <div style="background-color: #f2f2f2;padding: 20px;overflow: auto">
        <el-button type="success" style="margin-bottom: 5px" @click.native="$router.back(-1)">返回上一页</el-button>
        <div style="width:1000px;border-left: solid 1px #868686;border-top: solid 2px #868686;border-right: solid 2px #868686;border-bottom: solid 2px #868686">
            <el-row>
                <el-col :span="24"  class="audit-div"><h3>商户信息</h3></el-col>
            </el-row>
            <!--法人信息：begin-->
            <el-row style="border-top: 1px solid">
                <el-col :span="6" class="audit-div">商户名称</el-col><el-col :span="18" class="audit-div">{{ shop.shop_name }}</el-col>
            </el-row>
            <el-row style="border-top: 1px solid">
                <el-col :span="6" class="audit-div">法人姓名</el-col><el-col :span="6" class="audit-div">{{ shop.legal_person_name }}</el-col>
                <el-col :span="6" class="audit-div">法人手机号码</el-col><el-col :span="6" class="audit-div">{{ shop.legal_person_mobile }}</el-col>
            </el-row>
            <el-row style="border-top: 1px solid">
                <el-col :span="6" class="audit-div">法人身份证号</el-col><el-col :span="18" class="audit-div">{{ shop.legal_person_id_no }}</el-col>
            </el-row>
            <el-row style="border-top: 1px solid">
                <el-col :span="6" class="audit-div" style="height: 240px">法人身份证照片（2张）</el-col>
                <el-col :span="18" class="audit-div" style="height: 240px">
                    <el-col :span="12">
                        <div style="padding-top: 10px">
                            <el-button type="text" @click.native="handleShowImg(shop.legal_person_id_card_pic_front)">
                                <div>
                                    <img :src="shop.legal_person_id_card_pic_front" alt="正面" class="image" style="width:300px; height: 200px">
                                </div>
                            </el-button>
                        </div>
                    </el-col>
                    <el-col :span="12">
                        <div style="padding-top: 10px">
                            <el-button type="text" @click.native="handleShowImg(shop.legal_person_id_card_pic_back)">
                                <div>
                                    <img :src="shop.legal_person_id_card_pic_back" alt="反面" class="image" style="width:300px; height: 200px">
                                </div>
                            </el-button>
                        </div>
                    </el-col>
                </el-col>
            </el-row>
            <!--法人信息：end-->
            <!--实际控制人信息：beigin-->
            <el-row style="border-top: 1px solid">
                <el-col :span="6" class="audit-div">实际控制人与法人一致</el-col><el-col :span="18" class="audit-div" v-text="shop.is_eq === true ? '是' : '否'"></el-col>
            </el-row>
            <el-row style="border-top: 1px solid">
                <el-col :span="6" class="audit-div">实际控制人姓名</el-col><el-col :span="6" class="audit-div" v-text="shop.is_eq === true ? shop.legal_person_name : shop.actual_controller_name"></el-col>
                <el-col :span="6" class="audit-div">实际控制人手机号码</el-col><el-col :span="6" class="audit-div"  v-text="shop.is_eq === true ? shop.legal_person_mobile : shop.actual_controller_mobile"></el-col>
            </el-row>
            <el-row style="border-top: 1px solid">
                <el-col :span="6" class="audit-div">实际控制人身份证号</el-col><el-col :span="18" class="audit-div"  v-text="shop.is_eq === true ? shop.legal_person_id_no : shop.actual_controller_id_no"></el-col>
            </el-row>
            <el-row style="border-top: 1px solid">
                <el-col :span="6" class="audit-div" style="height: 240px">实际控制人身份证照片（2张）</el-col>
                <el-col :span="18" class="audit-div" style="height: 240px">
                    <el-col :span="12">
                        <div style="padding-top: 10px">
                            <el-button type="text" @click.native="handleShowImg(shop.is_eq === true ? shop.legal_person_id_card_pic_front : shop.actual_controller_id_card_pic_front)">
                                <div>
                                    <img :src="shop.is_eq === true ? shop.legal_person_id_card_pic_front : shop.actual_controller_id_card_pic_front" alt="正面" class="image" style="width:300px; height: 200px">
                                </div>
                            </el-button>
                        </div>
                    </el-col>
                    <el-col :span="12">
                        <div style="padding-top: 10px">
                            <el-button type="text" @click.native="handleShowImg(shop.is_eq === true ? shop.legal_person_id_card_pic_back : shop.actual_controller_id_card_pic_back)">
                                <div>
                                    <img :src="shop.is_eq === true ? shop.legal_person_id_card_pic_back : shop.actual_controller_id_card_pic_back" alt="反面" class="image" style="width:300px; height: 200px">
                                </div>
                            </el-button>
                        </div>
                    </el-col>

                </el-col>
            </el-row>
            <!--实际控制人：end-->
            <!--公司基本信息：beigin-->
            <el-row style="border-top: 1px solid">
                <el-col :span="6" class="audit-div">企业联系人电子邮箱</el-col><el-col :span="6" class="audit-div">{{shop.corporate_contacts_email }}</el-col>
                <el-col :span="6" class="audit-div">企业联系人手机号</el-col><el-col :span="6" class="audit-div">{{ shop.corporate_contacts_mobile }}</el-col>
            </el-row>
            <el-row style="border-top: 1px solid">
                <el-col :span="6" class="audit-div">打款银行卡</el-col><el-col :span="6" class="audit-div">{{ shop.bank_no }}</el-col>
                <el-col :span="6" class="audit-div">银行卡名称</el-col><el-col :span="6" class="audit-div">{{ shop.bank_name }}</el-col>
            </el-row>
            <el-row style="border-top: 1px solid">
                <el-col :span="6" class="audit-div">企业详细地址</el-col><el-col :span="18" class="audit-div">{{ shop.shop_addr }}</el-col>
            </el-row>

            <el-row style="border-top: 1px solid">
                <el-col :span="6" class="audit-div" style="height: 240px">企业三证（最多3张）</el-col>
                <el-col :span="18" class="audit-div" style="height: 240px">
                    <el-col :span="8" v-for=" pic in shop.three_cards_pic">
                        <div style="padding-top: 10px" >
                            <el-button type="text" @click.native="handleShowImg(pic.url)">
                                <div>
                                    <img :src="pic.url" class="image audit-img">
                                </div>
                            </el-button>
                        </div>
                    </el-col>
                </el-col>
            </el-row>
            <el-row style="border-top: 1px solid">
                <el-col :span="6" class="audit-div">注册资金（万）</el-col><el-col :span="6" class="audit-div">{{ shop.registered_capital }}</el-col>
                <el-col :span="6" class="audit-div">注册时间</el-col><el-col :span="6" class="audit-div">{{ shop.registered_at_year }}年{{ shop.registered_at_month }}月</el-col>
            </el-row>
            <el-row style="border-top: 1px solid">
                <el-col :span="6" class="audit-div">办公面积（平方米）</el-col><el-col :span="6" class="audit-div">{{ shop.office_area }}</el-col>
                <el-col :span="6" class="audit-div">职工人数</el-col><el-col :span="6" class="audit-div">{{ shop.staff_no }}</el-col>
            </el-row>

            <el-row style="border-top: 1px solid">
                <el-col :span="6" class="audit-div" style="height: 240px">企业办公场所照片（5-8张）</el-col>
                <el-col :span="18" class="audit-div" style="height: 240px; overflow: auto">
                    <el-col :span="8" v-for=" pic in shop.corporate_office_pic">
                        <div style="padding-top: 10px">
                            <el-button type="text" @click.native="handleShowImg(pic.url)">
                                <div>
                                    <img :src="pic.url" class="image audit-img">
                                </div>
                            </el-button>
                        </div>
                    </el-col>
                </el-col>
            </el-row>
            <el-row style="border-top: 1px solid">
                <el-col :span="6" class="audit-div" style="height: 240px">业务员与公司LOGO合照（1张）</el-col>
                <el-col :span="18" class="audit-div" style="height: 240px">
                    <el-col :span="8" v-for=" pic in shop.salesman_logo_pic">
                        <div style="padding-top: 10px">
                            <el-button type="text" @click.native="handleShowImg(pic.url)">
                                <div>
                                    <img :src="pic.url" class="image audit-img">
                                </div>
                            </el-button>
                        </div>
                    </el-col>
                </el-col>
            </el-row>

            <el-row style="border-top: 1px solid">
                <el-col :span="6" class="audit-div" style="height: 240px">相关行业资质（1-3张）</el-col>
                <el-col :span="18" class="audit-div" style="height: 240px">
                    <el-col :span="8" v-for=" pic in shop.qualification_pic">
                        <div style="padding-top: 10px">
                            <el-button type="text" @click.native="handleShowImg(pic.url)">
                                <div>
                                    <img :src="pic.url" class="image audit-img">
                                </div>
                            </el-button>
                        </div>
                    </el-col>
                </el-col>
            </el-row>
            <el-row style="border-top: 1px solid">
                <el-col :span="6" class="audit-div" style="height: 100px">持证人数及备注</el-col><el-col :span="18" class="audit-div" style="height: 100px;overflow: auto; text-align: left">{{ shop.holder_no_remark }}</el-col>
            </el-row>

            <el-row style="border-top: 1px solid">
                <el-col :span="6" class="audit-div" style="height: 240px">商户合作协议（1张）</el-col>
                <el-col :span="18" class="audit-div" style="height: 240px">
                    <el-col :span="8" v-for=" pic in shop.protocol_pic">
                        <div style="padding-top: 10px">
                            <el-button type="text" @click.native="handleShowImg(pic.url)">
                                <div>
                                    <img :src="pic.url" class="image audit-img">
                                </div>
                            </el-button>
                        </div>
                    </el-col>
                </el-col>
            </el-row>
            <el-row style="border-top: 1px solid">
                <el-col :span="6" class="audit-div" style="height: 240px">征信业务授权书（1张）</el-col>
                <el-col :span="18" class="audit-div" style="height: 240px">
                    <el-col :span="8" v-for=" pic in shop.authorization_pic">
                        <div style="padding-top: 10px">
                            <el-button type="text" @click.native="handleShowImg(pic.url)">
                                <div>
                                    <img :src="pic.url" class="image audit-img">
                                </div>
                            </el-button>
                        </div>
                    </el-col>
                </el-col>
            </el-row>
            <el-row style="border-top: 1px solid">
                <el-col :span="6" class="audit-div" style="height: 240px">商户承诺书（1张）</el-col>
                <el-col :span="18" class="audit-div" style="height: 240px">
                    <el-col :span="8" v-for=" pic in shop.commitment_pic">
                        <div style="padding-top: 10px">
                            <el-button type="text" @click.native="handleShowImg(pic.url)">
                                <div>
                                    <img :src="pic.url" class="image audit-img">
                                </div>
                            </el-button>
                        </div>
                    </el-col>
                </el-col>
            </el-row>
            <el-row style="border-top: 1px solid">
                <el-col :span="6" class="audit-div" style="height: 240px">网银流水（最多50张）</el-col>
                <el-col :span="18" class="audit-div" style="height: 240px; overflow: auto">
                    <el-col :span="8" v-for=" pic in shop.bank_bill_pic">
                        <div style="padding-top: 10px">
                            <el-button type="text" @click.native="handleShowImg(pic.url)">
                                <div>
                                    <img :src="pic.url" class="image audit-img">
                                </div>
                            </el-button>
                        </div>
                    </el-col>
                </el-col>
            </el-row>

            <el-row style="border-top: 1px solid">
                <el-col :span="6" class="audit-div" style="height: 150px">商户可分期商品类别</el-col>
                <el-col :span="18" class="audit-div" style="height: 150px; overflow: auto">
                    <el-tree :data="shop_category" :props="defaultProps"></el-tree>
                </el-col>
            </el-row>
            <el-row style="border-top: 1px solid">
                <el-col :span="6" class="audit-div" >商户可分期数</el-col>
                <el-col :span="18" class="audit-div" style="text-align: left">
                    <span v-for="v in shop.period" v-text="v +'期、'"></span>
                </el-col>
            </el-row>
            <el-row style="border-top: 1px solid">
                <el-col :span="6" class="audit-div">商户所在地</el-col><el-col :span="18" class="audit-div">{{ shop.shop_area}}</el-col>
            </el-row>
            <!--公司基本信息：end-->

            <!--审核信息：beigin-->
            <el-row style="border-left: 1px solid #868686;border-top: 1px solid #868686">
                <h3 style="padding-left: 20px;"><span>审核信息</span></h3>
                <el-form  :model="form" ref="shopAuditForm" label-width="120px">
                    <el-form-item label="商户号" required>
                        <span>{{ shop.shop_no }}</span> <el-button type="primary" @click="" size="small">生成二维码</el-button>
                    </el-form-item>
                    <el-form-item label="商户二维码" required>
                        <!--<el-button type="primary" @click="">保存</el-button>-->
                    </el-form-item>
                    <el-form-item label="审核结果" required>
                        <el-radio-group v-model="form.state">
                            <el-radio :label="1">通过</el-radio>
                            <el-radio :label="2">失败</el-radio>
                        </el-radio-group>
                    </el-form-item>
                    <el-row>
                        <el-col :span="6">
                            <el-form-item label="总授信额度(万)" class="num-input" required>
                                <el-input-number v-model="form.total_quota" placeholder="" style="width:100px;" :controls="false" :min="0"></el-input-number>
                            </el-form-item>
                        </el-col>
                        <el-col :span="6">
                            <el-form-item label="每日限额(万)" class="num-input" required>
                                <el-input-number v-model="form.daily_limit_quota" placeholder="" style="width:100px;" :controls="false" :min="0"></el-input-number>
                            </el-form-item>
                        </el-col>
                        <el-col :span="6">
                            <el-form-item label="单笔限额(万)" class="num-input" required>
                                <el-input-number v-model="form.single_limit_quota" placeholder="" style="width:100px;" :controls="false" :min="0"></el-input-number>
                            </el-form-item>
                        </el-col>
                    </el-row>
                    <el-form-item label="风控意见" required>
                        <el-input type="textarea" v-model="form.opinion"  :rows="2" placeholder="请输入意见内容"  style="width:500px;"></el-input>
                    </el-form-item>

                    <el-form-item>
                        <el-button type="primary" @click="handleSubmit">提交</el-button>
                        <el-button type="primary" @click="$router.back()">返回</el-button>
                    </el-form-item>
                </el-form>
            </el-row>
            <!--审核信息结束：end-->
        </div>
        <!--图片放大：begin-->
        <el-dialog :visible.sync="dialogVisible" align="center" width="80%">
            <img :src="dialogImageUrl" alt="">
        </el-dialog>
        <!--图片放大：end-->
    </div>

</template>
<script>
  import apiBase from '../../apiBase';
  import { getJsonAndCheckSuccess, reportErrorMessage } from '../../ApiUtil';

  export default {
    name: 'shopList',
    data() {
      return {
        form: {
          state: 1,
          opinion: '',
          total_quota: 0,
          single_limit_quota: 0,
          daily_limit_quota: 0,
        },
        shop: [],
        shop_category: [],
        msg: '',
        dialogVisible: false,
        dialogImageUrl: '',
        defaultProps: {
          children: 'children',
          label: 'label',
        },
      };
    },
    mounted() {
      this.getData();
    },
    methods: {
      getData() {
        this.$http.get(`${apiBase}shop-detail/${this.$route.params.id}`)
          .then(response => response.json())
          .then((json) => {
            if (json.status === 'SUCCESS') {
              this.shop = json.results;
              this.shop_category = this.formatterCategory(this.shop.current_checked_category);
              // console.log(this.shop_category);
            }
          }).catch(() => {
            this.$message('系统错误');
          });
      },
      handleSubmit() {
        const params = {
          state: this.form.state,
          total_quota: this.form.total_quota,
          single_limit_quota: this.form.single_limit_quota,
          daily_limit_quota: this.form.daily_limit_quota,
          opinion: this.form.opinion,
        };
        this.$http.put(`${apiBase}shop-audit/${this.$route.params.id}`, params)
          .then(getJsonAndCheckSuccess)
          .then(() => {
            this.$message({
              type: 'success',
              message: '审核操作成功！',
            });
            this.$router.push({ path: '/shop' });
          }).catch(reportErrorMessage(this));
      },
      formatterCategory(List) {
        const CategoryList = [];
        let a = 0;
        List.forEach((v) => {
          if (v.parent_id === 0) {
            CategoryList[a] = {
              id: v.id,
              label: v.label,
              children: [],
            };
            let i = 0;
            List.forEach((vv) => {
              if (vv.parent_id === CategoryList[a].id) {
                CategoryList[a].children[i] = {
                  id: vv.id,
                  label: vv.label,
                };
                i += 1;
              }
            });
            a += 1;
          }
        });
        return CategoryList;
      },
      filter() {
        this.currentPage = 1;
        this.getData();
      },
      clearFilter() {
        this.form = {
          state: 1,
          opinion: '',
          total_quota: 0,
          single_limit_quota: 0,
          daily_limit_quota: 0,
        };
        this.getData();
      },
      handleShowImg(url) {
        this.dialogVisible = true;
        this.dialogImageUrl = url;
      },
    },
  };
</script>

<style>
    .audit-div{
        min-height: 41px;
        margin: auto;
        text-align: center;
        line-height: 40px;
        border-left: 1px solid #868686;
    }
    .audit-img{
        width: 200px;
        height: 200px;
    }
</style>