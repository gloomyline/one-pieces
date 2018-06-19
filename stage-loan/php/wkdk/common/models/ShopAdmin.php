<?php

namespace common\models;

use Yii;
use yii\web\IdentityInterface;
use yii\base\InvalidParamException;
use yii\base\NotSupportedException;

/**
 * This is the model class for table "shop_admin".
 *
 * @property string $id
 * @property string $username
 * @property string $password
 * @property integer $shop_id
 * @property integer $state
 * @property string $login_ip
 * @property string $login_time
 * @property string $created_at
 * @property string $updated_at
 */
class ShopAdmin extends \yii\db\ActiveRecord implements IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_admin';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['shop_id', 'state'], 'integer'],
            [['login_time', 'created_at', 'updated_at'], 'safe'],
            [['username'], 'string', 'max' => 30],
            [['password'], 'string', 'max' => 64],
            [['login_ip'], 'string', 'max' => 15],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password' => 'Password',
            'shop_id' => 'Shop ID',
            'state' => 'State',
            'login_ip' => 'Login Ip',
            'login_time' => 'Login Time',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    public static function findByUsername($username)
    {
        return self::findOne(['username'=>$username]);
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
                print_r($e);
                return false;
            }
        }
        return true;
    }

    public static function findIdentity($userId)
    {
        return static::findOne(['id' => $userId]);
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
}
