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
//            ['username', 'required'],

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

    public function deleteToken($user_id, $token)
    {
        $tokens = TokenDevices::find()
            ->where([
                'user_id' => $user_id,
            ])
            ->all();

        if($tokens){
            foreach($tokens as $token_d) {
                if($token_d->token_device == $token){
                    $token_d->delete();
                    return true;
                }
            }
        } else {
            return true;
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
        $user->avatar = 'No image';
        $this->username = $request['User']['name'];
        $user->username = $request['User']['name'];
        $user->email = $this->email;
        $user->phone = $this->phone;
        $user->country = $this->country;
        $user->city = $this->city;
        $user->setPassword($request['User']['password']);
        $user->generateAuthKey();
        $user->status = 10;

        if($user->save()){
            $model = new PushNotifications();
            $token = new TokenDevices();
            $token->user_id = $user->getPrimaryKey();
            $token->token_device = $request['User']['token_device'];
            if(isset($request['User']['android'])){
                $token->is_ios = 0;
            }
            $model->user_id = $user->getPrimaryKey();
            $token->save(false);
            $model->save(false);
            return true;
        } else {
            return false;
        }
    }

    //Google SignUp
    public function gregister($accessToken, $token_device, $android = null)
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
        $flag = false;
        $user->email = $result->email;
        if($this->findByEmail($result->email)){
            $user = $this->findByEmail($result->email);
            $this->auth_key = $user->auth_key;
            $token = TokenDevices::find()->where(['user_id'=>$user->id, 'token_device'=>$token_device])->all();
            if($token){
                foreach ($token as $item){
                    if($item->token_device == $token_device){
                        return true;
                    } else {
                        $flag = true;
                    }
                }
                if($flag == true){
                    $token_model = new TokenDevices();
                    $token_model->user_id = $user->id;
                    $token_model->token_device = $token_device;
                    if(isset($android)){
                        $token_model->is_ios = 0;
                    }
                    $token_model->save(false);
                    return true;
                }
            } else {
                $token_model = new TokenDevices();
                $token_model['user_id'] = $user->id;
                $token_model->token_device = $token_device;
                if(isset($android)){
                    $token_model->is_ios = 0;
                }
                $token_model->save(false);
                return true;
            }
        }
        $user->username = $result->name;
        $this->username = $result->name;
        $this->email = $result->email;
        $user->avatar = $result->picture;
        $this->avatar = $result->picture;
        $user->setPassword($result->id);
        $user->generateAuthKey();
        $user->status = 10;

        if($user->save()){
            $model = new PushNotifications();
            $token_model = new TokenDevices();

            $model->user_id = $user->getPrimaryKey();
            $token_model->user_id = $user->getPrimaryKey();
            $token_model->token_device = $token_device;
            if(isset($android)){
                $token_model->is_ios = 0;
            }

            $token_model->save(false);
            $model->save(false);
            return true;
        } else {
            return false;
        }
    }

    //Facebook SignUp
    public function fregister($accessToken, $token_device, $android = null)
    {
        $url = 'https://graph.facebook.com/me?fields=id,name,email,picture.type(large)&access_token=' . $accessToken;

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
        $flag = false;
        if($this->findByEmail($result->email)){
            $user = $this->findByEmail($result->email);
            $this->auth_key = $user->auth_key;
            $token = TokenDevices::find()->where(['user_id'=>$user->id, 'token_device'=>$token_device])->all();
            if($token){
                foreach ($token as $item){
                    if($item->token_device == $token_device){
                        return true;
                    } else {
                        $flag = true;
                    }
                }
                if($flag == true){
                    $token_model = new TokenDevices();
                    $token_model->user_id = $user->id;
                    $token_model->token_device = $token_device;
                    if(isset($android)){
                        $token_model->is_ios = 0;
                    }
                    $token_model->save(false);
                    return true;
                }
            } else {
                $token_model = new TokenDevices();
                $token_model['user_id'] = $user->id;
                $token_model->token_device = $token_device;
                if(isset($android)){
                    $token_model->is_ios = 0;
                }
                $token_model->save(false);
                return true;
            }
        }
        $user->username = $result->name;
        $user->email = $result->email;
        $this->email = $user->email;

        if(isset($result->picture->data->url)){
            $user->avatar = $result->picture->data->url;
            $this->avatar = $result->picture->data->url;
        } else {
            $user->avatar = 'No image';
        }
        $user->setPassword($result->id);
        $user->generateAuthKey();
        $user->status = 10;

        if($user->save()){
            $model = new PushNotifications();
            $token_model = new TokenDevices();

            $model->user_id = $user->getPrimaryKey();
            $token_model->user_id = $user->getPrimaryKey();
            $token_model->token_device = $token_device;
            if(isset($android)){
                $token_model->is_ios = 0;
            }

            $token_model->save(false);
            $model->save(false);
            return true;
        } else {
            return false;
        }
    }

    //LinkedIn SignUp
    public function lregister($accessToken, $token_device, $android = null)
    {
        $url = 'https://api.linkedin.com/v1/people/~:(id,email-address,formatted-name,picture-urls::(original))?oauth2_access_token=' . $accessToken . '&format=json';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $result = curl_exec($ch);
        $result = json_decode($result);
        curl_close($ch);

        $user = new User();
        $flag = false;
        $user->email = $result->emailAddress;
        if($this->findByEmail($result->emailAddress)){
            $user = $this->findByEmail($result->emailAddress);
            $this->auth_key = $user->auth_key;
            $token = TokenDevices::find()->where(['user_id'=>$user->id, 'token_device'=>$token_device])->all();
            if($token){
                foreach ($token as $item){
                    if($item->token_device == $token_device){
                        return true;
                    } else {
                        $flag = true;
                    }
                }
                if($flag == true){
                    $token_model = new TokenDevices();
                    $token_model->user_id = $user->id;
                    $token_model->token_device = $token_device;
                    if(isset($android)){
                        $token_model->is_ios = 0;
                    }
                    $token_model->save(false);
                    return true;
                }
            } else {
                $token_model = new TokenDevices();
                $token_model['user_id'] = $user->id;
                $token_model->token_device = $token_device;
                if(isset($android)){
                    $token_model->is_ios = 0;
                }
                $token_model->save(false);
                return true;
            }
        }
        $user->username = $result->formattedName;
        $this->username = $result->formattedName;
        $this->email = $result->emailAddress;
        if(isset($result->pictureUrls)){
            $user->avatar = $result->pictureUrls->values[0];
            $this->avatar = $result->pictureUrls->values[0];
        } else {
            $user->avatar = 'No image';
        }
        $user->setPassword($result->id);
        $user->generateAuthKey();
        $user->status = 10;

        if($user->save()){
            $model = new PushNotifications();
            $token_model = new TokenDevices();

            $model->user_id = $user->getPrimaryKey();
            $token_model->user_id = $user->getPrimaryKey();
            $token_model->token_device = $token_device;
            if(isset($android)){
                $token_model->is_ios = 0;
            }

            $token_model->save(false);

            $model->save(false);
            return true;
        } else {
            return false;
        }
    }

    //Basic login
    public function login($request)
    {
        $user = $this->findByEmail($request['email']);
        $token_device = $request['token_device'];
        $flag = false;
        if($user){
            if(Yii::$app->security->validatePassword($request['password'], $user->password_hash)){
                $token = TokenDevices::find()->where(['user_id'=>$user->id, 'token_device'=>$token_device])->all();
                if($token){
                    foreach ($token as $item){
                        if($item->token_device == $token_device){
                            return true;
                        } else {
                            $flag = true;
                        }
                    }
                    if($flag == true){
                        $token_model = new TokenDevices();
                        $token_model->user_id = $user->id;
                        $token_model->token_device = $token_device;
                        if(isset($request['android'])){
                            $token_model->is_ios = 0;
                        }
                        $token_model->save(false);
                        return true;
                    }
                } else {
                    $token_model = new TokenDevices();
                    $token_model['user_id'] = $user->id;
                    $token_model->token_device = $token_device;
                    if(isset($request['android'])){
                        $token_model->is_ios = 0;
                    }
                    $token_model->save(false);
                    return true;
                }
            } else {
                return false;
            }
        } return false;
    }

    //Get user Data by ID
    public function UserData($user_id)
    {
        $user = static::find()
            ->where(['id' => $user_id])
            ->one();

        if(isset($user)){
            if(!$user->raiting){
                $user->raiting = 0;
            }

            return array(
                'status' => 200,
                'data' => [
                    'id' => $user->id,
                    'photo' => $user->avatar,
                    'name' => $user->username,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'country' => $user->country,
                    'city' => $user->city,
                    'rating' => $user->raiting
                ]
            );
        } else {
            return array(
                'status' => 404,
                'message' => 'User not found.'
            );
        }
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $time = time();

            if ($this->isNewRecord) {
                $this->created_at = $time;
            }

            $this->updated_at = $time;

            return true;
        }

        return false;
    }

}
