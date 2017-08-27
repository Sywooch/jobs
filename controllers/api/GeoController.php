<?php

namespace app\controllers\api;

use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBearerAuth;
use yii\web\Response;
use app\models\Post;

class GeoController extends ActiveController
{

    public $modelClass = 'app\models\User';

    protected function verbs()
    {
        return [
            'search' => ['POST']
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
    
    //Search posts in radius
    public function actionSearch()
    {
        $model = new Post();
        
        if(Yii::$app->request->post('latitude') && Yii::$app->request->post('longitude') && Yii::$app->request->post('radius')){
            $post = Yii::$app->request->post();
            return $model->GeoSearch($post['latitude'], $post['longitude'], $post['radius']);
        } else {
            return array(
                'status' => 400,
                'message' => 'Bad parameters.'
            );
        }
    }

}
