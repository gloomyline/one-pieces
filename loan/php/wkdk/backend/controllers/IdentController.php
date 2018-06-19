<?php
namespace backend\controllers;

use backend\bases\BackendController;
use common\models\User;
use common\models\UserAdditionalModel;
use common\models\UserBankModel;
use common\models\UserMobileReportModel;
use common\models\UserModel;
use common\models\UserLimuModel;
use yii\helpers\Json;
use Yii;

class IdentController extends BackendController
{
    /**
     * 认证中心展示
     * @return string
     */
    public function actionIndex()
    {
        $result = $data = [];
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $realName = $request->get('real_name', ''); // 真实姓名
        $userName = $request->get('user_name', ''); // 用户名
        $data = UserModel::getAuthCenterList($offset, $limit, $realName, $userName); // 获取认证中心列表数据
        foreach ($data['result'] as $row) {
            $result[] = [
                'id' => $row->id,
                'user_name' => $row->mobile,
                'real_name' => isset($row->real_name) ? $row->real_name : '',
                'is_identity_auth' => $row->is_identity_auth, // 身份认证
                'is_profile_auth' => $row->is_profile_auth, // 个人信息认证
                'is_bankcard_auth' => $row->is_bankcard_auth, // 银行卡认证
                'is_phone_auth' => $row->is_phone_auth, //手机认证
                'is_increase_quota' => isset($row->quotaApply) ? 1 : 0,// 提升额度
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $data['count'],
            'results' => $result
        ]);
    }

    /**
     * 身份认证列表
     * @return string
     */
    public function actionIdentityAuth()
    {
        $result = $data = [];
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $realName = $request->get('real_name', ''); // 真实姓名
        $userName = $request->get('user_name', ''); // 用户名
        $identityNo = $request->get('identity_no', ''); // 身份证号
        $state = $request->get('state', '');  // 状态
        $data = UserModel::getUserIdentityCardList($offset, $limit, $realName, $userName, $identityNo, $state); // 获取用户身份认证数据
        foreach ($data['result'] as $row) {
            $result[] = [
                'id' => $row->id,
                'user_name' => $row->mobile,
                'real_name' => isset($row->real_name) ? $row->real_name : '',
                'identity_no' => isset($row->userIdentityCard->identity_no) ? $row->userIdentityCard->identity_no : '',
                'state' => $row->is_identity_auth, // 身份认证
                'created_at' => isset($row->userIdentityCard->created_at) ? $row->userIdentityCard->created_at : '', // 添加时间
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $data['count'],
            'results' => $result
        ]);
    }

    /**
     * 个人信息认证列表
     * @return string
     */
    public function actionProfileAuth()
    {
        $result = $data = [];
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $realName = $request->get('real_name', ''); // 真实姓名
        $userName = $request->get('user_name', ''); // 用户名
        $state = $request->get('state', '');  // 状态
        $data = UserModel::getProfileAuthList($offset, $limit, $realName, $userName, $state); // 获取用户个人信息认证数据
        foreach ($data['result'] as $row) {
            $result[] = [
                'id' => $row->id,
                'mobile' => $row->mobile,
                'user_name' => $row->mobile,
                'real_name' => $row->real_name ?? '',
                'live_area' => $row->userBasic->live_area ?? '',
                'live_addr' => $row->userBasic->live_addr ?? '',
                'live_time' => $row->userBasic->live_time ?? '',
                'created_at' => $row->userBasic->created_at ?? '', // 添加时间
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $data['count'],
            'results' => $result
        ]);
    }

    /**
     * 银行认证列表
     * @return string
     */
    public function actionBankAuth()
    {
        $result = $data = [];
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $realName = $request->get('real_name', ''); // 真实姓名
        $userName = $request->get('user_name', ''); // 用户名
        $bankNo = $request->get('bank_no', ''); // 用户名
        $state = $request->get('state', '');  // 状态
        $data = UserBankModel::getBankAuthList($offset, $limit, $realName, $userName, $bankNo, $state); // 获取用户银行认证数据
        foreach ($data['result'] as $row) {
            $result[] = [
                'id' => $row->id,
                'mobile' => $row->user->mobile ?? '',
                'user_name' => $row->user->mobile ?? '',
                'real_name' => $row->userIdentityCard->real_name ??'',
                'identity_no' => $row->userIdentityCard->identity_no ?? '',
                'bank_no' => $row->bank_no ?? '',
                'opening_bank_name' => $row->bank_name ?? '',
                'state' => $row->state, // 银行卡认证
                'is_default' => $row->is_default,
                'created_at' => $row->created_at ?? '',
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $data['count'],
            'results' => $result
        ]);
    }

