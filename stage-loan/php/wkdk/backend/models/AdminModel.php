<?php
namespace backend\models;

use common\bases\CommonModel;
use Yii;

class AdminModel extends CommonModel
{
    const SIGNED = 1; // 在职
    const RESIGNED = 2; // 离职
    
    public static $stateArr = [
        self::SIGNED => '在职',
        self::RESIGNED => '离职'
    ];

    /**
     * 获取所有的管理员
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getAdmins()
    {
        return Admin::find()->with('role')->where(['state' => self::SIGNED])->all();
    }


    /**
     * 催收员列表
     * @param integer $offset
     * @param integer $limit
     * @param string $userName 登陆名
     * @param string $realName 真实姓名
     * @param array $orderBy 排序
     * @return array 返回数组【记录条数，列表数据对象】
     */
    public static function getUrgeStatics($offset, $limit, $userName, $realName ,$orderBy = ['admin.id' => SORT_DESC])
    {
        $model = Admin::find()
            ->joinWith('role');
        if ($userName != '') {
            $model->andWhere(['admin.username' => $userName]);
        }
        if ($realName != '') {
            $model->andWhere(['admin.real_name' => $realName]);
        }
        return [
            'count' => $model->count(),
            'result' => $model->offset($offset)->limit($limit)->orderBy($orderBy)->all() // 查询的结果
        ];
    }

}

