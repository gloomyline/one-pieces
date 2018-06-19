<?php
namespace frontend\widgets\footer;

use Yii;
use yii\base\Widget;

class FooterWidget extends Widget
{
    public function init()
    {
        parent::init();
    }
    public function run()
    {
        return $this->render('index');
    }
}