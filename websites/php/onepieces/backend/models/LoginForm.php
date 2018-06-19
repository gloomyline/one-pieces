<?php
namespace backend\models;

use Yii;
use common\models\User;
use common\bases\CommonForm;
use backend\models\Admin;
use backend\models\AdminModel;

/**
 * user form
 */
class LoginForm extends CommonForm
{
    public $username;
    public $password;
    public $rememberMe = false;
    public $verifyCode;
    public $captcha;

    private $_user = false;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['password'], 'required','message'=>'密码不能为空'],
            [['username'], 'required','message'=>'手机号不能为空'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean','message'=>'输入格式有误'],
            // password is validated by validatePassword()
            ['username', 'validatePassword'],
            ['verifyCode', 'required'],
            ['verifyCode', 'captcha'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, '用户名或密码不正确！.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        } else {
            return false;
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $user = Admin::findByUsername($this->username);
            $this->_user = $user;
            if ($user && $user->state == AdminModel::RESIGNED) {
                $this->_user = false;
            }
        }

        return $this->_user;
    }
    
    public function attributeLabels(){
        return [
            'username'=>'用户名',
            'password'=>'密码'
        ];
    }
}
