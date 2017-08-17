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

    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    //get all user posts
    public function UserPosts($user)
    {
        $dataProvider = new SqlDataProvider([
            'sql' => "SELECT  post.id AS post_id, post.user_id as creatorID, post.specification,
                post.title, post.price, post_image.image, post.latitude, post.longitude, post.category_id AS categoryID, category.name as categoryName
                FROM post   
                LEFT JOIN post_image 
                ON post.id = post_image.post_id 
                RIGHT JOIN category
                ON post.category_id = category.id
                WHERE post.user_id = {$user}
                AND post.status = 0
                GROUP BY post.id DESC",
            'pagination' => false,
        ] );
        return $dataProvider;
    }

    //Get post by category
    public function PostsByCategory($category_id)
    {
        $dataProvider = new SqlDataProvider([
            'sql' => "SELECT  post.id AS post_id, post.user_id as creatorID, post.specification,
                post.title, post.price, post_image.image, post.latitude, post.longitude, post.category_id AS categoryID, category.name as categoryName
                FROM post
                LEFT JOIN post_image 
                ON post.id = post_image.post_id
                RIGHT JOIN category
                ON post.category_id = category.id
                WHERE post.category_id = {$category_id}
                AND post.status = 0
                GROUP BY post.id DESC",
            'pagination' => [
                'pageSize' => 10,
            ]
        ]);
        return $dataProvider;
    }

    //Get single post
    public function GetPost($post_id, $user_id)
    {
        $flag = 'NO';

        $post = Post::find()
            ->joinWith(['category'])
            ->where(['post.id' => $post_id])
            ->one();

        $favorite = Favorites::find()
            ->where(['post_id' => $post_id, 'user_id' => $user_id])
            ->one();

        if(isset($favorite)){
            $flag = 'YES';
        }

        $response = [
            array(
                'post_id' => $post->id,
                'title' => $post->title,
                'price' => $post->price,
                'specification' => $post->specification,
                'isFavorite' => $flag,
                'creatorID' => $post->user_id,
                'latitude' => $post->latitude,
                'longitude' => $post->longitude,
                'categoryName' => $post->category->name,
                'categoryID' => $post->category->id
            )
        ];

        return $response;
    }

    //Search post by title
    public function PostSearch($title)
    {
        $dataProvider = new SqlDataProvider([
            'sql' => "SELECT  post.id AS post_id, post.user_id as creatorID, post.specification,
                post.title, post.price, post_image.image, post.latitude, post.longitude, post.category_id AS categoryID, category.name as categoryName
                FROM post
                LEFT JOIN post_image 
                ON post.id = post_image.post_id 
                RIGHT JOIN category 
                ON post.category_id = category.id
                AND post.status = 0
                WHERE post.title LIKE '%{$title}%'
                GROUP BY post.id DESC",
            'pagination' => [
                'pageSize' => 10,
            ]
        ]);

        return $dataProvider;
    }

}
