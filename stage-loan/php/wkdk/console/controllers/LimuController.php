<?php
namespace console\controllers;

use common\models\LimuAreaModel;
use common\services\LimuService;
use Yii;
use yii\console\Controller;

class LimuController extends Controller
{
    /**
     * 立木公积金/社保支持地区查询
     */
    public function actionLimuArea()
    {
        $result = LimuService::queryHouseFundAreas(); // 立木公积金支持地区查询
        if ($result) {
            // 成功
            if ($result->code == LimuService::API_SUCCESS_CODE) {
                foreach ($result->data as $k=>$v) {
                    $limuArea = LimuAreaModel::getAreaByAreaCode($v->areaCode);
                    if ($limuArea) {
                        LimuAreaModel::updateById($limuArea['id'], $v->status, LimuAreaModel::TYPE_HOUSE_FUND);
                    } else {
                        LimuAreaModel::addLimuArea($v->areaCode, $v->areaName, $v->status, $v->sortLetter, LimuAreaModel::TYPE_HOUSE_FUND); // 存入limu_area表
                    }
                }

                Yii::info(sprintf('Success: Provident fund support area has been updated successfully. '), 'limu');
            } else {
                Yii::error(sprintf('Error: [%s]%s. ', $result->code, $result->msg));
            }
        }

        $socialSecurityResult = LimuService::querySocialSecurityAreas(); // 立木社保支持地区查询
        if ($socialSecurityResult) {
            // 成功
            if ($socialSecurityResult->code == LimuService::API_SUCCESS_CODE) {
                foreach ($socialSecurityResult->data as $k=>$v) {
                    $limuArea = LimuAreaModel::getAreaByAreaCode($v->areaCode);
                    if ($limuArea) {
                        LimuAreaModel::updateById($limuArea['id'], $v->status, LimuAreaModel::TYPE_SOCIAL_SECURITY);
                    } else {
                        LimuAreaModel::addLimuArea($v->areaCode, $v->areaName, $v->status, $v->sortLetter, LimuAreaModel::TYPE_SOCIAL_SECURITY); // 存入limu_area表
                    }
                }
                Yii::info(sprintf('Success: Social security support area has been updated successfully. '), 'limu');
            } else {
                Yii::error(sprintf('Error: [%s]%s. ', $socialSecurityResult->code, $socialSecurityResult->msg));
            }
        }
    }

}