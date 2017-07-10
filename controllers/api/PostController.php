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
            'category' => ['POST'],
            'upload-post-image' => ['POST']
//            'update' => ['PUT', 'PATCH'],
//            'delete' => ['DELETE'],
        ];
    }

    public function actions(){
        $actions = parent::actions();
        unset($actions['create']);
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

}
