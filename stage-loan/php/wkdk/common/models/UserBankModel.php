<?php
namespace common\models;

use common\bases\CommonModel;

class UserBankModel extends CommonModel
{
    const DEFAULT_BANKCARD = 1; // 默认银行卡
    const NOT_DEFAULT_BANKCARD = 0; // 非默认银行卡

    const STATE_VALID = 'valid'; // 有效状态
    const STATE_INVALID = 'invalid'; // 无效状态

    /**
     * 获取用户所有银行卡
     * @param integer $userId 用户ID
     * @return array 返回查询的结果
     */
    public static function getBankByUserId($userId)
    {
        $model = UserBank::find();
        return $model->where(['user_id' => $userId])->andWhere(['state' => self::STATE_VALID])->andWhere(['<>', 'agreeno', ''])->all();
    }

    /**
     * 根据用户ID和卡号获取用户银行卡
     * @param string $userId 用户ID
     * @param string $bankNo 银行卡号
     * @return array 返回查询的结果
     */
    public static function getBankByUserIdAndNo($userId, $bankNo)
    {
        $model = UserBank::find();

        $condition = ['user_id' => $userId, 'bank_no' => $bankNo, 'state' => UserBankModel::STATE_VALID];
        $model->where($condition);
        return $model->one();
    }

    /**
     * 添加
     * @param array $data [`user_id`, `bank_name`, `bank_no`, `bank_user`]
     * @return array|integer 返回ID值或false
     */
    public static function add($data)
    {
        $model = new UserBank();
        $model->setAttributes($data);
        $res =  $model->save();
        if ($res) {
            return $model->id;
        } else {
            return false;
        }
    }

    /**
     * 更新
     * @param integer $userId 用户id
     * @param string $cardNo 银行卡号
     * @param array $data 更新的字段信息
     * @return boolean 是否更新成功 true-是 false-否
     */
    public static function update($userId, $cardNo, $data)
    {
        $model = UserBank::find()
                        ->where(['user_id' => $userId])
                        ->andWhere(['LIKE', 'bank_no', '%' . $cardNo, false])
                        ->one();
        if (empty($model)) {
            return false;
        }

        $model->setAttributes($data);
        $model->updated_at = date('Y-m-d H:i:s');
        $res = $model->save();
        if ($res) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 删除
     * @param integer $id ID
     * @aram integer $userId 用户ID
     * @return boolean 是否删除成功 true-是 false-否
     */
    public static function delete($id, $userId)
    {
        $model = UserBank::findOne(['id' => $id, 'user_id' => $userId]);
        if (empty($model)) {
            return true;
        }
        $res = $model->delete();
        if ($res) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 设置默认银行卡
     * @param integer $userBankId 用户银行卡ID
     * @param integer $userId 用户ID
     * @return bool 设置成功
     */
    public static function setBankcardDefault($userBankId, $userId)
    {
        UserBank::updateAll(['is_default' => self::DEFAULT_BANKCARD], 'id = :id', [':id' => $userBankId]); // 设置默认卡
        UserBank::updateAll(['is_default' => self::NOT_DEFAULT_BANKCARD], 'id <> :id and user_id = :user_id', [':id' => $userBankId, ':user_id' => $userId]); // 设置其他卡 的默认状态为 非默认状态
        return true;
    }

    /**
     * 获取用户绑定的默认银行卡
     * @param int $userId 用户ID
     * @return array 返回查询的结果
     */
    public static function getUserDefaultBankCard($userId)
    {
        $model = UserBank::find();
        return $model->where(['user_id' => $userId])->andWhere(['is_default' => self::DEFAULT_BANKCARD])->andWhere(['<>', 'agreeno', ''])->one();
    }

    /**
     * 获取银行卡认证列表
     * @param integer $offset 查询的基准数
     * @param integer $limit 查询的数目
     * @param string $realName 真实姓名
     * @param string $userName 用户名-手机号
     * @param string $bankNo 银行卡号
     * @param string $state 银行卡认证状态
     * @param array $orderBy 排序
     * @return array 返回数据库总条数，满足条件的数据
     */
    public static function getBankAuthList($offset, $limit, $realName, $userName, $bankNo, $state ,$orderBy = ['user_bank.id' => SORT_DESC])
    {
        $data = [];
        $user = UserBank::find()
            ->select('*,user_bank.id as id ,user_bank.user_id as user_id, user_bank.created_at as created_at , user_bank.state as state')
            ->joinWith('user')
            ->joinWith('userIdentityCard')
            ->where(['user.is_identity_auth' => UserModel::HAS_IDENTITY_AUTH]);

        if ($realName) {
            $user->andWhere(['user_identity_card.real_name' => trim($realName)]); // 真实姓名
        }
        if ($userName) {
            $user->andWhere(['user.mobile' => trim($userName)]); // 手机号-用户名
        }
        if ($bankNo) {
            $user->andWhere(['like', 'user_bank.bank_no', trim($bankNo)]); // 银行卡号
        }
        if ($state != '') {
            $user->andWhere(['user_bank.state' => trim($state)]); // 银行卡认证状态
        }
        $data['count'] = $user->count();
        $data['result'] = $user->offset($offset)->limit($limit)->orderBy($orderBy)->all();
        return $data;
    }

    /**
     * 删除银行卡认证
     * @param $id 银行卡认证id
     * @return int 返回成功删除记录数
     */
    public static function delUserBankById($id)
    {
        return UserBank::deleteAll(['id' => $id]);
    }

    public static function findUserBankById($id)
    {
        return UserBank::findOne(['id' => $id]);
    }

    /**
     * 根据银通签约的协议编号 以及用户ID查找用户银行卡信息
     * @param integer $userId 用户ID
     * @param string $noAgree 银通签约的协议编号
     * @return array|null|\yii\db\ActiveRecord 返回查询的结果
     */
    public static function findUserBankByNoAgree($userId, $noAgree)
    {
        return UserBank::find()->where(['user_id' => $userId, 'agreeno' => $noAgree, 'state' => UserBankModel::STATE_VALID])->one();
    }
}
