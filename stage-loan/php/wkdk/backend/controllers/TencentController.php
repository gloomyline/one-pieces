<?php
namespace backend\controllers;

use common\models\AntiFraudModel;
use Yii;
use yii\helpers\Json;
use common\services\TencentService;
use common\models\UserModel;
use common\models\UserIdentityCardModel;
use backend\bases\BackendController;

class TencentController extends BackendController
{
    /**
     * 创建用户反欺诈信息
     * @param user_id 用户ID
     * @return string
     */
    public function actionCreateAntiFraud()
    {
        $request = Yii::$app->request;
        $userId = $request->post('user_id');
        if (empty($userId)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '用户ID不能为空']);
        }

        $user = UserModel::findUserById($userId);
        if (!$user) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '用户不存在']);
        }

        $identity = UserIdentityCardModel::getIdentityCard($user->id);
        if (!$identity || $identity->state != UserIdentityCardModel::STATE_PASS) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '该用户尚未实名认证']);
        }

        $region = $request->post('region', 'gz');
        $idno = $request->post('idno', '');
        $mobile = $request->post('mobile', '');
        $params = ['idNumber' => $identity->identity_no, 'phoneNumber' => $user->mobile];

        $url = TencentService::makeURL('GET', 'AntiFraud', $region, TencentService::SECRET_ID, TencentService::SECRET_KEY, $params);
        $result = TencentService::sendRequest($url);

        if ($result && $result['code'] == 0) {
            $resultStr = json_encode($result);
            $data = ['user_id' => $userId, 'content' => $resultStr];

            $antiFraud = AntiFraudModel::getByUserId($userId);
            if (!$antiFraud) {
                AntiFraudModel::add($data);
            } else {
                AntiFraudModel::updateById($antiFraud->id, $data);
            }
            return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => $result]);
        }
        return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '获取数据失败code:' . $result['code']]);
    }

    /**
     * 更新用户反欺诈信息
     * @param user_id 用户ID
     * @return string
     */
    public function actionUpdateAntiFraud()
    {
        $request = Yii::$app->request;
        $userId = $request->post('user_id');
        if (empty($userId)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '用户ID不能为空']);
        }

        $user = UserModel::findUserById($userId);
        if (!$user) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '用户不存在']);
        }

        $identity = UserIdentityCardModel::getIdentityCard($user->id);
        if (!$identity || $identity->state != UserIdentityCardModel::STATE_PASS) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '该用户尚未实名认证']);
        }

        $region = $request->post('region', 'gz');
        $idno = $request->post('idno', '');
        $mobile = $request->post('mobile', '');
        $params = ['idNumber' => $identity->identity_no, 'phoneNumber' => $user->mobile];

        $url = TencentService::makeURL('GET', 'AntiFraud', $region, TencentService::SECRET_ID, TencentService::SECRET_KEY, $params);
        $result = TencentService::sendRequest($url);

        if ($result && $result['code'] == 0) {
            $resultStr = json_encode($result);
            $data = ['content' => $resultStr];
            $userAntiFraud = AntiFraudModel::getByUserId($userId);
            if (!$userAntiFraud) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '无用户反欺诈信息']);
            }
            AntiFraudModel::updateById($userAntiFraud->id, $data);

            return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '更新成功']);
        }
        return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '更新数据失败code:' . $result['code']]);
    }
}