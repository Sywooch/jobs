<?php

namespace app\controllers\api;

use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\web\Response;
use yii\rest\ActiveController;
use app\models\User;
use app\models\Post;
use yii\web\UploadedFile;

class PostController extends ActiveController
{

    public $modelClass = 'app\models\User';

    protected function verbs()
    {
        return [
            'view' => ['POST'],
            'create' => ['POST'],
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
        ];
        return $behaviors;
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
                    'message' => 'Post successfully saved.'
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

}
