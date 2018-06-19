<?php

namespace common\models;

use common\bases\CommonModel;

class UserIdentityCardModel extends CommonModel
{
    const STATE_PASS = 'pass';
    const STATE_NOPASS = 'nopass';
    /**
     * 添加用户身份证
     * @param int $userId 用户ID
     * @param string $realName 真实姓名
     * @param string $identityNo 身份证号
     * @param string $state 状态
     * @return boolean
     */
    public static function addIdentityCard($userId, $realName, $identityNo, $state)
    {
        $model = new UserIdentityCard();
        $model->user_id = $userId;
        $model->real_name = $realName;
        $model->identity_no = $identityNo;
        $model->state = $state;

        return $model->save();
    }

    /**
     * 修改用户身份证
     * @param int $userId 用户ID
     * @param string $realName 真实姓名
     * @param string $identityNo 身份证号
     * @param string $state 状态
     * @return boolean
     */
    public static function updateIdentityCard($userId, $realName, $identityNo, $state)
    {
        $model = UserIdentityCard::findOne(['user_id' => $userId]);
        if (empty($model)) {
            return false;
        }
        $model->real_name = $realName;
        $model->identity_no = $identityNo;
        $model->state = $state;
        $model->updated_at = date('Y-m-d H:i:s');

        return $model->save();
    }

    /**
     * 根据用户ID查询身份证信息
     * @param integer $userId 用户ID
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function getIdentityCard($userId)
    {
        $model = UserIdentityCard::find();
        $model->Where(['user_id' => $userId]);
        return $model->one();
    }
}
