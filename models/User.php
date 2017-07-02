<?php

namespace app\models;

use Yii;

class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    public $_user;
    public $password;

    public static function tableName()
    {
        return 'user';
    }


    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],

            ['username', 'required'],
            ['username', 'unique', 'message' => 'This username has already been taken'],

            [['country', 'city'], 'string', 'max' => 100],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'message' => 'This email address has already been taken'],
            
            ['phone', 'unique', 'message' => 'This phone number has already been taken'],
        ];
    }


    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['auth_key' => $token]);
    }

    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function regenerateAuthKey($user_id)
    {
        $user = self::findOne(['id'=>$user_id]);
        $user->auth_key = Yii::$app->security->generateRandomString();
        if($user->save()){
            return true;
        } else {
            return false;
        }
    }

    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    //Get token from new user
    public function getUserAuth()
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user->auth_key;
    }

    public function signup($request)
    {
        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->phone = $this->phone;
        $user->country = $this->country;
        $user->city = $this->city;
        $user->setPassword($request['User']['password']);
        $user->generateAuthKey();
        $user->status = 10;

        return $user->save() ? $user : null;
    }

    public function login($request)
    {
        $user = $this->findByUsername($request['username']);
        if($user){
            if(Yii::$app->security->validatePassword($request['password'], $user->password_hash)){
                return true;
            } else {
                return false;
            }
        } return false;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $time = time();
//            $this->access_token = Yii::$app->security->generateRandomString();
//            $this->token_expiry = $time + Yii::$app->params['token_expiry'];

            if ($this->isNewRecord) {
                $this->created_at = $time;
            }

            $this->updated_at = $time;

            return true;
        }

        return false;
    }

}
