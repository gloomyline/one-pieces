<?php
namespace console\controllers;

use common\models\ShopModel;
use Yii;
use yii\console\Controller;

class ShopController extends Controller
{
    /**
     * 更新商户当日可用额度
     */
    public function actionSetShopAvailableQuota()
    {
        // 更新已审核通过商户当日可用额度
        $shopCount = Yii::$app->db->createCommand('update shop set daily_available_quota = single_limit_quota where state = ' . ShopModel::STATE_AUDIT_PASS)->execute();
        Yii::info(sprintf('Tips: %d shops\'s `daily_available_quota` were updated successfully.', $shopCount), 'shop');
    }
}