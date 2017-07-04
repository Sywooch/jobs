<?php

namespace app\controllers\api;

use app\models\Profile;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\web\Response;
use yii\rest\ActiveController;
use app\models\User;
use yii\web\UploadedFile;

class ProfileController extends ActiveController
{

    public $modelClass = 'app\models\api\Profile';

    protected function verbs()
    {
        return [
            'profile' => ['POST'],
            'change-profile' => ['POST']
        ];
    }

    public function behaviors() {

        $behaviors = parent::behaviors();

        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
        ];
        return $behaviors;
    }

    //Get Profile Data
    public function actionProfile()
    {
        $user = Yii::$app->user->identity;
        if($user){
            return array(
                'status' => 200,
                'profile' => array(
                    'photo' => $user->avatar,
                    'name' => $user->username,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'country' => $user->country,
                    'city' => $user->city
                ),
                'token' => $user->auth_key
            );
        } else {
            return array(
                'status' => 404,
                'message' => 'User not found.'
            );
        }
    }
    
    //Change Profile Data
    public function actionChangeProfile()
    {
        $model = new Profile();
        if($model->load(Yii::$app->request->post())){
            if($model->validate() && $model->Change(Yii::$app->request->post())){
                return array(
                    'status' => 200,
                    'message' => 'Successfully saved.',
                    'profile' => array(
                        'photo' => $model->avatar,
                        'name' => $model->username,
                        'email' => $model->email,
                        'phone' => $model->phone,
                        'country' => $model->country,
                        'city' => $model->city
                    ),
                    'token' => $model->getToken()
                );
            } else {
                return array(
                    'status' => 400,
                    'message' => $model->getErrors()
                );
            }
        } else {
            return array([
                'status' => 400,
                'message' => 'Invalid parameters.'
            ]);
        }
    }
}
