<?php
namespace common\services;
use Yii;
use yii\helpers\Json;

class ZhimaService
{
    /**
     * 获取业务流水码（规则：年月日时分秒（14位） + 毫秒数（3位） + 用户Id（13位））
     * @param integer $userId 用户Id
     * @return string 返回生成的业务流水码
     */
    public static function BuildTransactionId($userId)
    {
        list($s1, $s2) = explode(' ', microtime());
        $count = (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000); // 当前的毫秒数

        $serialNo = str_pad((string)$userId, 13, '0', STR_PAD_LEFT); // 用户Id ,不足13位 前面补足0
        $transactionId = date('YmdHis') . substr((string)$count,10,3) . $serialNo; // 生成业务流水码
        return $transactionId; // 返回生成的业务流水码
    }
    
}