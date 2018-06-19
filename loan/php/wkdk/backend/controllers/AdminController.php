<?php

namespace backend\controllers;

use Yii;
use yii\helpers\Json;
use yii\helpers\Url;
use backend\models\Admin;
use backend\models\adminModel;
use backend\bases\BackendController;

class AdminController extends BackendController 
{
    public function actionIndex()
    {
        $results = $data = [];
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $state = $request->get('state','');
        $username = $request->get('username','');
        $realName = $request->get('real_name','');
        $adminModel = Admin::find()->with('role');
        if ($state) {
            $adminModel->andWhere(['admin.state' => intval($state)]);
        }
        if ($username) {
            $adminModel->andWhere(['admin.username' => trim($username)]);
        }
        if ($realName) {
            $adminModel->andWhere(['admin.real_name' => trim($realName)]);
        }
        
        $count = $adminModel->count();
        $result = $adminModel->offset($offset)->limit($limit)->orderBy(['admin.id' => SORT_DESC])->all();

        foreach ($result as $row) {
            $data[] = [
                'id' => $row->id,
                'username' => $row->username,
                'real_name' => $row->real_name,
                'state' => $row->state,
                'role' => isset($row->role) ? $row->role->item_name : '',
                'login_time' => $row->login_time,
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
        $username = trim($request->post('username'));
        if (empty($username)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请输入用户名']);
        }
        $password = trim($request->post('password'));
        if (empty($password)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请输入密码']);
        }
        $realName = trim($request->post('real_name'));
        if (empty($realName)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请输入真实姓名']);
        }
        
        //判断用户名是否已经存在
        $checkExist = Admin::find()->where(['username' => $username])->one();
        if ($checkExist) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '用户名已存在']);
        }

        $auth = Yii::$app->authManager;
        $role = $auth->getRole($request->post('role'));
        if (!$role) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请选择角色']);
        }
        $admin = new Admin();
        $admin->username = $username;
        $admin->real_name = $realName;
        $admin->password = Yii::$app->getSecurity()->generatePasswordHash($password);
        $transaction = Yii::$app->db->beginTransaction();
        if (!$admin->save()) {
            $transaction->rollBack();
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '保存失败']);
        }
        if (!$auth->assign($role, $admin->id)) {
            $transaction->rollBack();
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '分配角色失败']);
        }
        $transaction->commit();
        return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
    }

    public function actionUpdate()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $admin = Admin::findOne(['id' => $id]);
        if (!$admin) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        $username = trim($request->post('username'));
        if (empty($username)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请输入用户名']);
        }
        $realName = trim($request->post('real_name'));
        if (empty($realName)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请输入真实姓名']);
        }

        //判断用户名是否已经存在
        $checkExist = Admin::find()->where(['username' => $username])->andWhere(['<>', 'id', $id])->one();
        if ($checkExist) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '用户名已存在']);
        }

        $auth = Yii::$app->authManager;
        $role = $auth->getRole($request->post('role'));
        if (!$role) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请选择角色']);
        }
        $admin->username = $username;
        $admin->real_name = $realName;
        $transaction = Yii::$app->db->beginTransaction();
        if (!$admin->save()) {
            $transaction->rollBack();
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '保存失败']);
        }
        $auth->revokeAll($admin->id);
        if (!$auth->assign($role, $admin->id)) {
            $transaction->rollBack();
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '分配角色失败']);
        }
        $transaction->commit();
        return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
    }

    public function actionDetail()
    {
        $request = Yii::$app->request;
        $admin = Admin::find()
                        ->select(['admin.username', 'admin.real_name', 'auth_assignment.item_name as role'])
                        ->andWhere(['id' => $request->get('id')])
                        ->leftJoin('auth_assignment','admin.id = auth_assignment.user_id')
                        ->asArray()
                        ->one();
        if (!$admin) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'error_message' => '',
            'results' => $admin
        ]);
    }

    public function actionSetLeave()
    {
        $request = Yii::$app->request;
        $admin = Admin::findOne(['id' => $request->post('admin_id')]);
        if (!$admin) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        $admin->state = adminModel::RESIGNED;
        if (!$admin->save()) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '设置失败']);
        }
        return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
    }
    
    public function actionSetPassword()
    {
        $request = Yii::$app->request;
        $adminId = (int)$request->post('id', '');
        $password = trim($request->post('password', ''));
        $model = Admin::findIdentity($adminId);
        if (empty($adminId) || empty($password)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        $model->password = Yii::$app->getSecurity()->generatePasswordHash($password);
        if (!$model->save()) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '设置失败']);
        }
        return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
    }

    public function actionBasic()
    {
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'error_message' => '',
            'results' => [['name' => Yii::$app->user->identity->username]]
        ]);
    }
}
