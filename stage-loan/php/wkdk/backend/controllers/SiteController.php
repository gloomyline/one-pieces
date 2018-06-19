<?php
namespace backend\controllers;

use backend\models\Uploader;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Json;
use backend\models\LoginForm;
use backend\bases\BackendController;
use yii\web\Request;
use yii\web\Cookie;
use yii\web\UploadedFile;
use common\services\QiniuService;

/**
 * Site controller
 */
class SiteController extends BackendController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error', 'errors', 'logout', 'jcrop', 'cut-img', 'captcha'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'csrf', 'upload', 'qiniu-upload', 'qiniu'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['get'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions() 
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'maxLength' => 4,
                'minLength' => 4,
                'width' => 100,
                'height' => 35,
                'offset' => 10,
                'testLimit' => 999
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    { 
        return $this->render('index');
    }


    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->login()) {
                setcookie('admin_csrf', Yii::$app->request->getCsrfToken(), 0, '/');
                //return $this->goBack();
                return $this->redirect('/vue-dist/#/statistics');
            } else {
                Yii::$app->session->setFlash('error', '帐号密码或验证码错误');
            }
        }
        return $this->renderPartial('login', [
            'model' => $model,
            'error' => Yii::$app->session->getFlash('error'),
        ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect(['/site/login']);
    }

    /**
     * 图片上传
     * @return string
     */
    public function actionUpload()
    {
        $config = [
            "pathFormat" => '/{yyyy}/article/{time}{rand:6}',
            "maxSize" => 1024*1024*2,
            "allowFiles" => ['.jpeg', '.png', '.jpg'],
            'uploadFilePath' => '/data/images/',
        ];
        if (Yii::$app->request->isPost) {
            $model = new Uploader('file', $config,'');

            if ($model) {
                $result = $model->getFileInfo();
                return Json::encode([
                    'id' => 101,
                    'url' => '/data/images'.$result['url'],
                ]);
            }
        }
        return Json::encode(['status' => self::STATUS_FAILURE, 'error_message' => '上传失败']);
    }

    //七牛图片上传
    public function actionQiniuUpload()
    {

        // 最大上传3M
        if ($_FILES['file']['size'] > 3 * 1024 * 1024) {
            return Json::encode([
                'status' => self::STATUS_FAILURE,
                'error_message' => '仅支持小于3MB的图片哦~',
            ]);
        }
        $result = QiniuService::qiniuImageUpload(QiniuService::BUCKET_FENQI);
        if ($result['code'] == QiniuService::STATUS_SUCCESS) {
            return Json::encode([
                'status' => self::STATUS_SUCCESS,
                'url' => $result['url'],
            ]);
        } else {
            return Json::encode([
                'status' => self::STATUS_FAILURE,
                'error_message' => $result['message'],
            ]);
        }
    }
    // 编辑器图片上传接口
    public function actionQiniu()
    {
        // 最大上传3M
        if ($_FILES['file']['size'] > 3 * 1024 * 1024) {
            return Json::encode([
                'state' => self::STATUS_FAILURE,
                'error_message' => '图片大于3MB',
            ]);
        }
        $result = QiniuService::qiniuImageUpload(QiniuService::BUCKET_FENQI);
        if ($result['code'] == QiniuService::STATUS_SUCCESS) {
            return Json::encode([
                'state' => self::STATUS_SUCCESS,
                'url' => $result['url'],
            ]);
        } else {
            return Json::encode([
                'state' => self::STATUS_FAILURE,
                'error_message' => $result['message'],
            ]);
        }
    }

}