    /**
     * 手机认证
     * @return string
     */
    public function actionMobileAuth()
    {
        $result = $data = [];
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $realName = $request->get('real_name', ''); // 真实姓名
        $userName = $request->get('user_name', ''); // 用户名
        $state = $request->get('state', '');  // 状态
        $data = UserMobileReportModel::getMobileAuthList($offset, $limit, $realName, $userName, $state); // 获取手机认证列表数据
        foreach ($data['result'] as $row) {
            $result[] = [
                'id' => $row->id,
                'user_name' => $row->user->mobile, // 用户名
                'mobile' => $row->mobile, // 手机号
                'real_name' => $row->user->real_name ?? '', // 真实姓名
                'has_report' => $row->has_report, // 是否生成手机报告 第三方
                'state' => $row->state, // 手机认证审核状态
                'is_phone_auth' => $row->user->is_phone_auth, //user表用户手机认证状态
                'created_at' => $row->created_at,
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $data['count'],
            'results' => $result
        ]);
    }

    /**
     * 用户手机认证生成报告
     */
    public function actionMobileReport()
    {
        $request = Yii::$app->request;
        $userMobileId = $request->get('id'); // 手机认证记录Id
        $callRecordOffset = $request->get('call_record_offset', 0); // 偏移量
        $callRecordLimit = $request->get('call_record_limit', 10); // 查询的数目

        $cantactOffset = $request->get('cantact_offset', 0); // 偏移量
        $cantactLimit = $request->get('cantact_limit', 10); // 查询的数目

        $smsOffset = $request->get('sms_offset', 0); // 偏移量
        $smsLimit = $request->get('sms_limit', 10); // 查询的数目
        if (!$userMobileId) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        $userMobile = UserMobileReportModel::getMobileCredit($userMobileId); // 获取手机认证记录
        $report = [
            'report' => Json::decode($userMobile->report) ?? '',
            'content' => Json::decode($userMobile->content) ?? ''
        ];

        if ($report['report']) {
            // 关联信息
            $report['report']['relationInfo']['identiyNos'] = implode(',', $report['report']['relationInfo']['identiyNos']); // 关联身份证信息
            $report['report']['relationInfo']['mobiles'] = implode(',', $report['report']['relationInfo']['mobiles']); // 关联手机号信息
            $report['report']['relationInfo']['homeAddresses'] = implode(',', $report['report']['relationInfo']['homeAddresses']); // 关联家庭地址信息

            // 通话联系人分析处理
            if ($report['report']['contactAnalysis']) {
                $report['report']['has_more'] = true; // 标识是否存在更多联系人分析 true-存在 false-不存在
                if (!isset($report['report']['contactAnalysis'][$cantactOffset + $cantactLimit])) {
                    $report['report']['has_more'] = false;
                }
                $report['report']['contactAnalysis'] = array_slice($report['report']['contactAnalysis'], $cantactOffset, $cantactLimit);
            }
        }
        if ($report['content']) {
            // 近6个月通话记录
            if ($report['content']['callRecordInfo']) {
                $report['content']['has_more'] = true; // 标识是否存在更多通话记录 true-存在 false-不存在
                if (!isset($report['content']['callRecordInfo'][$callRecordOffset + $callRecordLimit])) {
                    $report['content']['has_more'] = false;
                }
                $report['content']['callRecordInfo'] = array_slice($report['content']['callRecordInfo'], $callRecordOffset, $callRecordLimit);
            }

            // 近6个月短信记录
            if ($report['content']['smsInfo']) {
                $report['content']['sms_has_more'] = true; // 标识是否存在更多短信记录 true-存在 false-不存在
                if (!isset($report['content']['smsInfo'][$smsOffset + $smsLimit])) {
                    $report['content']['sms_has_more'] = false;
                }
                $report['content']['smsInfo'] = array_slice($report['content']['smsInfo'], $smsOffset, $smsLimit);
            }
        }

        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'results' => $report ?? []
        ]);
    }

    /**
     * 京东认证
     */
    public function actionJdAuth()
    {
        $result = $data = [];
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $userName = trim($request->get('user_name', '')); // 真实姓名
        $mobile = trim($request->get('mobile', '')); // 身份证号
        $state = trim($request->get('state', ''));  // 状态
        $result = UserLimuModel::getLimuAuthList($limit, $offset, $userName, $mobile, $state, UserLimuModel::TYPE_JD);

        foreach ($result['result'] as $row) {
            $data[] = [
                'id' => $row->id,
                'user_name' => $row->user->real_name ?? '', // 用户姓名
                'mobile' => $row->mobile, // 手机号码
                'state' => $row->state, // 状态：pass-认证通过 nopass-认证不通过 busy-待认证
                'has_report' => $row->has_report, // 是否已生成报告 0-未生成 1-已生成
                'created_at' => $row->created_at, // 添加时间
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $result['count'],
            'results' => $data
        ]);
    }

    /**
     * 淘宝、京东、学历、信用卡、网银流水账单详情
     */
    public function actionLimuInfo()
    {
        $request = Yii::$app->request;
        $id = $request->get('id', '');
        if (empty($id)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        // 京东订单信息分页
        $jdOrderOffset = $request->get('jd_order_offset', 0);
        $jdOrderLimit = $request->get('jd_order_limit', 10);
        //京东收货地址分页
        $jdAddrOffset = $request->get('jd_addr_offset', 0);
        $jdAddrLimit = $request->get('jd_addr_limit', 10);

        //淘宝订单信息分页
        $tbOrderOffset = $request->get('tb_order_offset', 0);
        $tbOrderLimit = $request->get('tb_order_limit', 10);
        //淘宝收货地址分页
        $tbAddrOffset = $request->get('tb_addr_offset', 0);
        $tbAddrLimit = $request->get('tb_addr_limit', 10);

        $detail = UserLimuModel::getUserLimuById($id); //查询京东明细
        $result = isset($detail->content) ? Json::decode($detail->content) : '';

        if (isset($result['studentStatusInfo']['personalPhotos'])) {
            $result['studentStatusInfo']['personalPhotos'] =  'data:image/jpg;base64,' . $result['studentStatusInfo']['personalPhotos'];
        }
        if (isset($result['educationInfo']['personalPhotos'])) {
            $result['educationInfo']['personalPhotos'] =  'data:image/jpg;base64,' . $result['educationInfo']['personalPhotos'];
        }
        if ($detail->platform_type == UserLimuModel::TYPE_JD) {
            // 京东 收货地址
            if (isset($result['addressInfo']) && $result['addressInfo']) {
                $result['jd_addr_has_more'] = true; // 标识是否存在更多收货地址 true-存在 false-不存在
                if (!isset($result['addressInfo'][$jdAddrOffset + $jdAddrLimit])) {
                    $result['jd_addr_has_more'] = false;
                }
                $result['addressInfo'] = array_slice($result['addressInfo'], $jdAddrOffset, $jdAddrLimit);
            }
            // 京东订单信息
            if (isset($result['orderDetail']) && $result['orderDetail']) {
                $result['jd_order_has_more'] = true; // 标识是否存在更多订单详情 true-存在 false-不存在
                if (!isset($result['orderDetail'][$jdOrderOffset + $jdOrderLimit])) {
                    $result['jd_order_has_more'] = false;
                }
                $result['orderDetail'] = array_slice($result['orderDetail'], $jdOrderOffset, $jdOrderLimit);
            }

        }
        if ($detail->platform_type == UserLimuModel::TYPE_TAOBAO) {
            // 淘宝 收货地址
            if (isset($result['addresses']) && $result['addresses']) {
                $result['tb_addr_has_more'] = true; // 标识是否存在更多收货地址 true-存在 false-不存在
                if (!isset($result['addresses'][$tbAddrOffset + $tbAddrLimit])) {
                    $result['tb_addr_has_more'] = false;
                }
                $result['addresses'] = array_slice($result['addresses'], $tbAddrOffset, $tbAddrLimit);
            }
            // 淘宝订单信息
            if (isset($result['orderDetails']) && $result['orderDetails']) {
                $result['tb_order_has_more'] = true; // 标识是否存在更多订单详情 true-存在 false-不存在
                if (!isset($result['orderDetails'][$tbOrderOffset + $tbOrderLimit])) {
                    $result['tb_order_has_more'] = false;
                }
                $result['orderDetails'] = array_slice($result['orderDetails'], $tbOrderOffset, $tbOrderLimit);
            }
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'results' => $result
        ]);
    }
    /**
     * 淘宝认证
     */
    public function actionTaobaoAuth()
    {
        $data = [];
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $userName = trim($request->get('user_name', '')); // 真实姓名
        $mobile = trim($request->get('mobile', '')); // 身份证号
        $state = trim($request->get('state', ''));  // 状态
        $result = UserLimuModel::getLimuAuthList($limit, $offset, $userName, $mobile, $state, UserLimuModel::TYPE_TAOBAO);

        foreach ($result['result'] as $row) {
            $data[] = [
                'id' => $row->id,
                'user_name' => $row->user->real_name ?? '', // 用户姓名
                'mobile' => $row->mobile, // 手机号码
                'state' => $row->state, // 状态：pass-认证通过 nopass-认证不通过 busy-待认证
                'has_report' => $row->has_report, // 是否已生成报告 0-未生成 1-已生成
                'created_at' => $row->created_at, // 添加时间
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $result['count'],
            'results' => $data
        ]);
    }
    /**
     * 学历认证
     */
    public function actionEduAuth()
    {
        $data = [];
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $userName = trim($request->get('user_name', '')); // 真实姓名
        $mobile = trim($request->get('mobile', '')); // 身份证号
        $state = trim($request->get('state', ''));  // 状态
        $result = UserLimuModel::getLimuAuthList($limit, $offset, $userName, $mobile, $state, UserLimuModel::TYPE_EDUCATION);

        foreach ($result['result'] as $row) {
            $data[] = [
                'id' => $row->id,
                'user_name' => $row->user->real_name ?? '', // 用户姓名
                'mobile' => $row->mobile, // 手机号码
                'state' => $row->state, // 状态：pass-认证通过 nopass-认证不通过 busy-待认证
                'has_report' => $row->has_report, // 是否已生成报告 0-未生成 1-已生成
                'created_at' => $row->created_at, // 添加时间
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $result['count'],
            'results' => $data
        ]);
    }

    /**
     * 信用卡账单
     */
    public function actionBillAuth()
    {
        $data = [];
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $userName = trim($request->get('user_name', '')); // 真实姓名
        $mobile = trim($request->get('mobile', '')); // 身份证号
        $state = trim($request->get('state', ''));  // 状态
        $result = UserLimuModel::getLimuAuthList($limit, $offset, $userName, $mobile, $state, UserLimuModel::TYPE_BILL);

        foreach ($result['result'] as $row) {
            $data[] = [
                'id' => $row->id,
                'user_name' => $row->user->real_name ?? '', // 用户姓名
                'mobile' => $row->mobile, // 手机号码
                'state' => $row->state, // 状态：pass-认证通过 nopass-认证不通过 busy-待认证
                'has_report' => $row->has_report, // 是否已生成报告 0-未生成 1-已生成
                'created_at' => $row->created_at, // 添加时间
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $result['count'],
            'results' => $data
        ]);
    }

    /**
     * 网银流水
     */
    public function actionEbankAuth()
    {
        $data = [];
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $userName = trim($request->get('user_name', '')); // 真实姓名
        $mobile = trim($request->get('mobile', '')); // 身份证号
        $state = trim($request->get('state', ''));  // 状态
        $result = UserLimuModel::getLimuAuthList($limit, $offset, $userName, $mobile, $state, UserLimuModel::TYPE_EBANK);

        foreach ($result['result'] as $row) {
            $data[] = [
                'id' => $row->id,
                'user_name' => $row->user->real_name ?? '', // 用户姓名
                'mobile' => $row->mobile, // 手机号码
                'state' => $row->state, // 状态：pass-认证通过 nopass-认证不通过 busy-待认证
                'has_report' => $row->has_report, // 是否已生成报告 0-未生成 1-已生成
                'created_at' => $row->created_at, // 添加时间
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $result['count'],
            'results' => $data
        ]);
    }

    /**
     * 删除京东、淘宝、学历、信用卡账单、网银流水认证记录
     * @return string
     */
    public function actionDelUserLimu()
    {
        $request = Yii::$app->request;
        $id = $request->post('limu_id', '');
        if (empty($id)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        if (UserLimuModel::delUserLimuById($id)) {
            return Json::encode([
                'status' => self::STATUS_SUCCESS,
                'error_message' => '删除成功'
            ]);
        }
        return Json::encode([
            'status' => self::STATUS_FAILURE,
            'error_message' => "操作失败"
        ]);
    }

    /**
     * 用户人际关系
     * @return string
     */
    public function actionRelation()
    {
        $result = $data = [];
        $request = Yii::$app->request;
        $userId = $request->get('user_id', ''); // 用户id
        if (!$userId) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        $result = UserAdditionalModel::getUserAdditionalByUserId($userId);
        if ($result) {
            $data = [
                'linkman_relation_fir' => isset($result->linkman_relation_fir) ? $result->linkman_relation_fir : '', // 1号联系人与本人关系
                'linkman_name_fir' => isset($result->linkman_name_fir) ? $result->linkman_name_fir : '', // 1号联系人姓名
                'linkman_tel_fir' => isset($result->linkman_tel_fir) ? $result->linkman_tel_fir : '', // 1号联系人手机号码
                'linkman_relation_sec' => isset($result->linkman_relation_sec) ? $result->linkman_relation_sec : '', // 2号联系人与本人关系
                'linkman_name_sec' => isset($result->linkman_name_sec) ? $result->linkman_name_sec : '', // 2号联系人姓名
                'linkman_tel_sec' => isset($result->linkman_tel_sec) ? $result->linkman_tel_sec : '', // 2号联系人手机号码
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'results' => $data
        ]);
    }

    /**
     * 用户工作信息
     * @return string
     */
    public function actionWork()
    {
        $result = $data = [];
        $request = Yii::$app->request;
        $userId = $request->get('user_id', ''); // 用户id
        if (!$userId) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        $result = UserAdditionalModel::getUserAdditionalByUserId($userId);
        if ($result) {
            $data = [
                'industry' => isset($result->industry) ? $result->industry : '', // 从事行业
                'position' => isset($result->position) ? $result->position : '', // 工作岗位
                'company_name' => isset($result->company_name) ? $result->company_name : '', // 单位名称
                'company_area' => isset($result->company_area) ? $result->company_area : '', // 单位所在地
                'company_addr' => isset($result->company_addr) ? $result->company_addr : '', // 详细地址
                'company_tel' => isset($result->company_tel) ? $result->company_tel : '', // 电话
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'results' => $data
        ]);
    }

    /**
     *
     *提升额度列表
     *
     * @return string
     */
    public function actionIncreaseQuota()
    {
        $results = $data = [];
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $realName = $request->get('real_name', '');
        $mobile = $request->get('mobile', '');
        $results = UserModel::getIncreaseQuotaList($offset, $limit, $realName, $mobile, ['id' => SORT_ASC]);
        foreach ($results['result'] as $row) {
            $data[] = [
                'id' => $row->id,
                'mobile' => $row->mobile, // 用户名
                'real_name' => $row->real_name ?? '', // 真实姓名
                'is_edu_auth' => $row->is_edu_auth, // 学历认证
                'is_jd_auth' => $row->is_jd_auth, // 京东认证
                'is_taobao_auth' => $row->is_taobao_auth, // 淘宝认证
                'bankcard' => isset($row->userBasic->bankcard) ? 1 : 0, // 常用信用卡
                'wechat' => isset($row->userBasic->wechat) ? 1 : 0, // weixin
                'qq' => isset($row->userBasic->qq) ? 1 : 0, // QQ
                'is_ebank_auth' => $row->is_ebank_auth, // 网银流水
                'is_bill_auth' => $row->is_bill_auth, // 银行卡账单
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $results['count'],
            'results' => $data
        ]);
    }

    /**
     * 手机认证记录删除
     * @return string
     */
    public function actionDelUserMobileAuth()
    {
        $id = Yii::$app->request->post('mobile_id', '');
        if (empty($id)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        if (UserMobileReportModel::delUserMobileReportById($id)) {
            return Json::encode([
                'status' => self::STATUS_SUCCESS,
                'error_message' => '删除成功'
            ]);
        }
        return Json::encode([
            'status' => self::STATUS_FAILURE,
            'error_message' => "操作失败"
        ]);
    }

    /**
     * 银行卡认证记录删除
     * @return string
     */
    public function actionDelUserBankAuth()
    {
        $id = Yii::$app->request->post('bank_id', '');
        if (empty($id)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        $model = UserBankModel::findUserBankById($id);
        if ($model && $model->is_default == UserBankModel::DEFAULT_BANKCARD) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '删除失败！该卡为用户的有效默认银行卡，不允许执行删除操作！']);
        }
        if (UserBankModel::delUserBankById($id)) {
            return Json::encode([
                'status' => self::STATUS_SUCCESS,
                'error_message' => '删除成功'
            ]);
        }
        return Json::encode([
            'status' => self::STATUS_FAILURE,
            'error_message' => "操作失败"
        ]);
    }
}