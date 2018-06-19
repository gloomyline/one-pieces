<?php
namespace common\extend;

use Yii;
/**
 * 操纵文件类
 */
class FileUtil
{
    /**
     * 导出execl文件
     * @param type $filename
     */
    public static function exportExcelHeaders($filename)   
    {   
        $headers = Yii::$app->response->headers;
        $headers->add('Content-type', 'application/octet-stream');
        $headers->add('Accept-Ranges', 'bytes');
        $headers->add('Content-type', 'application/vnd.ms-excel;charset=utf-8');
        $headers->add('Content-Disposition', 'attachment;filename=' . $filename);
    }
    
    /**
     * 导出CSV文件
     * @param type $filename
     */
    public static function exportCsvHeaders($filename)
    {
        header('Content-type: text/csv');
        header('Content-Disposition: attachment;filename=' . $filename . '.csv');
        header('Cache-Control: max-age=0');
    }

    /**
     * 对CSV内容字符串进行最后处理
     * @param $body
     * @return string
     */
    public static function getCsvBody($body, $append = false)
    {
        return ($append ? '' : chr(255) . chr(254)) . mb_convert_encoding($body, 'UTF-16LE', 'UTF-8');
    }
    
    /**
     * 逐行输出CSV内容
     * @param array $value
     */
    public static function outputCsv($value)
    {
        echo self::getCsvBody(implode("\t", $value) . "\n");
    }

    /**
     * 逐行附加CSV内容
     * @param array $value
     */
    public static function appendCsv($value)
    {
        echo self::getCsvBody(implode("\t", $value) . "\n", true);
    }
}