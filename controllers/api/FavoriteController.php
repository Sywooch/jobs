<?php

namespace app\controllers\api;

use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBearerAuth;
use yii\web\Response;
use app\models\Favorites;
use yii\data\ActiveDataProvider;

class FavoriteController extends ActiveController
{

    public $modelClass = 'app\models\User';

    protected function verbs()
    {
        return [
            'add-favorite' => ['POST'],
            'favorites' => ['POST'],
            'remove-favorite' => ['POST']
        ];
    }

    public function behaviors() {

        $behaviors = parent::behaviors();

        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className()
        ];
        return $behaviors;
    }
    
    //Get all Favorites
    public function actionFavorites()
    {
        $model = new Favorites();
        $user = Yii::$app->user->identity;
        if($user){
            return $model->FavoriteList($user->id);
        } else {
            return array(
                'status' => 400,
                'message' => 'Bad parameters.'
            );
        }
    }

    //Add post to favorites
    public function actionAddFavorite()
    {
        $user = Yii::$app->user->identity;
        if(Yii::$app->request->post('post_id')){
            $model = new Favorites();
            $model->post_id = Yii::$app->request->post('post_id');
            $model->user_id = $user->id;
            if($model->save()){
                return array(
                    'favorite_id' => $model->id,
                    'status' => 200,
                    'message' => 'Successfully added.'
                );
            } else {
                return array(
                    'status' => 500,
                    'message' => 'Can\'t add post to favorites.'
                );
            }
        } else {
            return array(
                'status' => 400,
                'message' => 'Bad parameters.'
            );
        }
    }

    //Remove post from favorites
    public function actionRemoveFavorite()
    {
        if(Yii::$app->request->post('favorite_id')){
            $model = Favorites::findOne(['id' => Yii::$app->request->post('favorite_id')]);
            if(isset($model)){
                $model->delete();
                return array(
                    'status' => 200,
                    'message' => 'Successfully removed.'
                );
            } else {
                return array(
                    'status' => 404,
                    'message' => 'Can\'t find favorite post.'
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
