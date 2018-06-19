<?php
namespace common\models;

use common\models\Feedback;
use common\bases\CommonModel;
use Yii;

class FeedbackModel extends CommonModel {
    const STATE_NOT_PROCESSED = 1; // 未处理
    const STATE_HAS_PROCESSED = 2; // 已处理

    /**
     * @param array $data
     * @return bool
     */
    public static function add($data) {
        $model = new Feedback();
        $model->setAttributes($data);
        $result = $model->save();
        if ($result) {
            return $model->id;
        }
        return false;
    }

    /**
     * 获取意见反馈记录
     * @param integer $offset 查询基准数
     * @param integer $limit 查询记录数
     * @param string $mobile 用户名-手机号码
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getFeedbackList($offset, $limit, $mobile)
    {
        $feedback = Feedback::find()
            ->select('* , feedback.id as id')
            ->joinwith('user');
        if ($mobile != '') {
            $feedback->andWhere(['user.mobile' => $mobile]);
        }
        return [
            'count' => $feedback->count(),
            'result' =>$feedback->orderBy(['feedback.id' => SORT_DESC])->offset($offset)->limit($limit)->all()
        ];
    }

    /**
     * 根据id删除反馈记录
     * @param integer $id
     * @return int 返回删除的结果条数
     */
    public static function delFeedbackById($id)
    {
        return Feedback::deleteAll(['id' => $id]);
    }

}