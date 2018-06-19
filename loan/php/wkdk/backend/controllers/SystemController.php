<?php

namespace backend\controllers;

use common\config\RiskConfig;
use common\models\RiskRuleModel;
use Yii;
use yii\helpers\Json;
use backend\services\FileService;
use backend\bases\BackendController;

class SystemController extends BackendController
{
    /**
     * 风控字段记录列表
     * @return string
     */
    public function actionRisk()
    {
        $data = [];
        $request = Yii::$app->request;
        $state = $request->get('state', '');
        $title = $request->get('title', '');
        $list = RiskConfig::$risks;
        // 无筛选
        if ($state == '' && $title =='') {
            foreach ($list as $lt) {
                $data[] = [
                    'title' => $lt['title'], // 表名
                    'description' => $lt['description'], // 注释
                    'enable' => (int)$lt['enable'], // 启用禁用
                    'items' => $lt['items'], // 内容
                ];
            }
        } elseif($state!= '' && $title == '') {
            foreach ($list as $lt) {
                if ($lt['enable'] == $state) {
                    $data[] = [
                        'title' => $lt['title'], // 表名
                        'description' => $lt['description'], // 注释
                        'enable' => (int)$lt['enable'], // 启用禁用
                        'items' => $lt['items'], // 内容
                    ];
                }
            }
        } elseif ($state == '' && $title !='') {
            foreach ($list as $lt) {
                if ($lt['title'] == $title) {
                    $data[] = [
                        'title' => $lt['title'], // 表名
                        'description' => $lt['description'], // 注释
                        'enable' => (int)$lt['enable'], // 启用禁用
                        'items' => $lt['items'], // 内容
                    ];
                }
            }
        } elseif ($state != '' && $title !='') {
            foreach ($list as $lt) {
                if ($lt['title'] == $title && $lt['enable'] == $state) {
                    $data[] = [
                        'title' => $lt['title'], // 表名
                        'description' => $lt['description'], // 注释
                        'enable' => (int)$lt['enable'], // 启用禁用
                        'items' => $lt['items'], // 内容
                    ];
                }
            }
        }
        $count = count($data);
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $count,
            'results' => $data
        ]);
    }

    /**
     * 信用分规则配置列表
     * @return string
     */
    public function actionRule()
    {
        $results = $data = [];
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $name = $request->get('name', '');
        $state = $request->get('state', '');
        $results = RiskRuleModel::getRiskRuleList($offset, $limit, $name, $state, ['module' => SORT_ASC]);
        foreach ($results['result'] as $row) {
            $data[] = [
                'id' => $row->id,
                'item' => $row->item, // 表字段英文名
                'name' => $row->name, // 字段中文
                'module' => $row->module, // 表注释
                'pattern' => $row->pattern, // 模式
                'operator' => $row->operator, // 操作符
                'val' => $row->val, // 值
                'outcome' => $row->outcome, // 结果
                'symbol' => $row->symbol,
                'score' => $row->score, // 分值
                'state' => $row->state, // 状态
                'remarks' => $row->remarks, // 备注
            ];
        }
        // 当前已设规则总分 = 数据库统计的总分 - 数据库统计减分总和*2
        $totalScore = RiskRuleModel::getScoreStatistics();
        $totalDecreaseScore = RiskRuleModel::getScoreStatistics(['symbol' => RiskRuleModel::SYMBOL_DECREASE]);
        // 当前有效规则总分 = 数据库统计的有效总分 - 数据库有效减分项总和*2
        $totalValidScore = RiskRuleModel::getScoreStatistics(['state' => RiskRuleModel::STATE_ENABLE]);
        $totalValidDecreaseScore = RiskRuleModel::getScoreStatistics(['state' => RiskRuleModel::STATE_ENABLE,'symbol' => RiskRuleModel::SYMBOL_DECREASE]);

        $riskModule = RiskConfig::$risks; // 获取风控记录信息
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $results['count'],
            'results' => $data,
            'risk' => $riskModule,
            'totalScore' => $totalScore - $totalDecreaseScore * 2,
            'totalValidScore' => $totalValidScore - $totalValidDecreaseScore * 2,
        ]);
    }

    /**
     * 添加信用分规则记录
     * @return string
     */
    public function actionAddRiskRule()
    {
        $request = Yii::$app->request;
        $module = $request->post('module', '');
        if (empty($module)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请选择所属表注释！']);
        }
        $pattern = $request->post('pattern', '');
        if (empty($pattern)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '模式选项不能为空！']);
        }
        $itemName = $request->post('itemName', '');
        if (empty($itemName)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '表字段不能为空！']);
        }
        // 传入的表字段名为 : 英文，中文  需分割
        $arr = explode(',', $itemName);
        if (count($arr) !==2 ) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '表字段参数错误！']);
        }
        $item = $arr[0];
        $name = $arr[1];
        $operator = $request->post('operator', ''); //认证状态--操作符
        if (empty($operator)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '认证状态不能为空！']);
        }
        $val = $request->post('val', ''); // 值----可为空
        $isMatched = preg_match('/^([\x{4e00}-\x{9fa5}]*)|(\d*),?([\x{4e00}-\x{9fa5}]*)|(\d*)$/u', $val);
        if (!$isMatched) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '值选项只能输入中文，数字以及用逗号隔开的区间！']);
        }
        $outcome = $request->post('outcome', ''); // 结果
        if (empty($outcome)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '结果不能为空！']);
        }
        $symbol = $request->post('symbol', '');
        if (empty($symbol)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '分数增加减少不能为空！']);
        }
        $score = $request->post('score', ''); // 分数
        if ($score == '') {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '分数不能为空且为大于零的数！']);
        }
        $state = $request->post('state', '');
        if ($state == '') {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请填写规则状态']);
        }
        $remarks = $request->post('remarks', '');
        // 检测是否重复添加
        $result = RiskRuleModel::findByCondition(['item' => $item, 'name' => $name, 'module' => $module, 'pattern' => $pattern, 'operator' => $operator, 'val' => $val]);
        if ($result) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '您已添加过相同类型的规则，请检测后再继续操作']);
        }
        $data = [];
        $data = [
            'item' => $item,
            'name' => $name,
            'module' => $module,
            'pattern' => $pattern,
            'operator' => $operator,
            'val' => trim($val),
            'outcome' => $outcome,
            'symbol' => $symbol,
            'score' => intval($score),
            'state' => intval($state),
            'remarks' => $remarks,
            'created_at' => date('Y-m-d H:i:s', time()),
            'updated_at' => date('Y-m-d H:i:s', time()),
            'admin_id' => Yii::$app->user->id,
        ];
        $result = RiskRuleModel::add($data);
        if ($result) {
            return Json::encode([
                'status' => self::STATUS_SUCCESS,
                'error_message' => '添加成功'
            ]);
        }

        return Json::encode([
            'status' => self::STATUS_FAILURE,
            'error_message' => "添加失败"
        ]);
    }

    /**
     * 编辑信用分规格
     * @return string
     *
     */
    public function actionUpdateRiskRule()
    {
        $request = Yii::$app->request;
        $id = $request->post('id', '');
        if (empty($id)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        $riskRuleModel = RiskRuleModel::findByCondition(['id' => intval($id)]);
        if (!$riskRuleModel) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '您所操作的记录不存在，请重新操作！']);
        }
        $module = $request->post('module', '');
        if (empty($module)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请选择所属表注释！']);
        }
        $pattern = $request->post('pattern', '');
        if (empty($pattern)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '模式选项不能为空！']);
        }
        $itemName = $request->post('itemName', '');
        if (empty($itemName)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '表字段不能为空！']);
        }
        $arr = explode(',', $itemName);
        if (count($arr) !==2 ) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '表字段参数错误！']);
        }
        $item = $arr[0];
        $name = $arr[1];
        $operator = $request->post('operator', ''); //认证状态--操作符
        if (empty($operator)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '认证状态不能为空！']);
        }
        $val = $request->post('val', ''); // 值----可为空-匹配中文数字，逗号
        $isMatched = preg_match('/^([\x{4e00}-\x{9fa5}]*)|(\d*),?([\x{4e00}-\x{9fa5}]*)|(\d*)$/u', $val);
        if (!$isMatched) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '值选项只能输入中文，数字以及用逗号隔开的区间！']);
        }
        $outcome = $request->post('outcome', ''); // 结果
        if (empty($outcome)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '结果不能为空！']);
        }
        $symbol = $request->post('symbol', '');
        if (empty($symbol)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '分数增加减少不能为空！']);
        }
        $score = $request->post('score', ''); // 分数
        if ($score == '') {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '分数不能为空且为大于零的数！']);
        }

        $state = $request->post('state', '');
        if ($state == '') {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请填写规则状态']);
        }
        $remarks = $request->post('remarks', '');
        // 检测是否存在相同记录
        $result = RiskRuleModel::findByCondition([
            'item' => $item,
            'name' => $name,
            'module' => $module,
            'pattern' => $pattern,
            'operator' => $operator,
            'val' => $val,
            'outcome' => $outcome,
            'symbol' => $symbol,
            'score' => intval($score),
            'state' => intval($state),
            'remarks' => $remarks,]);
        if ($result) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '您已存在过相同类型的规则，或没有对规则进行有效修改，请检测后再继续操作']);
        }
        $data = [];
        $data = [
            'item' => $item,
            'name' => $name,
            'module' => $module,
            'pattern' => $pattern,
            'operator' => $operator,
            'val' => trim($val),
            'outcome' => $outcome,
            'symbol' => $symbol,
            'score' => intval($score),
            'state' => intval($state),
            'remarks' => $remarks,
            'updated_at' => date('Y-m-d H:i:s', time()),
            'admin_id' => Yii::$app->user->id, // 管理员id
        ];
        $riskRuleModel->setAttributes($data);
        if ($riskRuleModel->save()) {
            return Json::encode([
                'status' => self::STATUS_SUCCESS,
                'error_message' => '编辑成功'
            ]);
        }

        return Json::encode([
            'status' => self::STATUS_FAILURE,
            'error_message' => "添加失败"
        ]);
    }

    /**
     * 信用分的启用禁用操作
     * @return string
     */
    public function actionUpdateState()
    {
        $request = Yii::$app->request;
        $id = $request->get('id', '');
        if (empty($id)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        $model = RiskRuleModel::findByCondition(['id' => $id]);
        if(!$model) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '您所操作的记录不存在，请重新操作']);
        }
        if ($model->state === RiskRuleModel::STATE_ENABLE) {
            $model->state = RiskRuleModel::STATE_DISABLE;
        } elseif ($model->state === RiskRuleModel::STATE_DISABLE) {
            $model->state = RiskRuleModel::STATE_ENABLE;
        }
        if ($model->save()) {
            return Json::encode([
                'status' => self::STATUS_SUCCESS,
                'error_message' => '操作成功'
            ]);
        }
        return Json::encode([
            'status' => self::STATUS_FAILURE,
            'error_message' => "操作失败"
        ]);
    }

    /**
     * 删除信用分规则
     * @return string
     */
    public function actionDelRiskRule()
    {
        $request = Yii::$app->request;
        $id = $request->get('id', '');
        if (empty($id)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        if (RiskRuleModel::delRiskRuleById($id)) {
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
     * 信用分等级以及自动审核放款功能设置列表
     * @return string
     */
    public function actionCreditSet()
    {

        $data = [
            'system_auto_review_switch' => Yii::$app->params['system_auto_review_switch'], // 系统是否自动审核
            'auto_loan_need_score' => Yii::$app->params['auto_loan_need_score'],   //自动放款所需分数大于等于几
            'grade' => Yii::$app->params['grade'],
        ];
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'error_message' => '',
            'results' => $data
        ]);

    }

    /**
     * 修改等级分数
     * @return string
     *
     */
    public function actionCreditGradeUpdate()
    {
        $request = Yii::$app->request;
        $id = $request->post('id', '');
        $name = $request->post('name', '');
        $start = $request->post('start', '');
        $end = $request->post('end', '');
        if ($id == '') {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        if ($name == '' ) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '信用等级不能为空']);
        }
        if ($start == '') {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '起始分值不能为空']);
        }
        if ($end == '') {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '结束分值不能为空']);
        }
        $grade = Yii::$app->params['grade'];

        $pre = $id - 2;
        $cur = $id - 1;
        $next = $id;
        if (!isset($grade[$cur])) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        // 前一个等级的结束分值 与 当前起始分值做对比
        if (isset($grade[$pre]) && ($grade[$pre]['end'] >= $start)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '当前输入的起始分值应大于'.$grade[$pre]['end']]);
        }

        // 当前等级的结束分值 与 后一个等级的起始分值做对比
        if (isset($grade[$next]) && ($grade[$next]['start'] <= $end)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '当前输入的结束分值应小于'.$grade[$next]['start']]);
        }

        $grade[$cur]['name'] = $name;
        $grade[$cur]['start'] = $start;
        $grade[$cur]['end'] = $end;
        $path = dirname(dirname(__FILE__));
        $file = $path . '/../common/config/CreditConfig.php';
        $data = ['grade' => $grade];

        if (FileService::saveParamsArr($data, $file, true)) {
            return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
        }
        return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '保存失败']);
    }

    /**
     * 修改是否开启系统自动审核功能，以及自动放款最大值System automatically review
     * @return string
     */
    public function actionModifySysAutoReview()
    {
        $request = Yii::$app->request;

        $systemAutoReviewSwitch = $request->post('system_auto_review_switch', '');
        $autoLoanNeedScore = $request->post('auto_loan_need_score', '');

        if (!in_array($systemAutoReviewSwitch, ['on', 'off'])) { // 系统自动审核开启 on 关闭off
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        if ($autoLoanNeedScore == '') {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '自动放款所需的最大分值不能为空！']);
        }

        $path = dirname(dirname(__FILE__));
        $file = $path . '/../common/config/CreditConfig.php';
        $data = ['system_auto_review_switch' => $systemAutoReviewSwitch, 'auto_loan_need_score' => $autoLoanNeedScore];

        if (FileService::saveParamsArr($data, $file, true)) {
            return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
        }
        return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '保存失败']);
    }
}