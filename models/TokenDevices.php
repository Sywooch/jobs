<?php

namespace app\models;

use Yii;

class TokenDevices extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'token_devices';
    }


    public function rules()
    {
        return [
            [['user_id', 'token_device'], 'required'],
            [['token_device'], 'string', 'max' => 255]
        ];
    }

}
