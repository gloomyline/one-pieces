<?php

namespace backend\controllers;

use Yii;
use yii\helpers\Json;
use backend\services\FileService;
use backend\bases\BackendController;

class SystemController extends BackendController
{

    const TYPE_EXAMPLE = 'example'; // 案例中心
    const TYPE_ARTICLE = 'article'; // 文章管理

    public function actionSetParams() {
        $request = Yii::$app->request;
        $type = $request->get('type', '');
        if (empty($type)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        $data = $file = '';
        if ($type == self::TYPE_EXAMPLE) {
            $exampleId = intval($request->POST('example_id',''));
            if (empty($exampleId)) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '导航名称不能为空']);
            }

            $path = dirname(dirname(__FILE__));
            $file = $path . '/../common/config/params-local.php';
            $data = ['example_id' => $exampleId];
        } elseif ($type == self::TYPE_ARTICLE) {
            $articleId = intval($request->POST('article_id',''));
            if (empty($articleId)) {
                return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '导航名称不能为空']);
            }

            $path = dirname(dirname(__FILE__));
            $file = $path . '/../common/config/params-local.php';
            $data = ['article_id' => $articleId];
        }
        if (FileService::saveParams($data, $file, true)) {
            return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
        }
        return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '保存失败']);
    }
}