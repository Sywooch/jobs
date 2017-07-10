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
            [['specification', 'title', 'latitude', 'longitude', 'user_id', 'price', 'category_id'], 'required'],
            [['title', 'latitude', 'longitude'], 'string', 'max' => 100],
            [['name'], 'string']
        ];
    }

}
