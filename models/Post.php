<?php

namespace app\models;

use Yii;

class Post extends \yii\db\ActiveRecord
{
    
    public static function tableName()
    {
        return 'post';
    }


    public function rules()
    {
        return [
            [['name', 'title', 'latitude', 'longitude', 'user_id'], 'required'],
            [['name', 'latitude', 'longitude'], 'string', 'max' => 100],
            [['title'], 'string']
        ];
    }

}
