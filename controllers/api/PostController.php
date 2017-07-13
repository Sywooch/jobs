<?php

namespace app\controllers\api;

use app\models\Category;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\web\Response;
use yii\rest\ActiveController;
use app\models\Post;
use app\models\PostImages;
use yii\web\UploadedFile;
use yii\data\ActiveDataProvider;

class PostController extends ActiveController
{

    public $modelClass = 'app\models\User';

    protected function verbs()
    {
        return [
            'view' => ['POST'],
            'create' => ['POST'],
            'update' => ['POST'],
            'delete' => ['POST'],
            'category' => ['POST'],
            'upload-post-image' => ['POST'],
            'user-posts' => ['POST'],
        ];
    }

    public function actions(){
        $actions = parent::actions();
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    public function behaviors() {

        $behaviors = parent::behaviors();

        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
            'except' => ['category']
        ];
        return $behaviors;
    }

    //Get Category
    public function actionCategory()
    {
        $activeData = new ActiveDataProvider([
            'query' => Category::find()->orderBy('id'),
            'pagination' => false,
        ]);
        return $activeData;
    }

    //Create Post
    public function actionCreate()
    {
        $post = new Post();
        $user = Yii::$app->user->identity;
        if($post->load(Yii::$app->request->post()) && isset($user)){
            $post->user_id = $user->id;
            if($post->save()){
                return array(
                    'status' => 200,
                    'message' => 'Post successfully saved.',
                    'post_id' => $post->id
                );
            } else {
                return array(
                    'status' => 400,
                    'message' => 'Can\'t save post.'
                );
            }
        } else {
            return array(
                'status' => 400,
                'message' => 'Bad parameters.'
            );
        }
    }

    //Update Post
    public function actionUpdate()
    {
        if(Yii::$app->request->post('post_id')){
            $request = Yii::$app->request->post();
            $post = Post::findOne(['id' => Yii::$app->request->post('post_id')]);
            if($post){
                $post->title = $request['title'];
                $post->specification = $request['specification'];
                $post->latitude = $request['latitude'];
                $post->longitude = $request['longitude'];
                $post->price = $request['price'];
                $post->category_id = $request['category_id'];
                if($post->save()){
                    return array(
                        'status' => 200,
                        'message' => 'Post successfully updated.',
                        'post' => $post
                    );
                } else{
                    return array(
                        'status' => 400,
                        'message' => 'Can\'t update post.'
                    );
                }
            } else {
                return array(
                    'status' => 404,
                    'message' => 'Post not found.'
                );
            }
        } else {
            return array(
                'status' => 400,
                'message' => 'Bad parameters.'
            );
        }
    }

    //Delete Post
    public function actionDelete()
    {
        if(Yii::$app->request->post('id')){
            $post = Post::findOne(['id' => Yii::$app->request->post('id')]);
            if($post){
                if($post->delete()){
                    return array(
                        'status' => 200,
                        'message' => 'Post successfully deleted.'
                    );
                } else {
                    return array(
                        'status' => 500,
                        'message' => 'Can\'t delete post.'
                    );
                }
            } else {
                return array(
                    'status' => 404,
                    'message' => 'Post not found.'
                );
            }
        } else {
            return array(
                'status' => 400,
                'message' => 'Bad parameters.'
            );
        }
    }
    
    //Upload Images to Post
    public function actionUploadPostImage()
    {
        $model = new PostImages();
        if(Yii::$app->request->post('post_id')){
            $photos = UploadedFile::getInstancesByName("photo");
            if($photos){
                $result = $model->upload($photos, Yii::$app->request->post('post_id'));
                return array(
                    'status' => 200,
                    'message' => 'Photos successfully saved.',
                    'photos' => $result
                );
            } else {
                return array(
                    'status' => 400,
                    'message' => 'Photos not found.'
                );
            }
        } else {
            return array(
                'status' => '400',
                'message' => 'Missing post_id.'
            );
        }
    }

    //Bulk delete post images
    public function actionDeletePostImage()
    {
        $model = new PostImages();
        if(Yii::$app->request->post('image_ids')){
            $request = Yii::$app->request->post('image_ids');
            if($model->deletePhoto($request)){
                return array(
                    'status' => 200,
                    'message' => 'Images successfully deleted.'
                );
            }
        } else {
            return array(
                'status' => 400,
                'message' => 'Bad parameters.'
            );
        }
    }

    //Get all posts by user token
    public function actionUserPosts()
    {
        $user = Yii::$app->user->identity;
        if(Yii::$app->request->post() && $user){
            $dataProvider = new ActiveDataProvider([
                'query' => Post::find()
                    ->select(['id', 'title', 'specification', 'price', 'category_id', 'latitude', 'longitude', 'user_id'])
                    ->where(['user_id' => $user->id]),
                'pagination' => false
            ]);
            return $dataProvider;
        } else {
            return array(
                'status' => 400,
                'message' => 'Bad parameters.'
            );
        }
    }

    //Get posts by category
    public function actionPostsByCategory()
    {
        if(Yii::$app->request->post('category_id')){
            $dataProvider = new ActiveDataProvider([
                'query' => Post::find()
                    ->select(['id', 'title', 'specification', 'price', 'category_id', 'latitude', 'longitude', 'user_id'])
                    ->where(['category_id' => Yii::$app->request->post('category_id')]),
                'pagination' => [
                    'pageSize' => 10,
                ]
            ]);
            return $dataProvider;
        } else {
            return array(
                'status' => 400,
                'message' => 'Bad parameters.'
            );
        }
    }

}
