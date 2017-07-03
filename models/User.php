<?php

namespace app\models;

use Yii;

class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    public $_user;
    public $password;
    public $photo;

    public static function tableName()
    {
        return 'user';
    }


    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],

            [['avatar'], 'string', 'max' => 255],
            ['username', 'required'],

            [['country', 'city'], 'string', 'max' => 100],

            [['photo'], 'file'],

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

    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
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
            $this->_user = User::findByEmail($this->email);
        }

        return $this->_user->auth_key;
    }

    //Basic SignUp
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

    //Google SignUp
    public function gregister($accessToken)
    {
        $url = 'https://www.googleapis.com/oauth2/v1/userinfo?access_token=' . $accessToken;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($result);

        $user = new User();
        $user->username = $result->name;
        $user->email = $result->email;
        if($this->findByEmail($result->email)){
            $this->auth_key = $this->findByEmail($result->email)->auth_key;
            return $this->auth_key;
        }
        $this->email = $result->email;
        $user->avatar = $result->picture;
        $user->setPassword($result->id);
        $user->generateAuthKey();
        $user->status = 10;

        return $user->save() ? $user : null;
    }

    //Facebook SignUp
    public function fregister($accessToken)
    {
        $url = 'https://graph.facebook.com/me?access_token=' . $accessToken;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($result);
        var_dump($result);die;

        $user = new User();
        $user->username = $result->name;
        $user->email = $result->email;
        if($this->findByEmail($result->email)){
            $this->auth_key = $this->findByEmail($result->email)->auth_key;
            return $this->auth_key;
        }
        $this->email = $result->email;
        $user->avatar = $result->picture;
        $user->setPassword($result->id);
        $user->generateAuthKey();
        $user->status = 10;

        return $user->save() ? $user : null;
    }

    public function login($request)
    {
        $user = $this->findByEmail($request['email']);
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
