<?php
namespace frontend\bases;

use common\bases\CommonController;
use common\extend\filters\TokenAuth;
use yii\filters\AccessControl;
use common\models\User;
use yii\helpers\Json;
use Yii;

class FrontendController extends CommonController {
    const STATUS_SUCCESS = 'SUCCESS';
    const STATUS_FAILURE = 'FAILURE';
    const STATUS_NOLOGIN = 'NOLOGIN';

    const APPLAY_AUDITING = 'AUDITING'; // 审核中
    
    /**
     * 绑定访问控制过滤器
     * 
     * @return array
     */
    public function behaviors()
    {
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

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        $result = parent::beforeAction($action);
        return $result;
    }
    
    /**
     * 从错误信息数组中取得错误信息,并组成字符串
     *
     * @param array $errors 是个多维数组
     * @param string $delimiter 字符串的分隔符, 默认为空字符串
     * @return string 错误信息
     */
    public function getErrorMessageFromArray($errors, $delimiter = '') {
        $errorMessage = '';
        if (is_array($errors)) {
            foreach ($errors as $error) {
                $errorMessage .= $this->getErrorMessageFromArray($error, $delimiter);
            }
        } else if (is_string($errors)) {
            $errorMessage .= $errors;
        }
        return $errorMessage;
    }

}
