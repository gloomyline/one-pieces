<?php

namespace common\bases;
use yii\base\Component;
use Yii;
/**
 * 服务类基类
 */
class CommonService extends Component
{
    /**
     * 字符串转数组
     *
     * @param string $string 字符串
     * @param string $delimiter 分隔符
     * @return array
     */
    public function stringToArray($string, $delimiter = ",") 
    {
        if (strstr($string, $delimiter)) {
            $arr = explode($delimiter, $string);
        } else {
            $arr = [$string];
        }
        return $arr;
    }
}