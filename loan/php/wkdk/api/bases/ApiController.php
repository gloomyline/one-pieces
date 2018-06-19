<?php

namespace api\bases;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Json;
use common\bases\CommonController;
use common\extend\filters\TokenAuth;
use common\models\User;

/**
 * API控制器基类
 */
class ApiController extends CommonController {
    const STATUS_SUCCESS = 'SUCCESS';
    const STATUS_FAILURE = 'FAILURE';

    /**
     * 绑定访问控制过滤器
     * 
     * @return array
     */
    public function behaviors() {
        $behaviors = parent::behaviors();
        // Token登录
        $behaviors['tokenAuth'] = [
            'class' => TokenAuth::className(),
        ];
        // 访问控制
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'denyCallback' => function ($rule, $action) {
                echo Json::encode([
                    'status' => 'TOKEN_ERROR',
                    'error_message' => '登录状态出错, 没有使用合法Token, 有问题请联系客服',
                ]);
            },
        ];
        return $behaviors;
    }

    public function beforeAction($action) {
        $this->enableCsrfValidation = false;
        $result = parent::beforeAction($action);
        $user = Yii::$app->user->identity;
        return $result;
    }

    public function getUserInfo() {
        if (Yii::$app->user->isGuest) {
            return false;
        } else {
            return User::findOne(Yii::$app->user->identity->id);
        }
    }

    public function getUserId($mustBe = false, $returnString = 0) {
        $model = $this->getUserInfo();
        if (!empty($model)) {
            return $model->id;
        } else {
            if ($mustBe) {
                return Json::encode([
                    'status' => 'FAILURE',
                    'error_message' => '获取用户信息失败',
                ]);
            } else {
                return $returnString;
            }
        }
    }
}
