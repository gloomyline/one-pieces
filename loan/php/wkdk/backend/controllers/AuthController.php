<?php

namespace backend\controllers;

use Yii;
use yii\helpers\Json;
use yii\helpers\Url;
use backend\models\AuthItem;
use backend\models\AuthItemChild;
use backend\models\AuthItemModel;
use backend\bases\BackendController;

class AuthController extends BackendController
{
    public function actionIndex()
    {
        $results = $data = [];
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $name = $request->get('name','');
        $authItemModel = AuthItem::find()->where(['type' => AuthItemModel::TYPE_PERMISSION]);

        if ($name) {
            $authItemModel->andWhere(['LIKE', 'name' , trim($name)]);
        }

        $count = $authItemModel->count();
        $result = $authItemModel->offset($offset)->limit($limit)->all();

        foreach ($result as $row) {
            $data[] = [
                'name' => $row->name,
                'description' => $row->description,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ];
        }

        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $count,
            'results' => $data
        ]);
    }

    public function actionAdd()
    {
        $request = Yii::$app->request;
        $name = $request->post('name','');
        $description = $request->post('description','');
        if (empty($name)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请输入权限名称']);
        }
        if (empty($description)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请输入描述']);
        }

        //判断权限是否已经存在
        $checkExist = AuthItem::find()->where(['name' => $name])->one();
        if ($checkExist) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '权限名称已存在']);
        }

        $authItem = new AuthItem();
        $authItem->name = trim($name);
        $authItem->description = trim($description);
        $authItem->type = authItemModel::TYPE_PERMISSION;
        $authItem->created_at = time();
        $authItem->updated_at = time();
        if (!$authItem->save()) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '权限修改失败']);
        }
        return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
    }

    public function actionUpdate()
    {
        $request = Yii::$app->request;
        $oldName = $request->post('old_name','');
        $name = $request->post('name','');
        $description = $request->post('description','');
        if (empty($name)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请输入权限名称']);
        }
        if (empty($description)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请输入描述']);
        }

        //判断权限是否已经存在
        $checkExist = AuthItem::find()->where(['name' => $name])->andWhere(['=', 'description', $description])->one();
        if ($checkExist) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '权限名称已存在']);
        }

        $authItem = AuthItem::find()->where(['name' => trim($oldName)])->one();
        $authItem->name = trim($name);
        $authItem->description = trim($description);
        $authItem->updated_at = time();
        if (!$authItem->save()) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '权限修改失败']);
        }
        return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
    }

    public function actionDelete()
    {
        $request = Yii::$app->request;
        $name = $request->post('itemname','');
        $authItem = AuthItem::find()->where(['name' => trim($name)])->one();
        if ($authItem->delete()) {
            return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
        }
        return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '删除失败']);
    }

    public function actionDetail()
    {
        $request = Yii::$app->request;
        $name = $request->get('name','');
        $auth = AuthItem::find()->where(['name' => trim($name)])->asArray()->one();
        if (!$auth) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'error_message' => '',
            'results' => $auth
        ]);
    }

    public function actionRoleAuths()
    {
        $request = Yii::$app->request;
        $name = $request->get('name','');

        $results = $data = $roleAuth = [];
        $authItemChildModel = AuthItemChild::find()->where(['parent' => trim($name)]);
        $result = $authItemChildModel->all();
        foreach ($result as $row) {
            $roleAuth[] = $row->child;
        }

        $authItemModel = AuthItem::find()->where(['type' => authItemModel::TYPE_PERMISSION]);
        $result = $authItemModel->all();
        foreach ($result as $row) {
            $item = [
                'name' => $row->name,
                'description' => $row->description,
                'isChecked' => false,
            ];
            if (in_array($row->name, $roleAuth)) {
                $item['isChecked'] = true;
            }
            $data[] = $item;
        }

        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'results' => $data
        ]);
    }

    public function actionAssign()
    {
        $request = Yii::$app->request;
        $parent = $request->post('parent', '');
        $child = $request->post('child', '');
        $enable = (int)$request->post('enable', '');
        if ($enable == 2) {
            $model = AuthItemChild::find()->where(['parent' => trim($parent), 'child' => trim($child)])->one();
            if ($model && $model->delete()) {
                return Json::encode(['status' => self::STATUS_SUCCESS,'error_message' => '取消成功']);
            }
        } elseif ($enable == 1) {
            $model = new AuthItemChild();
            $model->parent = $parent;
            $model->child = $child;
            if ($model->save()) {
                return Json::encode(['status' => self::STATUS_SUCCESS,'error_message' => '添加成功']);
            }
        }

        return Json::encode(['status' => self::STATUS_FAILURE,'error_message' => '设置失败']);
    }
}
