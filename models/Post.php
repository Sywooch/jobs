<?php

namespace app\models;

use Yii;
use yii\data\SqlDataProvider;

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
        ];
    }

    public function getImages()
    {
        return $this->hasMany(PostImages::className(), ['post_id' => 'id']);
    }

    //get all user posts
    public function UserPosts($user)
    {
        $dataProvider = new SqlDataProvider([
            'sql' => "SELECT  post.id, 
                            post.title, post.price, post_image.image
                          FROM post   
                          LEFT JOIN post_image 
                          ON post.id = post_image.post_id 
                          WHERE post.user_id = {$user->id}
                          GROUP BY post.id",
            'pagination' => false,
        ] );
        return $dataProvider;
    }

    //Get post by category
    public function PostsByCategory($category_id)
    {
        $dataProvider = new SqlDataProvider([
            'sql' => "SELECT  post.id, 
                            post.title, post.price, post_image.image
                          FROM post
                          LEFT JOIN post_image 
                          ON post.id = post_image.post_id 
                          WHERE post.category_id = {$category_id}
                          GROUP BY post.id",
            'pagination' => [
                'pageSize' => 10,
            ]
        ]);
        return $dataProvider;
    }

    //Get single post
    public function GetPost($post_id)
    {
        $dataProvider = new SqlDataProvider([
            'sql' => "SELECT  post.id, 
                            post.title, post.price, post_image.image
                          FROM post
                          LEFT JOIN post_image 
                          ON post.id = post_image.post_id 
                          WHERE post.id = {$post_id}
                          GROUP BY post.id",
            'pagination' => [
                'pageSize' => 10,
            ]
        ]);
        return $dataProvider;
    }

}
