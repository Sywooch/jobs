<?php

namespace app\models;

use Yii;

class PushNotifications extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'push_notifications';
    }
    

    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'message'], 'integer'],
        ];
    }

    public function ChangePush($request, $user)
    {
        $push = static::findOne(['user_id' => $user->id]);
        if(isset($request['message'])){
            $push->message = $request['message'];
            if($push->save(false)){
                return array(
                    'status' => 200,
                    'message' => 'Successfully changed.'
                );
            }
        }
    }
}
