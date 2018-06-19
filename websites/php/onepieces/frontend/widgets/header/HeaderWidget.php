<?php
namespace frontend\widgets\header;

use common\models\NavigationModel;
use frontend\services\MenuService;
use Yii;
use yii\base\Widget;

class HeaderWidget extends Widget
{
    public $items = [];

    public function init()
    {
        parent::init();

    }

    public function run()
    {
        $nav = MenuService::getMenu(NavigationModel::TOP_NAV);
        return $this->render('index', ['current' => Yii::$app->request->getPathInfo(), 'nav' =>$nav]);
    }
}