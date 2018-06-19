<?php
namespace common\services;
use common\bases\CommonService;
use Yii;

class SiteService extends CommonService
{
    /**
     * 获取下个月的第{$day}天的格式化日期
     * @param string $date 指定日期 2017-12-20
     * @param integer $day 第几天 默认【0】0-标识使用params配置的repayment_day还款日
     * @param string $format 返回的日期格式
     * @return string 返回日期字符串
     */
    public static function getNextMonthDate($date, $day = 0, $format = 'Y-m-d')
    {
        if ($day == 0) {
            $day = Yii::$app->params['repayment_day']; // 还款日
        }
        $month = (integer)date('m', strtotime($date)); // 获取给定日期的月份
        $year = (integer)date('Y', strtotime($date)); // 获取给定日期的年份
        // 若给定日期为12月
        if ($month == 12) {
            $nextMonth = '01'; // 下个月的月份
            $year = (string)($year + 1); // 下个月的年份
        } else {
            $nextMonth = (string)($month + 1); // 下个月的月份
        }
        return date($format, strtotime($year .'-' . $nextMonth. '-' . $day)); // 返回指定格式的日期字符串
    }

    /**
     * 获取上个月的第{$day}天的格式化日期
     * @param string $date 指定日期 2017-12-20
     * @param integer $day 第几天 默认【0】0-标识使用params配置的repayment_day还款日
     * @param string $format 返回的日期格式
     * @return string 返回日期字符串
     */
    public static function getPrevMonthDate($date, $day = 0, $format = 'Y-m-d')
    {
        if ($day == 0) {
            $day = Yii::$app->params['repayment_day']; // 还款日
        }
        $month = (integer)date('m', strtotime($date)); // 获取给定日期的月份
        $year = (integer)date('Y', strtotime($date)); // 获取给定日期的年份
        // 若给定日期为1月
        if ($month == 1) {
            $nextMonth = '12'; // 上个月的月份
            $year = (string)($year - 1); // 上个月的年份
        } else {
            $nextMonth = $month - 1; // 上个月的月份
            if ($nextMonth < 10) {
                $nextMonth = str_pad((string)$nextMonth, 2, '0', STR_PAD_LEFT);
            }
        }
        return date($format, strtotime($year .'-' . $nextMonth. '-' . $day)); // 返回指定格式的日期字符串
    }

    /**
     * 返回当前时间到指定日期的天数
     * @param string $date 指定日期 2017-12-20
     * @return integer 返回距离的天数
     */
    public static function getDaysDistance($date)
    {

        return (strtotime($date) - strtotime(date('Y-m-d'))) / 3600 / 24;
    }

    /**
     * 获取最近还款日
     * @param string $date 指定日期 2017-12-26
     * @param string $format 返回的日期格式
     * @return string 返回日期字符串
     */
    public static function getRecentRepayingDate($date, $format = 'Y-m-d')
    {
        // 如果指定日期在当月10号之前（包含10号），返回当月10号的日期；若在10号之后，返回次月10号的日期
        if (strtotime($date) <= strtotime(date('Y-m-10', strtotime($date)))) {
            return date('Y-m-10', strtotime($date));
        } else {
            return self::getNextMonthDate($date);
        }
    }

    /**
     * 获取上一次用户还款日
     * @param string $date 指定日期 2017-12-26
     * @param string $format 返回的日期格式
     * @return string 返回日期字符串
     */
    public static function getLastRepayedDate($date, $format = 'Y-m-d')
    {
        // 如果指定日期在当月10号之前（包含10号），返回当月10号的日期；若在10号之后，返回次月10号的日期
        if (strtotime($date) > strtotime(date('Y-m-10', strtotime($date)))) {
            return date('Y-m-10', strtotime($date));
        } else {
            return self::getPrevMonthDate($date);
        }
    }

    /**
     *  拼接数组为字符串形式 如[1,2,3] => '[1],[2],[3]'
     * @param array $array 待处理的数组
     * @param string $prevSign 前缀默认 【[】
     * @param string $suffixSign 前缀默认 【]】
     * @return string 返回处理完成的字符串
     */
    public static function implodeBySpecialSign($array, $prevSign = '[', $suffixSign = ']')
    {
        foreach ($array as $k => $v) {
            $array[$k] = sprintf('%s%s%s', $prevSign, $v, $suffixSign);
        }
        return implode(',', $array);
    }
}