<?php
/**
 * Created by PhpStorm.
 * User: lzh
 * Date: 2018/3/16
 * Time: 17:18
 */

namespace backend\controllers;


use common\models\PartnerModel;
use Yii;
use yii\helpers\Json;
use backend\bases\BackendController;

class PartnerController extends BackendController
{
    // 合作伙伴列表
    public function actionIndex()
    {
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $result = $data = [];
        $result = PartnerModel::getAllPartner($offset, $limit);

        foreach ($result['result'] as $row) {
            $data[] = [
                'id' => $row->id,
                'name' => $row->name,
                'description' => $row->description,
                'sort' => $row->sort,
                'link' => $row->link,
                'image' => $row->image,
                'state' => $row->state,
                'created_at' => $row->created_at,
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $result['count'],
            'results' => $data
        ]);
    }

    public function actionAdd()
    {
        $request = Yii::$app->request;
        $name = trim($request->post('name', ''));
        $description = trim($request->post('description', ''));
        $link = trim($request->post('link', ''));
        $state = intval($request->post('state'));
        $sort = intval($request->post('sort')); //
        $image = trim($request->post('image')); // 合作伙伴图片

        // 表单检验
        if (empty($name)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '名称不能为空']);
        }
        if (empty($state) || !in_array($state, [PartnerModel::STATE_SHOW, PartnerModel::STATE_HIDDEN])) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        $data = [
            'name' => $name,
            'description' => $description,
            'link' => $link,
            'sort' => $sort,
            'state' => $state,
            'image' => $image,
        ];
        $result = PartnerModel::add($data);
        if (!$result) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '添加失败']);
        }
        return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
    }

    public function actionUpdate()
    {
        $request = Yii::$app->request;
        $id = intval($request->post('id'));
        $name = trim($request->post('name', ''));
        $description = trim($request->post('description', ''));
        $link = trim($request->post('link', ''));
        $state = intval($request->post('state'));
        $sort = intval($request->post('sort')); //
        $image = trim($request->post('image')); // 合作伙伴图片


        // 表单检验
        $partner = PartnerModel::findOneById($id);
        if (!$partner) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误,操作的记录不存在']);
        }
        // 表单检验
        if (empty($name)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '名称不能为空']);
        }
        if (empty($state) || !in_array($state, [PartnerModel::STATE_SHOW, PartnerModel::STATE_HIDDEN])) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }

        $data = [
            'name' => $name,
            'description' => $description,
            'link' => $link,
            'sort' => $sort,
            'state' => $state,
            'image' => $image,
        ];
        $result = PartnerModel::update($id, $data);
        if (!$result) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '保存成功']);
        }
        return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
    }

    public function actionDel()
    {
        $request = Yii::$app->request;
        $id = intval($request->post('partner_id', ''));

        if (empty($id)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '无效参数！请重试。']);
        }

        if (PartnerModel::delById($id)) {
            return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
        }
        return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '删除失败']);

    }

}