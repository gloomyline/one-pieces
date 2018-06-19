<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/20
 * Time: 9:56
 */

namespace backend\controllers;


use common\models\NavigationModel;
use Yii;
use yii\helpers\Json;
use common\models\ArticleModel;
use backend\bases\BackendController;

class ArticleController extends BackendController
{
    // 文章列表
    public function actionIndex()
    {
        $request = Yii::$app->request;
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);
        $navId = intval($request->get('nav_id', ''));
        $result = $data = [];
        $result = ArticleModel::getAllArticle($offset, $limit, $navId);
        foreach ($result['result'] as $row) {
            $data[] = [
                'id' => $row->id,
                'title' => $row->title,
                'nav_id' => $row->nav_id,
                'description' => $row->description,
                'sort' => $row->sort,
                'image' => $row->image,
                'state' => $row->state,
                'created_at' => $row->created_at,
            ];
        }
        $articleId = Yii::$app->params['article_id'] ?? '';
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'count' => (int) $result['count'],
            'article_id' => (int)$articleId,
            'results' => $data
        ]);
    }

    // 添加文章
    public function actionAdd()
    {

        $request = Yii::$app->request;
        $navId = intval($request->post('nav_id'));
        $title = trim($request->post('title', ''));
        $author = trim($request->post('author', ''));
        $description = trim($request->post('description', ''));
        $notice = intval($request->post('notice', ''));
        $state = intval($request->post('state'));
        $sort = intval($request->post('sort'));
        $image = trim($request->post('image'));
        $content = $request->post('content', ''); // 内容

        // 表单检验
        if (empty($navId)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '分类不能为空']);
        }
        if (empty($title)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '名称不能为空']);
        }
        if (empty($author)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '文章作者不能为空']);
        }
        if (empty($state) || !in_array($state, [ArticleModel::STATE_SHOW, ArticleModel::STATE_HIDDEN])) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }
        if (empty($notice) || !in_array($notice, [ArticleModel::NOTICE_TRUE, ArticleModel::NOTICE_FALSE])) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }

        $data = [
            'nav_id' => $navId,
            'title' => $title,
            'author' => $author,
            'description' => $description,
            'notice' => $notice,
            'sort' => $sort,
            'state' => $state,
            'image' => $image,
            'content' => $content ?? '',
        ];
        $result = ArticleModel::add($data);
        if (!$result) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '添加失败']);
        }
        return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
    }

    // 文章修改
    public function actionUpdate()
    {
        $request = Yii::$app->request;
        $id = intval($request->post('id'));
        $navId = intval($request->post('nav_id'));
        $title = trim($request->post('title', ''));
        $author = trim($request->post('author', ''));
        $description = trim($request->post('description', ''));
        $notice = intval($request->post('notice', ''));
        $state = intval($request->post('state'));
        $sort = intval($request->post('sort'));
        $image = trim($request->post('image'));
        $content = $request->post('content', ''); // 内容

        // 表单检验
        $article = ArticleModel::findOneById($id);
        if (!$article) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误,操作的记录不存在']);
        }
        if (empty($navId)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '分类不能为空不']);
        }
        if (empty($title)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '名称不能为空']);
        }
        if (empty($author)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '文章作者不能为空']);
        }
        if (empty($state) || !in_array($state, [ArticleModel::STATE_SHOW, ArticleModel::STATE_HIDDEN])) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误2']);
        }
        if (empty($notice) || !in_array($notice, [ArticleModel::NOTICE_TRUE, ArticleModel::NOTICE_FALSE])) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数错误']);
        }

        $data = [
            'nav_id' => $navId,
            'title' => $title,
            'author' => $author,
            'description' => $description,
            'notice' => $notice,
            'sort' => $sort,
            'state' => $state,
            'image' => $image,
            'content' => $content ?? '',
        ];
        $result = ArticleModel::update($id, $data);
        if (!$result) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '保存成功']);
        }
        return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
    }

    // 删除文章
    public function actionDel()
    {
        $request = Yii::$app->request;
        $id = intval($request->post('article_id', ''));

        if (empty($id)) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '无效参数！请重试。']);
        }

        if (ArticleModel::delById($id)) {
            return Json::encode(['status' => self::STATUS_SUCCESS, 'error_message' => '']);
        }
        return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '删除失败']);

    }

    // 添加文章时下发的数据
    public function actionNeed()
    {
        $articleId = Yii::$app->params['article_id'];
        $result = $data = [];
        $result = NavigationModel::findChildrenByPid($articleId);
        foreach ($result as $row) {
            $data[] = [
                'id' => (int)$row->id,
                'name' => $row->name
            ];
        }
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'results' => $data
        ]);
    }

    // 编辑文章时下发数据
    public function actionGetArticle()
    {
        $result = $data = [];
        $request = Yii::$app->request;
        $id = $request->get('id', 0);
        $result = ArticleModel::findOneById($id);
        if (!$result) {
            return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '参数有误']);
        }
        $data = [
            'id' => $result->id,
            'title' => $result->title ?? '',
            'author' => $result->author ?? '',
            'image' => $result->image ?? '',
            'nav_id' => $result->nav_id ?? '',
            'state' => $result->state ?? '',
            'notice' => $result->notice ?? '',
            'content' => $result->content ?? '',
            'description' => $result->description ?? '',
            'sort' => $result->sort ?? '',
        ];
        return Json::encode([
            'status' => self::STATUS_SUCCESS,
            'results' => $data
        ]);
    }

}