<?php

namespace app\models;

use Yii;

class Category extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'category';
    }


    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name', 'image'], 'string', 'max' => 225],
        ];
    }

}
