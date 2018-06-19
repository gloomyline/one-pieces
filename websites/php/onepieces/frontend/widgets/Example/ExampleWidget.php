<?php


namespace frontend\widgets\example;


use common\models\ExampleModel;
use common\models\NavigationModel;
use Yii;
use yii\base\Widget;
use yii\data\Pagination;
use yii\helpers\Url;

class ExampleWidget extends Widget
{

    /**
     * 显示条数
     * @var int
     */
    public $limit = 9 ;

    /**是否显示分页
     * @var bool
     */
    public $page = true;

    /**
     * 导航数量限制
     * @var int
     */
    public $navLimit = 6;

    public function run()
    {
        $curPage = Yii::$app->request->get('page',1); // 当前页数回
        $nav = Yii::$app->request->get('nav','');

        // 获取案例中心的所有导航

        $exampleId = Yii::$app->params['example_id'] ?? ''; // 获得案例中心的配置id
        // 当不存在导航时案例导航应限制应为5

        if (empty($nav)) {
            $this->navLimit = $this->navLimit - 1;
        }
        $navList = NavigationModel::findShowChildrenByPid($exampleId, $this->navLimit);
        $navLt = [];
        foreach ($navList as $row) {
            $navLt [] = [
                'id' => $row->id,
                'name' => $row->name,
            ];
        }

        // 条件
        $cond = [];
        if ($nav) {
            $cond = ['nav_id' => $nav];
        }
        $cond['state'] = ExampleModel::STATE_SHOW;
        $orderBy = ['sort'=>SORT_DESC, 'id'=>SORT_DESC];
        $res = ExampleModel::getList($cond, $curPage, $this->limit, $orderBy);

        $result['current_nav'] = $nav ?? ''; // 当前选中导航id
        $result['nav'] = $navLt ?? []; // 导航列表
        $result['body'] = $res['data'] ?? [];
        //是否显示分页
        if($this->page){
            $page = new Pagination(['totalCount'=>$res['count'],'pageSize'=>$res['pageSize']]);
            $result['page'] = $page;
        }
        //print_r($result);exit();
        return $this->render('index', ['data'=>$result]);
    }
}