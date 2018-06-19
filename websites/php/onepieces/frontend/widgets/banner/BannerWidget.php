<?php
namespace frontend\widgets\banner;
/**
 * 轮播图部件
 */

use common\models\BannerModel;
use Yii;
use yii\base\Widget;

class BannerWidget extends Widget
{
    public $items = [];
    
    public function init()
    {
        parent::init();
       
    }
   
    public function run()
    {
        $banner = [];
        $result = BannerModel::getAllBanner(0, -1); // 获取banner信息
        foreach ($result['result'] as $k => $v) {
            if ($v['state'] == BannerModel::STATE_SHOW) {
                $banner[] = [
                    'serial' => $k,
                    'name' => $v['name'],
                    'link' => $v['link'],
                    'image' => $v['image'],
                ];
            }
        }
        return $this->render('index',['banner' => $banner]);
    }
    
    
}