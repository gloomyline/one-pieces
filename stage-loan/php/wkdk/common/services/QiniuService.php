<?php
namespace common\services;

use Yii;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use yii\base\Exception;
use yii\helpers\Json;

class QiniuService
{
    const ACCESSKEY = 'KhvOYywdxZMvNlk2akSdTvU-wf9ZFtyZsvXiwDT2';
    const SECRETKEY = '_4Co0OIFroCOXFj_2jBokk7gHUOUsMreCk5Ogk01';
    const IMGURL = 'http://pic.wkdk.cn/';
    const BUCKET_FENQI = 'fenqi';
    const BUCKET_VITA = 'vita';
    const ALLOWED_IMG_EXT = ['bmp', 'jpg', 'jpeg', 'png'];
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILURE = 'failure';

    /**
     * @param $bucket 七牛存储空间
     * 对上传的图片保存到七牛并返回结果
     */
    public static function qiniuImageUpload($bucket)
    {
        if (empty($_FILES)) {
            return ['code' => self::STATUS_FAILURE, 'message' => '请选择图片'];
        }

        $orignFileName = $_FILES['file']['name']; // 原文件名
        $filePath = $_FILES['file']['tmp_name']; // 临时文件
        $fileSize = $_FILES['file']['size']; // 文件大小
        $ext = strrchr($orignFileName, '.'); // 后缀

        if (!in_array(strtolower(substr($ext, 1)), self::ALLOWED_IMG_EXT)) {
            return ['code' => self::STATUS_FAILURE, 'message' => '图片格式不支持'];
        }
        $newName = sha1_file($filePath);
        $key = $newName . $ext;
        
        $auth = new Auth(self::ACCESSKEY, self::SECRETKEY); // 构建鉴权对象
        $token = $auth->uploadToken($bucket); // 生成上传 Token
        $uploadMgr = new UploadManager(); // 初始化 UploadManager 对象并进行文件的上传。
        list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath); // 开始上传

        if ($err !== null) {
            return ['code' => self::STATUS_FAILURE, 'message' => $err->message()];
        } else {
            return ['code' => self::STATUS_SUCCESS, 'url' => self::IMGURL . $ret['key']];
        }
    }
}