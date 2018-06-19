<?php
namespace frontend\widgets\partner;

use common\models\PartnerModel;
use Yii;
use yii\base\Widget;

class PartnerWidget extends Widget
{
    public $items = [];

    public function init()
    {
        parent::init();

    }

    public function run()
    {
        $partner = [];
        $result =  PartnerModel::getAllPartner(0, -1);
        foreach ($result['result'] as $v) {
            if ($v['state'] == PartnerModel::STATE_SHOW) {
                $partner[] = [
                    'id' => $v['id'],
                    'name' => $v['name'],
                    'link' => $v['link'],
                    'image' => $v['image'],
                ];
            }
        }
        return $this->render('index', ['partner' => $partner]);
    }
}