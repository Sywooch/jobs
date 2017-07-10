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

            if($user->avatar) {
                if(!preg_match('/^(http|https):\\/\\/[a-z0-9_]+([\\-\\.]{1}[a-z_0-9]+)*\\.[_a-z]{2,5}'.'((:[0-9]{1,5})?\\/.*)?$/i', $user->avatar) && file_exists(getcwd().'/'.$user->avatar)){
                    $user->avatar = 'http://vlad.urich.org/web/'.$user->avatar;
                }
            }

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

                if($model->avatar) {
                    if(!preg_match('/^(http|https):\\/\\/[a-z0-9_]+([\\-\\.]{1}[a-z_0-9]+)*\\.[_a-z]{2,5}'.'((:[0-9]{1,5})?\\/.*)?$/i', $model->avatar) && file_exists(getcwd().'/'.$model->avatar)){
                        $model->avatar = 'http://vlad.urich.org/web/'.$model->avatar;
                    }
                }

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
