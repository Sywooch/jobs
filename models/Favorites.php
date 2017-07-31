<?php

namespace app\models;

use Yii;
use yii\data\SqlDataProvider;

class Favorites extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'favorites';
    }


    public function rules()
    {
        return [
            [['post_id', 'user_id'], 'required'],
        ];
    }

    public function FavoriteList($user_id)
    {
        $dataProvider = new SqlDataProvider([
            'sql' => "SELECT favorites.id AS favorite_id, post.id AS post_id, post.user_id as creatorID, post.specification,
                post.title, post.price, post_image.image, post.latitude, post.longitude, post.category_id AS categoryID, category.name as categoryName
                FROM post
                LEFT JOIN post_image ON post.id = post_image.post_id 
                JOIN favorites ON post.id = favorites.post_id
                JOIN user ON favorites.user_id = user.id
                JOIN category ON post.category_id = category.id
                WHERE user.id = {$user_id}
                GROUP BY favorites.id DESC",
            'pagination' => [
                'pagesize' => 20
            ],
        ] );
        return $dataProvider;
    }

}
