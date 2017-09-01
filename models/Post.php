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
            [['title', 'latitude', 'longitude'], 'string', 'max' => 100]
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
                post.title, post.price, post_image.image, post.latitude, post.longitude, post.category_id AS categoryID, category.name as categoryName, date
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
                post.title, post.price, post_image.image, post.latitude, post.longitude, post.category_id AS categoryID, category.name as categoryName, date
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
                'categoryID' => $post->category->id,
                'date' => $post->date
            )
        ];

        return $response;
    }

    //Search post by title
    public function PostSearch($title)
    {
        $dataProvider = new SqlDataProvider([
            'sql' => "SELECT  post.id AS post_id, post.user_id as creatorID, post.specification,
                post.title, post.price, post_image.image, post.latitude, post.longitude, post.category_id AS categoryID, category.name as categoryName, date
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

    //Search posts in radius
    public function GeoSearch($lat, $lng, $r)
    {
        $dataProvider = new SqlDataProvider([
            'sql' => "SELECT id AS post_id, title, latitude, longitude, date,
                ROUND((6371 * acos(cos(radians({$lat})) * cos(radians(latitude)) * cos(radians(longitude) - radians({$lng})) + sin(radians({$lat})) * sin(radians(latitude)))), (2)) AS distance
                FROM post 
                WHERE status = 0
                HAVING distance < {$r}
                ORDER BY distance",
            'pagination' => [
                'pageSize' => 20
            ]
        ]);

        return $dataProvider;
    }

    //Add raiting to User
    public function AddRaiting($raiting, $user_id, $current_user)
    {
        $user = User::findOne(['id' => $user_id]);
        
        if(!$user->raiting){
            $user->raiting = $raiting;
            if($user->save(false)){
                $this->SendPush($user_id, $current_user);
                return array(
                    'status' => 200,
                    'message' => 'Successfully added.',
                    'user_raiting' => $raiting
                );
            }
        }  else {
            $user->raiting = ($user->raiting + $raiting) / 2;
            if($user->save(false)){
                $this->SendPush($user_id, $current_user);
                return array(
                    'status' => 200,
                    'message' => 'Successfully added.',
                    'user_raiting' => $user->raiting
                );
            }
        }
    }

    public function SendPush($user_id, $current_user)
    {
        $push_text = $current_user->username.' rated you.';
        $token_devices = TokenDevices::findAll(['user_id' => $user_id]);

        if(isset($token_devices)){
            foreach ($token_devices as $t_d){
                if($t_d->token_device != 'SIMULATOR' && $t_d->is_ios == 1){
                    $tokens_ios[] = $t_d->token_device;
                }
                if($t_d->token_device != 'SIMULATOR' && $t_d->is_ios == 0){
                    $tokens_android[] = $t_d->token_device;
                }
            }
            if(isset($tokens_ios)) {
                $apns = Yii::$app->apns;
                $apns->sendMulti($tokens_ios, $push_text,
                    [
                        'sound' => 'default',
                        'badge' => 1
                    ]
                );
            }
            if(isset($tokens_android)){
                $note = Yii::$app->fcm->createNotification("Rating updated", $push_text);
                $note->setColor('#ffffff')
                    ->setBadge(1);

                $message = Yii::$app->fcm->createMessage();
                foreach($tokens_android as $t_a){
                    $message->addRecipient(new Device($t_a));
                }
                $message->setNotification($note)
                    ->setData([]);
            }

        }
    }

}
