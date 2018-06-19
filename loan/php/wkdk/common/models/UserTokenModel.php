<?php

namespace common\models;

use common\bases\CommonModel;

class UserTokenModel extends CommonModel {

    // Access Token的过期时间30天的秒数
    const TOKEN_EXPIRY = 2592000;

    /**
     * 生成token
     * 
     * @param integer $userId 用户ID
     * @param string $source 来源
     * @return object [[UserToken]] | false
     */
    public static function generateUserToken($userId, $source) {
        $time = time();
        $userToken = new UserToken();
        $userToken->userid = $userId;
        $userToken->expiry_timestamp = $time + self::TOKEN_EXPIRY;
        $userToken->source = $source;
        $userToken->created_at = $time;
        // 生成Token
        $userToken->access_token = \Yii::$app->getSecurity()->generateRandomString();
        if ($userToken->save()) {
            return $userToken;
        } else {
            return false;
        }
    }

    /**
     * 生成token
     * 
     * @param string $accessToken 用户AccessToken
     * @return object [[UserToken]] | false
     */
    public static function refreshUserToken($accessToken) {
        $userToken = self::getUserTokenByAccessToken($accessToken);
        $time = time();
        $userToken->expiry_timestamp = $time + self::TOKEN_EXPIRY;
        if ($userToken->save()) {
            return $userToken;
        } else {
            return false;
        }
    }

    /**
     * 销毁token（设置为过期）
     * 
     * @param object $accessToken 用户AccessToken
     * @return object [[UserToken]] | false
     */
    public static function dropUserToken($accessToken) {
        $userToken = self::getUserTokenByAccessToken($accessToken);
        if (!$userToken) {
            return false;
        }
        $time = time();
        $userToken->expiry_timestamp = $time - 1;
        if ($userToken->save()) {
            return $userToken;
        } else {
            return false;
        }
    }

    /**
     * 根据 access token 获取UserToken
     * 
     * @param integer $accessToken 用户AccessToken
     * @return object | false
     */
    public static function getUserTokenByAccessToken($accessToken) {
        $userToken = UserToken::findOne(['access_token' => $accessToken]);
        return $userToken ? $userToken : false;
    }

    /**
     * 校验Access Token
     * 
     * @param string $accessToken 用户Access Token
     * @return json
     */
    public static function validateAccessToken($accessToken) {
        if (!$accessToken) {
            return false;
        }
        $userToken = self::getUserTokenByAccessToken($accessToken);
        $nowTime = time();
        if ($userToken && $nowTime <= $userToken->expiry_timestamp) {
            return $userToken;
        } else {
            return false;
        }
    }

}
