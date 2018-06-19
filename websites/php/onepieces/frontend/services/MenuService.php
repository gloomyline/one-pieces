<?php
namespace frontend\services;

use common\models\NavigationModel;
use frontend\bases\FrontendService;
use Yii;

class MenuService extends FrontendService
{
    /**
     * 获取导航菜单
     * @param integer $pid 父ID
     * @param int $level 导航层次
     * @return array|bool
     */
   public static function getMenu($pid, $level = 3)
   {
       $data = []; // 最终返回数据
       if ($level == 0 ) {
           return false;
       }
       $nav = NavigationModel::findShowChildrenByPid($pid);
       foreach ($nav as $k => $v) {
           $data[] = [
               'serial' => $k + 1,
               'id' => $v['id'],
               'pid' => $v['pid'],
               'name' => $v['name'],
               'description' => $v['description'],
               'link' => $v['link'],
               'is_open' => $v['is_open'],
               'children' => [],
           ];
           $data[$k]['children'] = self::getMenu($v['id'], $level - 1);
       }
       return $data;
   }
}