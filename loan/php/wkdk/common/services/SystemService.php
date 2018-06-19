<?php
namespace common\services;
use common\extend\FileUtil;
use yii\data\Pagination;

class SystemService
{         
    /**
     * 导出CSV文件
     * 
     * @param string $fileName 导出文件名
     * @param array $titles 标题
     * @param array $data 导出数据
     * @return string
     */    
    public static function exportCsv($fileName, $titles, $data)
    {
        FileUtil::exportCsvHeaders($fileName);
        $csv = implode("\t", $titles) . "\n";
        foreach ($data as $value) {
            $csv .= implode("\t", $value) . "\n";
        }
        return chr(255) . chr(254) . mb_convert_encoding($csv, 'UTF-16LE', 'UTF-8');
    }
}