<?php
namespace backend\controllers;

use common\models\Article;
use common\models\ArticleModel;
use backend\models\Uploader;
use backend\bases\BackendController;
use common\models\FeedbackModel;
use common\models\MobileLogModel;
use yii\helpers\Json;
use Yii;
class ContentController extends BackendController
{

    /**
     * 获取意见反馈列表
     * @return string
     */
    public function actionFeedback()
    {
        $results = $data = [];
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $mobile = $request->get('mobile', '');
        $results = FeedbackModel::getFeedbackList($offset, $limit, $mobile);
        foreach ($results['result'] as $row) {
            $data[] = [
                'id' => $row->id,
                'mobile' => $row->user->mobile,
                'type' => $row->type,
                'content' => $row->content ?? '',
                'created_at' => $row->created_at,
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $results['count'],
            'results' => $data
        ]);
    }

    /**删除反馈记录
     * @return string
     */
    public function actionDelFeedback()
    {
        $request = Yii::$app->request;
        $id = $request->post('feedbackId', '');
        $result = FeedbackModel::delFeedbackById($id);
        if (!$result) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
    }

    /**
     * 获取文章列表
     * @return string
     */
    public function actionArticle()
    {
        $results = $data = [];
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $title = $request->get('title', '');
        $type = $request->get('type', '');
        $results = ArticleModel::getArticleList($offset, $limit, $title, $type);
        $count = count($results);
        foreach ($results['result'] as $row) {
            $data[] = [
              'id' => $row->id,
              'title' => $row->title,
              'type' => $row->type,
              'image' => $row->image,
              'state' => $row->state,
              'content' => $row->content,
              'created_at' => $row->created_at,
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $results['count'],
            'results' => $data
        ]);
    }

    /**
     * 添加文章
     * @return string
     */
    public function actionArticleAdd() // 添加文章
    {

        $request = Yii::$app->request;
        $title = trim($request->post('title'));
        if (empty($title)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请输入文章标题']);
        }
        $type = trim($request->post('type'));
        if (empty($type)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请选择文章类型']);
        }
        $image = trim($request->post('image', ""));
        if (empty($image)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请上传文章图片']);
        }
        $state = intval($request->post('state'));
        if (empty($state)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请上选择文章的状态']);
        }
        $sort = intval($request->post('sort'));
        if (empty($sort)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '文章的排序不能为空且只能为一个大于零的数']);
        }
        $content = $request->post('content' ,"");
        if (empty($content)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '文章的内容不能为空']);
        }

        $model = new Article();

        $model->title = $title;
        $model->type = $type;
        $model->image = $image;
        $model->state = $state;
        $model->sort = $sort;
        $model->content = $content;

        if (!$model->save()) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '保存失败']);
        }
        return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
    }

    /**
     * 根据文章id获取文章内容
     * @return string
     */
    public function actionGetArticle()
    {
        $result = $data = [];
        $request = Yii::$app->request;
        $id = $request->get('id', 0);
        $result = ArticleModel::getArticleById($id);
        if (!$result) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数有误']);
        }
        $data = [
            'id' => $result->id,
            'title' => $result->title ?? '',
            'type' => $result->type ?? '',
            'state' => (string)$result->state ?? '',
            'content' => $result->content ?? '',
            'image' => $result->image ?? '',
            'sort' => $result->sort ?? '',
        ];
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'results' => $data
        ]);
    }

    /**
     * 跟新文章内容
     * @return string
     */
    public function actionArticleUpdate()
    {
        $request = Yii::$app->request;
        $id = trim($request->get('id'));
        if (empty($id)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数有误']);
        }
        $title = trim($request->post('title'));
        if (empty($title)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请输入文章标题']);
        }
        $type = trim($request->post('type'));
        if (empty($type)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请选择文章类型']);
        }
        $image = trim($request->post('image', ""));
        /*if (empty($image)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请上传文章图片']);
        }*/
        $state = intval($request->post('state'));
        if (empty($state)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '请上选择文章的状态']);
        }
        $sort = intval($request->post('sort'));
        if (empty($sort)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '文章的排序不能为空且只能为一个大于零的数']);
        }
        $content = $request->post('content' ,"");
        if (empty($content)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '文章的内容不能为空']);
        }
        $model = ArticleModel::getArticleById($id);
        if (!$model) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '文章不存在']);
        }
        $model->title = $title;
        $model->type = $type;
        $model->state = $state;
        $model->sort = $sort;
        $model->content = $content;
        if ($model->image != $image && !empty($image)) { // 如果图片地址不一样，更新图片地址，删除原图
           /* if (!empty($model->image) && file_exists('.' . $model->image)) {
                unlink('.' . $model->image); // 如果原图存在
            }*/
            $model->image = $image; // 跟新图片地址
        }
        if (!$model->save()) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '保存失败']);
        }
        return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
    }

    /**
     * 删除文章
     * @return string
     */
    public function actionArticleDel()
    {

        $request = Yii::$app->request;
        $id = $request->post('article_id', '');
        $result = ArticleModel::delArticleById($id);
        if (!$result) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
    }

    /**
     * 短信列表
     * @return string
     */
    public function actionMessage()
    {
        $results = $data = [];
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $mobile = $request->get('mobile', '');
        $type = $request->get('type', '');
        $state = $request->get('state', '');
        $results = MobileLogModel::getMobileLogList($offset, $limit, $mobile, $type, $state);
        foreach ($results['result'] as $row) {
            $data [] = [
                'id' => $row->id,
                'real_name' => $row->user->real_name ?? '',
                'mobile' => $row->mobile,
                'type' => $row->type,
                'send_message' => $row->send_message ?? '',
                'created_at' => $row->created_at,
                'state' => json_decode($row->return_message)->Message,
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $results['count'],
            'results' => $data
        ]);
    }
}