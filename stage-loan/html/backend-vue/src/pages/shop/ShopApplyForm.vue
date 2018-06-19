<template>
    <div style="background-color: #f2f2f2;padding: 20px;overflow: auto">
        <el-button type="success" style="margin-bottom: 5px" @click.native="$router.back(-1)">返回上一页</el-button>
        <el-form :model="form" ref="shopForm" label-width="150px" style="width:1000px;border: solid 1px;">
            <h4>商户信息</h4>
            <el-form-item label="商户名称" prop="shop_name" :rules="[{ required: true, message: '商户名称不能为空' }]">
                <el-input v-model="form.shop_name" placeholder="请输入" style="width:200px;" :maxlength="14"></el-input>
            </el-form-item>

            <!--企业法人：begin-->
            <el-row>
                <el-col :span="12">
                    <el-form-item label="企业法人姓名" prop="legal_person_name" :rules="[{ required: true, message: '企业法人姓名不能为空' }]">
                        <el-input v-model="form.legal_person_name" placeholder="请输入" style="width:200px;" :maxlength="10"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="法人手机号" prop="legal_person_mobile" :rules="[{ required: true, message: '法人手机号不能为空' }]">
                        <el-input type="tel" v-model="form.legal_person_mobile" placeholder="请输入" style="width:200px;" :maxlength="11"></el-input>
                    </el-form-item>
                </el-col>
            </el-row>
            <el-form-item label="法人身份证号" prop="legal_person_id_no" :rules="[{ required: true, message: '法人身份证号不能为空' }]">
                <el-input v-model="form.legal_person_id_no" placeholder="请输入" style="width:60%"></el-input>
            </el-form-item>


            <el-form-item label="上传身份证照片" prop="legal_person_id_card_pic" required>
                <el-row>
                    <el-col :span="10" align="center">
                        <el-upload
                                class="image-uploader-apply"
                                ref="image"
                                :action="url_action"
                                :show-file-list="false"
                                :withCredentials="true"
                                :on-success="handleIdCardPicFrontSuccess"
                                :before-upload="beforeAvatarUpload">
                            <img v-if="form.legal_person_id_card_pic_front" :src="form.legal_person_id_card_pic_front" class="image-shop">
                            <i v-else class="el-icon-plus image-uploader-apply-icon"></i>
                            <div slot="tip" class="el-upload__tip">正面</div>
                        </el-upload>
                    </el-col>
                    <el-col :span="10" align="center">
                        <el-upload
                                class="image-uploader-apply"
                                ref="image"
                                :action="url_action"
                                :show-file-list="false"
                                :withCredentials="true"
                                :on-success="handleIdCardPicBackSuccess"
                                :before-upload="beforeAvatarUpload">
                            <img v-if="form.legal_person_id_card_pic_back" :src="form.legal_person_id_card_pic_back" class="image-shop">
                            <i v-else class="el-icon-plus image-uploader-apply-icon"></i>
                            <div slot="tip" class="el-upload__tip">反面</div>
                        </el-upload>
                    </el-col>
                </el-row>
                <el-row>
                    <el-col :span="20" align="center"><span>请上传身份证照片（支持JPG/PNG）</span></el-col>
                </el-row>
            </el-form-item>
            <!--企业法人：end-->
            <div style="border-top: dotted 1px;margin-bottom: 10px"></div>

            <!--实际控制人：begin-->
            <el-form-item label="" prop="is_eq">
                <el-checkbox v-model="form.is_eq">实际控制人与法人一致</el-checkbox>
            </el-form-item>
            <el-row v-show="!form.is_eq">
                <el-col :span="12">
                    <el-form-item label="实际控制人姓名" prop="actual_controller_name" :rules="[{ required: !form.is_eq, message: '实际控制人姓名不能为空' }]">
                        <el-input v-model="form.actual_controller_name" placeholder="请输入" :maxlength="10" style="width:200px;" :disabled="form.is_eq"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="实际控制人手机号" prop="actual_controller_mobile" :rules="[{ required: !form.is_eq, message: '实际控制人手机号不能为空' }]">
                        <el-input v-model="form.actual_controller_mobile" placeholder="请输入" :maxlength="11" style="width:200px;" :disabled="form.is_eq"></el-input>
                    </el-form-item>
                </el-col>
            </el-row>
            <el-form-item label="实际控制人身份证号" prop="actual_controller_id_no" :rules="[{ required: !form.is_eq, message: '实际控制人身份证号不能为空' }]" v-show="!form.is_eq">
                <el-input v-model="form.actual_controller_id_no" placeholder="请输入" style="width:60%;" :disabled="form.is_eq"></el-input>
            </el-form-item>
            <el-form-item label="打款银行卡号" prop="bank_no" :rules="[{ required: true, message: '打款银行卡号不能为空' }]">
                <el-input v-model="form.bank_no" :maxlength="25" placeholder="请输入打款银行卡卡号，结算资金将汇入绑定的银行卡" style="width:450px;"></el-input>
            </el-form-item>
            <el-form-item label="打款银行名称" prop="bank_name" :rules="[{ required: true, message: '打款银行名称不能为空' }]">
                <el-input v-model="form.bank_name" placeholder="****银行" style="width:450px;" :maxlength="50"></el-input>
            </el-form-item>
            <el-form-item label="上传身份证照片" prop="actual_controller_id_card_pic" :rules="[{ required: !form.is_eq, message: '法人身份证照片不能为空' }]" v-show="!form.is_eq">
                <el-row>
                    <el-col :span="10" align="center">
                        <el-upload
                                class="image-uploader-apply"
                                ref="image"
                                :action="url_action"
                                :show-file-list="false"
                                :withCredentials="true"
                                :disabled="form.is_eq"
                                :on-success="handleActIdCardPicFrontSuccess"
                                :before-upload="beforeAvatarUpload">
                            <img v-if="form.actual_controller_id_card_pic_front" :src="form.actual_controller_id_card_pic_front"  alt="身份证正面照" class="image-shop">
                            <i v-else class="el-icon-plus image-uploader-apply-icon"></i>
                            <div slot="tip" class="el-upload__tip">正面</div>
                        </el-upload>
                    </el-col>
                    <el-col :span="10" align="center">
                        <el-upload
                                class="image-uploader-apply"
                                ref="image"
                                :action="url_action"
                                :show-file-list="false"
                                :withCredentials="true"
                                :disabled="form.is_eq"
                                :on-success="handleActIdCardPicBackSuccess"
                                :before-upload="beforeAvatarUpload">
                            <img v-if="form.actual_controller_id_card_pic_back" :src="form.actual_controller_id_card_pic_back" alt="身份证反面照" class="image-shop">
                            <i v-else class="el-icon-plus image-uploader-apply-icon"></i>
                            <div slot="tip" class="el-upload__tip">反面</div>
                        </el-upload>
                    </el-col>

                </el-row>
                <el-row>
                    <el-col :span="20" align="center"><span>请上传身份证照片（支持JPG/PNG）</span></el-col>
                </el-row>
            </el-form-item>
            <!--实际控制人：end-->

            <div style="border-top: dotted 1px;margin-bottom: 10px"></div>

            <!--企业基本信息：begin-->
            <el-row>
                <el-col :span="12">
                    <el-form-item label="企业联系人电子邮箱" prop="corporate_contacts_email" >
                        <el-input v-model="form.corporate_contacts_email" placeholder="企业联系人电子邮箱" :maxlength="30" style="width:200px;"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="企业联系人手机号" prop="corporate_contacts_mobile" :rules="[{ required: true, message: '企业联系人手机号不能为空' }]">
                        <el-input v-model="form.corporate_contacts_mobile" placeholder="请输入企业联系人手机号" style="width:200px;" :maxlength="11"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="企业详细地址" prop="shop_addr">
                        <el-input v-model="form.shop_addr" placeholder="请输入企业详细地址" style="width:80%;" :maxlength="50"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :span="24">
                    <el-form-item label="企业三证" prop="three_cards_pic">
                        <el-upload
                                ref="cards"
                                :action="url_action"
                                list-type="picture-card"
                                :file-list="form.three_cards_pic"
                                :on-preview="handlePicturePreview"
                                :on-change="handelW"
                                :withCredentials="true"
                                :limit="3"
                                :before-upload="beforeAvatarUpload"
                                :on-remove="handleRemove">
                            <i class="el-icon-plus"></i>
                            <div slot="tip" class="el-upload__tip">请上传企业三证照片（1~3张,支持JPG/PNG）</div>
                        </el-upload>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="注册资金（万）" prop="registered_capital" class="num-input">
                        <el-input-number v-model="form.registered_capital" placeholder="请输入企业注册资金" style="width:80%;" :controls="false" :min="0"></el-input-number>
                    </el-form-item>
                </el-col>

                <el-col :span="12">
                    <el-form-item label="注册时间" required>
                        <el-col :span="10">
                            <el-select v-model="form.registered_at_year" placeholder="请选择" clearable style="width:100px">
                                <el-option
                                        v-for="(v,k) in years"
                                        :label="String(v)"
                                        :value="String(v)">
                                </el-option>
                            </el-select>年
                        </el-col>
                        <el-col :span="12">
                            <el-select v-model="form.registered_at_month" placeholder="请选择" clearable style="width:100px">
                                <el-option
                                        v-for="m in 12"
                                        :label="m"
                                        :value="String(m)">
                                </el-option>
                            </el-select>月
                        </el-col>
                    </el-form-item>
                </el-col>

                <el-col :span="20">
                    <el-form-item label="办公面积(平方米)" prop="office_area" class="num-input">
                        <el-input-number v-model="form.office_area" placeholder="请输入企业办公面积" style="width:500px;" :controls="false" :min="0"></el-input-number>
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="职工人数" prop="staff_no" class="num-input">
                        <el-input-number v-model="form.staff_no" placeholder="请输入企业职工人数" style="width:500px;" :controls="false" :min="0"></el-input-number>
                    </el-form-item>
                </el-col>

                <el-col :span="24">
                    <el-form-item label="企业办公场所照片及房产租赁协议" prop="corporate_office_pic">
                        <el-upload
                                ref="corporate_office_pic"
                                :action="url_action"
                                list-type="picture-card"
                                :file-list="form.corporate_office_pic"
                                :on-preview="handlePicturePreview"
                                :on-change="handleCorporateOfficePic"
                                :withCredentials="true"
                                :before-upload="beforeAvatarUpload"
                                :limit="8"
                                :on-remove="handleRemoveCorporateOfficePic">
                            <i class="el-icon-plus"></i>
                            <div slot="tip" class="el-upload__tip">请上传企业办公场所照片及办公地点房产租赁协议（5~8张,支持JPG/PNG）</div>
                        </el-upload>
                    </el-form-item>
                </el-col>

                <el-col :span="24">
                    <el-form-item label="业务员与公司logo合照" prop="salesman_logo_pic">
                        <el-upload
                                ref="salesman_logo_pic"
                                :action="url_action"
                                list-type="picture-card"
                                :file-list="form.salesman_logo_pic"
                                :on-preview="handlePicturePreview"
                                :on-change="handleSalesmanLogo"
                                :withCredentials="true"
                                :before-upload="beforeAvatarUpload"
                                :limit="1"
                                :on-remove="handleRemoveSalesmanLogo">
                            <i class="el-icon-plus"></i>
                            <div slot="tip" class="el-upload__tip">请上传业务人员与公司LOGO合照（1张,支持JPG/PNG）</div>
                        </el-upload>
                    </el-form-item>
                </el-col>
                <!--图片放大：begin-->
                <el-dialog :visible.sync="dialogVisible" width="80%" align="center">
                    <img  :src="dialogImageUrl" alt="">
                </el-dialog>
                <!--图片放大：end-->
                <el-col :span="24">
                    <el-form-item label="相关行业资质及持证人数" prop="qualification_pic">
                        <el-upload
                                ref="qualification_pic"
                                :action="url_action"
                                list-type="picture-card"
                                :file-list="form.qualification_pic"
                                :on-preview="handlePicturePreview"
                                :on-change="handleQualificationPic"
                                :withCredentials="true"
                                :before-upload="beforeAvatarUpload"
                                :limit="3"
                                :on-remove="handleRemoveQualificationPic">
                            <i class="el-icon-plus"></i>
                            <div slot="tip" class="el-upload__tip">请上传企业相关资质照片（1~3张,支持JPG/PNG）</div>
                        </el-upload>
                    </el-form-item>
                </el-col>
                <el-col :span="24">
                    <el-form-item label="" prop="holder_no_remark">
                        <el-input v-model="form.holder_no_remark" placeholder="请输入持证人数及备注" style="width:500px;" :maxlength="20"></el-input>
                    </el-form-item>
                </el-col>

                <el-col :span="24">
                    <el-form-item label="商户合作协议" prop="protocol_pic">
                        <el-upload
                                ref="protocol_pic"
                                :action="url_action"
                                list-type="picture-card"
                                :file-list="form.protocol_pic"
                                :on-preview="handlePicturePreview"
                                :on-change="handleProtocolPic"
                                :withCredentials="true"
                                :before-upload="beforeAvatarUpload"
                                :limit="1"
                                :on-remove="handleRemoveProtocolPic">
                            <i class="el-icon-plus"></i>
                            <div slot="tip" class="el-upload__tip">请上传商户合作合同签章页照片（1张,支持JPG/PNG）</div>
                        </el-upload>
                    </el-form-item>
                </el-col>
                <el-col :span="24">
                    <el-form-item label="征信业务授权书" prop="authorization_pic">
                        <el-upload
                                ref="authorization_pic"
                                :action="url_action"
                                list-type="picture-card"
                                :file-list="form.authorization_pic"
                                :on-preview="handlePicturePreview"
                                :on-change="handleAuthorizationPic"
                                :withCredentials="true"
                                :before-upload="beforeAvatarUpload"
                                :limit="1"
                                :on-remove="handleRemoveAuthorizationPic">
                            <i class="el-icon-plus"></i>
                            <div slot="tip" class="el-upload__tip">请上传征信业务授权书照片（1张,支持JPG/PNG）</div>
                        </el-upload>
                    </el-form-item>
                </el-col>
                <el-col :span="24">
                    <el-form-item label="商户承诺书" prop="commitment_pic">
                        <el-upload
                                ref="commitment_pic"
                                :action="url_action"
                                list-type="picture-card"
                                :file-list="form.commitment_pic"
                                :on-preview="handlePicturePreview"
                                :on-change="handleCommitmentPic"
                                :withCredentials="true"
                                :before-upload="beforeAvatarUpload"
                                :limit="1"
                                :on-remove="handleRemoveCommitmentPic">
                            <i class="el-icon-plus"></i>
                            <div slot="tip" class="el-upload__tip">请上传商户承诺书照片（1张,支持JPG/PNG）</div>
                        </el-upload>
                    </el-form-item>
                </el-col>

                <el-col :span="24">
                    <el-form-item label="网银流水" prop="bank_bill_pic">
                        <el-upload
                                ref="bank_bill_pic"
                                :action="url_action"
                                list-type="picture-card"
                                :file-list="form.bank_bill_pic"
                                :on-preview="handlePicturePreview"
                                :on-change="handleBankBillPic"
                                :withCredentials="true"
                                :before-upload="beforeAvatarUpload"
                                :limit="50"
                                :on-remove="handleRemoveBankBillPic">
                            <i class="el-icon-plus"></i>
                            <div slot="tip" class="el-upload__tip">请上传近6个月网银流水（不超过50张,支持JPG/PNG）</div>
                        </el-upload>
                    </el-form-item>
                </el-col>

                <el-col :span="24">
                    <el-form-item label="商户可分期商品类别" prop="category" :rules="[{ required: true, message: '商户可分期商品类别不能为空' }]">
                        <el-select v-model="form.category" multiple placeholder="商品分类" clearable size="large" style="width: 600px;">
                            <el-option-group
                                    v-for="group in categories"
                                    :key="group.label"
                                    :label="group.label">
                                <el-option
                                        v-for="item in group.options"
                                        :key="item.value"
                                        :label="item.title"
                                        :value="item.id">
                                </el-option>
                            </el-option-group>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="24">
                    <el-form-item label="商户可分期数" prop="period" :rules="[{ required: true, message: '商户可分期数不能为空' }]">
                        <el-checkbox :indeterminate="isIndeterminate" v-model="checkAll" @change="handleCheckAllChange">全选</el-checkbox>
                        <div style="margin: 15px 0;"></div>
                        <el-checkbox-group v-model="form.period" @change="handleCheckedPeriodsChange">
                            <el-checkbox v-for="v in periods" :label="v" :key="v">{{v}}期</el-checkbox>
                        </el-checkbox-group>
                    </el-form-item>

                </el-col>

                <el-col :span="24">
                    <el-form-item label="商户所在地区" prop="city_id" :rules="[{ required: true, message: '商户所在地不能为空' }]">
                        <el-cascader
                                :options="province"
                                v-model="form.city_id"
                                @active-item-change="handleItemChange"
                                :props="props">
                        </el-cascader>
                    </el-form-item>
                </el-col>

                <el-col :span="12"></el-col>
            </el-row>
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
    name: 'ShopApplyForm',
    data() {
      return {
        url_action: `${apiBase}qiniu-upload`,
        role: this.$route.params.name,
        dialogImageUrl: [],
        dialogVisible: false,
        checkAll: false,
        isIndeterminate: true,
        years: [],
        form: {
          shop_name: '', // 商户名称
          legal_person_name: '', // 企业法人姓名
          legal_person_mobile: '', // 企业法人手机号
          legal_person_id_no: '', // 企业法人身份证号
          legal_person_id_card_pic_front: '', // 企业法身份证正面照
          legal_person_id_card_pic_back: '', // 企业法人身份证
          is_eq: false, // 实际控制人与法人一致
          actual_controller_name: '', // 企业法人姓名
          actual_controller_mobile: '', // 企业法人手机号
          actual_controller_id_no: '', // 企业法人身份证号
          actual_controller_id_card_pic_front: '', // 企业法身份证正面照
          actual_controller_id_card_pic_back: '', // 企业法人身份证
          corporate_contacts_email: '', // 企业联系邮箱
          corporate_contacts_mobile: '', // 企业联系手机号
          bank_name: '', // 银行卡名称
          bank_no: '', // 打款银行卡号
          shop_addr: '', // 企业详细地址
          three_cards_pic: [], // 企业三证
          registered_capital: '', // 注册资金
          registered_at_year: 2017, // 注册年
          registered_at_month: 6, // 注册月
          office_area: '', // 办公面积
          staff_no: '', // 职工人数
          corporate_office_pic: [], // 企业办公环境以及房屋租赁
          salesman_logo_pic: [], // 业务经理与公司logo合照
          qualification_pic: [],
          holder_no_remark: '',
          protocol_pic: [],
          authorization_pic: [],
          commitment_pic: [],
          bank_bill_pic: [],
          category: [],
          city_id: [],
          period: [],
        },
        periods: [],
        validateRes: true,
        datas: [],
        province: [],
        // 商户所在地测试数据
        props: {
          value: 'value',
          label: 'label',
          children: 'cities',
        },
        categories: [],
      };
    },
    mounted() {
      this.yearList();
      if (this.$route.params.id) {
        this.$http.get(`${apiBase}shop-detail/${this.$route.params.id}`)
          .then(getJsonAndCheckSuccess)
          .then((json) => {
            if (json.results.city_id) {
              this.handleItemChange(json.results.city_id[0]);
            }
            this.form = json.results;
            // console.log(this.form.category);
          }).catch(reportErrorMessage(this));
      }
      this.getData();
    },
    methods: {
      // 身份证上传
      handleIdCardPicFrontSuccess(res) {
        this.form.legal_person_id_card_pic_front = res.url;
      },
      handleIdCardPicBackSuccess(res) {
        this.form.legal_person_id_card_pic_back = res.url;
      },
      handleActIdCardPicFrontSuccess(res) {
        this.form.actual_controller_id_card_pic_front = res.url;
      },
      handleActIdCardPicBackSuccess(res) {
        this.form.actual_controller_id_card_pic_back = res.url;
      },
      // 图片上传之前的校验
      beforeAvatarUpload(file) {
        const isJPG = file.type === 'image/jpeg';
        const isPNG = file.type === 'image/png';
        const isLt3M = file.size / 1024 / 1024 < 3;
        if (!(isJPG || isPNG)) {
          this.$message.error('上传图片只能是 JPG/PNG 格式!');
        }
        if (!isLt3M) {
          this.$message.error('上传图片大小不能超过 3MB!');
        }
        return (isJPG || isPNG) && isLt3M;
      },
      getData() {
        this.$http.get(`${apiBase}need-msg`)
          .then(getJsonAndCheckSuccess)
          .then((response) => {
            // this.datas = response.results.category;
            this.categories = response.results.category;
            this.province = response.results.province;
            this.periods = response.results.period;
            this.cate = response.results.cate;
          }).catch(reportErrorMessage(this));
      },
      handleSubmit() {
        this.formValidate();
        if (!this.validateRes) {
          this.validateRes = true;
          return;
        }
        const params = {
          shop_name: this.form.shop_name, // 商户名称
          legal_person_name: this.form.legal_person_name, // 企业法人姓名
          legal_person_mobile: this.form.legal_person_mobile, // 企业法人手机号
          legal_person_id_no: this.form.legal_person_id_no, // 企业法人身份证号
          legal_person_id_card_pic_front: this.form.legal_person_id_card_pic_front, // 企业法身份证正面照
          legal_person_id_card_pic_back: this.form.legal_person_id_card_pic_back, // 企业法人身份证
          is_eq: this.form.is_eq, // 实际控制人与法人一致
          actual_controller_name: this.form.actual_controller_name,
          actual_controller_mobile: this.form.actual_controller_mobile,
          actual_controller_id_no: this.form.actual_controller_id_no,
          actual_controller_id_card_pic_front: this.form.actual_controller_id_card_pic_front,
          actual_controller_id_card_pic_back: this.form.actual_controller_id_card_pic_back,
          corporate_contacts_email: this.form.corporate_contacts_email, // 企业联系邮箱
          corporate_contacts_mobile: this.form.corporate_contacts_mobile, // 企业联系手机号
          bank_name: this.form.bank_name, // 银行卡名称
          bank_no: this.form.bank_no, // 打款银行卡号
          shop_addr: this.form.shop_addr, // 企业详细地址
          registered_capital: this.form.registered_capital, // 注册资金
          registered_at_year: this.form.registered_at_year, // 注册年
          registered_at_month: this.form.registered_at_month, // 注册月
          office_area: this.form.office_area, // 办公面积
          staff_no: this.form.staff_no, // 职工人数
          holder_no_remark: this.form.holder_no_remark,
          category: this.form.category,
          city_id: this.form.city_id,
          period: this.form.period,
        };
        params.three_cards_pic = this.formatterUploadImageUrl(this.form.three_cards_pic);
        params.corporate_office_pic = this.formatterUploadImageUrl(this.form.corporate_office_pic);
        params.salesman_logo_pic = this.formatterUploadImageUrl(this.form.salesman_logo_pic);
        params.qualification_pic = this.formatterUploadImageUrl(this.form.qualification_pic);
        params.protocol_pic = this.formatterUploadImageUrl(this.form.protocol_pic);
        params.authorization_pic = this.formatterUploadImageUrl(this.form.authorization_pic);
        params.commitment_pic = this.formatterUploadImageUrl(this.form.commitment_pic);
        params.bank_bill_pic = this.formatterUploadImageUrl(this.form.bank_bill_pic);
        if (this.$route.params.id) { // 修改时
          this.$http.put(`${apiBase}shop-update/${this.$route.params.id}`, params)
            .then(getJsonAndCheckSuccess)
            .then(() => {
              this.$message({
                type: 'success',
                message: '修改成功！',
              });
              this.$router.push({ path: '/shop' });
            }).catch(reportErrorMessage(this));
        } else {
          this.$http.post(`${apiBase}shop-apply`, params)
            .then(getJsonAndCheckSuccess)
            .then(() => {
              this.$message({
                type: 'success',
                message: '保存成功！',
              });
              this.$router.push({ path: '/shop' });
            }).catch(reportErrorMessage(this));
        }
      },
      formatterUploadImageUrl(fileList) {
        const list = [];
        fileList.forEach((v, k) => {
          if (v.response) {
            list[k] = v.response.url;
          } else {
            list[k] = v.url;
          }
        });
        return list;
      },
      // 企业三证
      handleRemove(file, fileList) {
        this.form.three_cards_pic = fileList;
      },
      handelW(file, fileList) {
        this.form.three_cards_pic = fileList;
      },
      // 业务员与公司合照
      handleRemoveSalesmanLogo(file, fileList) {
        this.form.salesman_logo_pic = fileList;
      },
      handleSalesmanLogo(file, fileList) {
        this.form.salesman_logo_pic = fileList;
      },
      // 办公场所
      handleRemoveCorporateOfficePic(file, fileList) {
        this.form.corporate_office_pic = fileList;
      },
      handleCorporateOfficePic(file, fileList) {
        this.form.corporate_office_pic = fileList;
      },
      // 资质照
      handleRemoveQualificationPic(file, fileList) {
        this.form.qualification_pic = fileList;
      },
      handleQualificationPic(file, fileList) {
        this.form.qualification_pic = fileList;
      },
      // 商户合作协议
      handleRemoveProtocolPic(file, fileList) {
        this.form.protocol_pic = fileList;
      },
      handleProtocolPic(file, fileList) {
        this.form.protocol_pic = fileList;
      },
      // 征信授权
      handleRemoveAuthorizationPic(file, fileList) {
        this.form.authorization_pic = fileList;
      },
      handleAuthorizationPic(file, fileList) {
        this.form.authorization_pic = fileList;
      },
      // 商户承诺书
      handleRemoveCommitmentPic(file, fileList) {
        this.form.commitment_pic = fileList;
      },
      handleCommitmentPic(file, fileList) {
        this.form.commitment_pic = fileList;
      },
      // 网银流水
      handleRemoveBankBillPic(file, fileList) {
        this.form.bank_bill_pic = fileList;
      },
      handleBankBillPic(file, fileList) {
        this.form.bank_bill_pic = fileList;
      },
      handlePicturePreview(file) {
        this.dialogImageUrl = file.url;
        this.dialogVisible = true;
      },
      // 图片提交张数检验
      formValidate() {
        // 判断输入空值
        // console.log(this.form);
        if (!this.form.shop_name) {
          this.validateRes = false;
          this.$message.error('商户名称不能为空');
        } else if (!this.form.legal_person_name) {
          this.validateRes = false;
          this.$message.error('法人姓名不能为空');
        } else if (!this.form.legal_person_mobile) {
          this.validateRes = false;
          this.$message.error('企业法人手机号码不能为空');
        } else if (!/0?(13|14|15|17|18)[0-9]{9}/.test(this.form.legal_person_mobile)) {
          this.validateRes = false;
          this.$message.error('请填写正确企业法人手机号码');
        } else if (!this.form.legal_person_id_no) {
          this.validateRes = false;
          this.$message.error('企业法人身份证号不能为空');
        } else if (!this.form.legal_person_id_card_pic_front) {
          this.validateRes = false;
          this.$message.error('请上传企业法人身份证正面照');
        } else if (!this.form.legal_person_id_card_pic_back) {
          this.validateRes = false;
          this.$message.error('请上传企业法人身份证反面照');
        } else if (!this.form.corporate_contacts_mobile) {
          this.validateRes = false;
          this.$message.error('请填企业联系人手机号');
        } else if (!this.form.bank_name) {
          this.validateRes = false;
          this.$message.error('请填写银行卡名称');
        } else if (!this.form.bank_no) {
          this.validateRes = false;
          this.$message.error('请填写打款银行卡卡号');
        } else if (this.form.category.length < 1) {
          this.validateRes = false;
          this.$message.error('请勾选商户可分期商品分类');
        } else if (this.form.period.length < 1) {
          this.validateRes = false;
          this.$message.error('请勾选商户可分期期数');
        } else if (this.form.city_id.length < 1) {
          this.validateRes = false;
          this.$message.error('请选择商户所在地');
        }
        /* if (this.validateRes) {
          const reg = /^1[3|4|5|7|8][0-9]\d{4,8}$/;
          if (!reg.test((this.form.legal_person_mobile))) {
            this.validateRes = false;
            this.$message.error('企业法人手机号输入有误，请重新输入');
          }
        }*/
      },
      getYear() {
        const date = new Date();
        return date.getFullYear();
      },
      yearList() {
        const year = Number(this.getYear());
        let a = 0;
        const list = [];
        for (let i = year; i > 1960; i -= 1) {
          list[a] = i;
          a += 1;
        }
        this.years = list;
      },
      // 商户所在地测试
      handleItemChange(val) {
        setTimeout(() => {
          // 如果已经有记录不需要查
          this.province.forEach((v, k) => {
            if (Number(v.value) === Number(val) && !v.cities.length) {
              this.$http.get(`${apiBase}get-cities/${val}`)
                .then(getJsonAndCheckSuccess)
                .then((response) => {
                  this.province[k].cities = response.results;
                }).catch(reportErrorMessage(this));
            }
          });
        }, 300);
      },
      handleCheckAllChange(val) {
        this.form.period = val ? this.periods : [];
        this.isIndeterminate = false;
      },
      handleCheckedPeriodsChange(value) {
        const checkedCount = value.length;
        this.checkAll = checkedCount === this.periods.length;
        this.isIndeterminate = checkedCount > 0 && checkedCount < this.periods.length;
      },
    },
  };
</script>
<style type="text/css">
    .image-uploader-apply .el-upload {
        border: 1px dashed #331919;
        border-radius: 6px;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    .image-uploader-apply-icon {
        font-size: 28px;
        color: #8c939d;
        width: 300px;
        height: 178px;
        line-height: 178px;
        text-align: center
    }
    .image-shop {
        width: 300px;
        height: 178px;
        display: block;
    }
    .info-test{
        border-radius: 10px;
        line-height: 20px;
        padding: 10px;
        margin: 10px;
        background-color: #ffffff;
    }
    .image-uploader-apply .el-upload:hover {
        border-color: #20a0ff;
    }
    .num-input .el-input-number .el-input input{
        text-align: left !important;
    }
</style>