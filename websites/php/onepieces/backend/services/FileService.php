<?php
namespace backend\services;

use Yii;
use yii\helpers\Json;
use backend\bases\BackendService;

class FileService extends BackendService
{
    public static function saveParams($data = [], $file, $reset = false, $remove = false)
    {
        if (!file_exists($file)) {
           return false;
        }

        $old = include($file);
        if (is_array($old)) {
            foreach ($old as $key => $value) {
                if (!isset($data[$key])) {
                    if (!$remove) {
                        $data[$key] = $value;
                    }
                } else {
                    if (!$reset) {
                        $data[$key] = $value;
                    }
                }
            }
        }
        
        //写入文件
        $str = "<?php\nreturn [\n";
        foreach ($data as $key => $value) {
            $value = htmlspecialchars($value);
            $str .= "\t'{$key}' => '{$value}',\n";
        }
        $str .= '];';
        if(!file_put_contents($file,$str))
            return false;
        return true;
    }
}

