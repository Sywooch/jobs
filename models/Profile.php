<?php

namespace app\models;

use Yii;

class Profile extends User
{

    public $photo;

    public function rules()
    {
        return [

            [['avatar', 'phone'], 'string', 'max' => 255],
//            ['username', 'required'],

            [['country', 'city'], 'string', 'max' => 100],

            [['photo'], 'file'],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
//            ['email', 'unique', 'message' => 'This email address has already been taken.'],
        ];
    }
    
    public function getToken()
    {
        return $this->auth_key = Yii::$app->user->identity->auth_key;
    }

    public function Change($request)
    {
        $profile = Yii::$app->user->identity;
        
        $profile->username = $request['Profile']['name'];
        $profile->email = $this->email;
        $profile->phone = $this->phone;
        $profile->country = $this->country;
        $profile->city = $this->city;

        return $profile->save() ? $profile : null;
    }

}