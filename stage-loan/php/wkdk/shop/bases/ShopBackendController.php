<?php
/**
 * 后台控制器基类
 */
namespace shop\bases;

use Yii;
use yii\helpers\Json;
use common\bases\CommonController;

class ShopBackendController extends CommonController {
    const STATUS_SUCCESS = 'SUCCESS';
    const STATUS_FAILURE = 'FAILURE';
    const STATUS_NOLOGIN = 'NOLOGIN';

    public $logRemarks = '';
    /**
     * 判断是否有登录
     */
    public function beforeAction($action)
    {
        parent::beforeAction($action);
        if (Yii::$app->user->isGuest && $this->action->id != 'login' && $this->action->id != 'captcha') {
            if (Yii::$app->request->isAjax) {
                echo Json::encode([
                    'status' => self::STATUS_NOLOGIN,
                    'error_message' => '登录超时，请重新登陆'
                ]);
            } else {
                $this->redirect(['site/login']);
            }
            return false;
        } else {
            //判断是否有权限
            $action = Yii::$app->controller->route;
            if ($action && Yii::$app->params['is_open_rbac']) {
                $action = '/'.$action;
                $permission = Yii::$app->authManager->getPermission($action);

                if ($action == '/site/login' || $action == '/site/captcha'
                    || ($permission
                        && ((empty($permission->ruleName) && in_array($action, Yii::$app->user->identity->getRoutes()))
                            || ($permission->ruleName && Yii::$app->user->can($action))))) {
                    return true;
                } else {
                    $notice = '此操作';        
                    if (Yii::$app->request->isAjax) {
                        echo Json::encode([
                            'status' => self::STATUS_FAILURE,
                            'error_message' => '对不起，您没有' . $notice . '的权限'
                        ]);
                    } else {
                        //$this->redirect(['site/errors', 'msg' => '对不起，您没有' . $notice . '的权限']);
                        echo Json::encode([
                            'status' => self::STATUS_FAILURE,
                            'error_message' => '对不起，您没有' . $notice . '的权限'
                        ]);
                    }
                    return false;
                }
            } else {
                return true;
            }
        }

    }
    public function afterAction($action, $result)
    {
        //$this->userLog();
        return parent::afterAction($action, $result);
    }

    /**
     * @param string $msg
     * @return string
     */
    protected function err($msg)
    {
        return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => $msg]);
    }

    /**
     * @param array $result
     * @return string
     */
    protected function success($result = [])
    {
        return Json::encode(array_merge(['status' => self::STATUS_SUCCESS, 'error_message' => ''], $result));
    }
}