<?php

namespace app\models;

use Yii;

class Profile extends User
{

    public $photo;

    public function rules()
    {
        return [

            [['avatar', 'phone', 'username'], 'string', 'max' => 255],
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
        $this->username = $request['Profile']['name'];
        $profile->email = $this->email;
        $profile->phone = $this->phone;
        $profile->country = $this->country;
        $profile->city = $this->city;

        return $profile->save() ? $profile : null;
    }

//    public function ChangePassword($request, $user)
//    {
//        $current_password = $request['current_password'];
//        $new_password = $request['new_password'];
//
//        if(Yii::$app->security->validatePassword($current_password, $user->password_hash)){
//            $user->setPassword($new_password);
//            $user->save(false);
//            return array(
//                'message' => 'Password successfully changed.'
//            );
//        } else {
//            return array(
//                'message' => 'Invalid current_password.'
//            );
//        }
//    }

    public function sendEmail($user)
    {
        if (!$user) {
            return false;
        }

        if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
            $user->generatePasswordResetToken();
            if (!$user->save()) {
                return false;
            }
        }

        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'passwordResetToken-html', 'text' => 'passwordResetToken-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => 'Jobs robot'])
            ->setTo($user->email)
            ->setSubject('Password reset for ' . Yii::$app->name)
            ->send();
    }

}
