<?php


namespace frontend\widgets\news;


use common\models\ArticleModel;
use common\models\NavigationModel;
use yii\data\Pagination;
use Yii;
use yii\base\Widget;

class NewsWidget extends Widget
{


    /**
     * 显示条数
     * @var int
     */
    public $limit = 3 ;

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
        $result = [];
        $curPage = Yii::$app->request->get('page',1); // 当前页数回
        $newsId = Yii::$app->request->get('news_id',''); // 新闻详情id 偏移量
        $nav = Yii::$app->request->get('nav','');

        // 获取案例中心的所有导航
        $articleId = Yii::$app->params['article_id'] ?? ''; // 获得新闻中心中心的配置id
        // 当不存在导航时案例导航应限制应为5
        if (empty($nav)) {
            $this->navLimit = $this->navLimit - 1;
        }
        $navList = NavigationModel::findShowChildrenByPid($articleId, $this->navLimit);
        $navLt = [];
        foreach ($navList as $row) {
            $navLt [] = [
                'id' => $row->id,
                'name' => $row->name,
            ];
        }

        $result['current_nav'] = $nav ?? ''; // 当前选中导航id
        $result['nav'] = $navLt ?? []; // 导航列表

        // 列表页
        if (!$newsId) {
            $cond = []; // 条件
            if ($nav) {
                $cond = ['nav_id' => $nav];
            }
            $cond['state'] = ArticleModel::STATE_SHOW;
            $orderBy = ['sort'=>SORT_DESC, 'id'=>SORT_DESC];
            $res = ArticleModel::getList($cond, $curPage, $this->limit, $orderBy);

            $result['body'] = $res['data'] ?: [];

            // 列表起始偏移量
            $result['start_offset'] = $res['start'] ?? 0;

            //是否显示分页
            if($this->page){
                $page = new Pagination(['totalCount' => $res['count'], 'pageSize' => $res['pageSize']]);
                $result['page'] = $page;
            }
            return $this->render('index', ['data' => $result]);
        } else { // 详情页

            // 获取当前详情
            $offset = $newsId - 1;
            $limit = 1;
            $res = ArticleModel::getOneRecord($offset, $limit, $nav);
            $result['news'] = $res['result'];
            // 上一页
            $result['pre_show'] = ($newsId - 1) > 0 ? 1 : 0;
            $result['pre'] = $newsId - 1;
            // 下一页
            $result['next_show'] = ($newsId + 1) <= $res['count'] ? 1 : 0;
            $result['next'] = ($newsId + 1) <= $res['count'] ? $newsId + 1 : $res['count'];

            return $this->render('detail', ['data' => $result]);
        }

    }

}