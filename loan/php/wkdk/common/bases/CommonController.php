<?php

/**
 * 控制器基类
 * BaseController.php
 * @author     addition
 */

namespace common\bases;

use yii\web\Controller;
use yii\helpers\Json;
use Yii;

class CommonController extends Controller 
{

    public function beforeAction($action)
    {
        // if ($action->id == 'signup' || $action->id =='upload-icon' || $action->id =='login') {
        //     $this->enableCsrfValidation = false;
        // }
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    /**
     * 获取POST或者GET传递的数据
     * @param 参数名称
     * @return array 
     * @author addition
     */
    public function getParams($param = '', $defaultValue = NULL)
    {
        if ($param != '') {
            $result = \Yii::$app->request->post($param);
            if (empty($result)) {
                $result = \Yii::$app->request->get($param, $defaultValue);
            }
            return trim($result);
        } else {
            $result = \Yii::$app->request->post() or $result = \Yii::$app->request->get();
        }
        return $result;
    }

    /**
     * 返回成功JSON格式
     * author :addition
     * @param string
     * @return JSON
     */
    public function jsonSuccess($message = '操作成功') {
        return json_encode([
            'code' => '1000',
            'message' => $message
        ]);
    }

    /**
     * 返回失败JSON格式
     * tags
     * author :addition
     * @param string
     * @return JSON
     */
    public function jsonFail($message = '操作失败') 
    {
        return json_encode([
            'code' => '2000',
            'message' => $message
        ]);
    }

    /**
     * AR转数组
     * tags
     * @author addition
     * @param unknowtype
     * @return array
     */
    public function objectToArray($object, &$array = [])
    {
        foreach ($object as $value) {
            $array[] = $value->getAttributes();
        }
    }

    /**
     * 图片上传
     * @param unknown $img
     * @param unknown $savePath
     */
    public function upload($img, $path, $saveHomeImg = false, $homerId = 0)
    {
        $tmpPath = '/tmp/' . time() . rand(100000, 999999) . '.jpg';
        $this->base64_to_img($img, $tmpPath);
        $imagesName = sha1_file($tmpPath) . '.jpg';
        $returnPath = $path . $imagesName;
        $result = ResourceService::ossFileUpload($returnPath, $tmpPath);
        if (!$result['code']) {
            return ['code' => 0, 'path' => $tmpPath, 'name' => $imagesName, 'returnPath' => $returnPath];
        }
        //上传成功
        if ($saveHomeImg) {//存入生活家图片表 pengcaiyun
            HomeImagesModel::addImg($homerId, $returnPath);
        }
        return ['code' => 1, 'path' => $tmpPath, 'name' => $imagesName, 'returnPath' => $returnPath];
    }

    /**
     * base64转图片保存
     * @auther: addition
     * @param string
     */
    public function base64_to_img($base64_string, $output_file)
    {
        $ifp = fopen($output_file, "wb");
        
        fwrite($ifp, base64_decode($base64_string));
        fclose($ifp);
        return( $output_file );
    }
    
}
