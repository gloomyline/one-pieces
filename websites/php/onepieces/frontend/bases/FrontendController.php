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
