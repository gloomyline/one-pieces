<?php

namespace backend\models;

use Yii;
use backend\models\AuthAssignment;

use yii\web\IdentityInterface;
use yii\base\InvalidParamException;
use yii\base\NotSupportedException;

/**
 * This is the model class for table "admin".
 *
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property integer $is_admin
 * @property string $real_name
 * @property integer $state
 */
class Admin extends \yii\db\ActiveRecord implements IdentityInterface
{
    private $_routes = null;

    public static function tableName()
    {
        return 'admin';
    }

    public function rules()
    {
        return [
            [['state'], 'integer'],
            [['username'], 'string', 'max' => 30],
            [['password'], 'string', 'max' => 64],
            [['real_name'], 'string', 'max' => 10],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password' => 'Password',
            'real_name' => 'Real Name',
            'state' => 'State',
        ];
    }

    public function getRole()
    {
        return $this->hasOne(AuthAssignment::className(), ['user_id' => 'id']);
    }

    public function validatePassword($password){
        if(empty($password)){
            return false;
        }else{
            try {
                if($this->password != md5($password) && !Yii::$app->getSecurity()->validatePassword($password, $this->password)){
                    return false;
                }
            } catch (InvalidParamException $e) {
                Yii::error($e);
                return false;
            }
        }
        return true;
    }
    public static function findByUsername($username)
    {
        return self::findOne(['username'=>$username]);
    }
    
    public static function findIdentity($userid)
    {
        return static::findOne(['id' => $userid]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        return true;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function getRoutes()
    {
        if (is_null($this->_routes)) {
            $this->_routes = array_keys(Yii::$app->authManager->getPermissionsByUser($this->id));
        }
        return $this->_routes;
    }
}
