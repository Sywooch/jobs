<?php

namespace app\controllers\api;

use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\web\Response;
use yii\rest\ActiveController;
use app\models\User;
use yii\web\UploadedFile;

class UserController extends ActiveController
{

    public $modelClass = 'app\models\User';

    protected function verbs()
    {
        return [
            'index' => ['POST'],
            'logout' => ['POST'],
            'login' => ['POST'],
            'glogin' => ['POST'],
            'flogin' => ['POST'],
            'llogin' => ['POST'],
//            'view' => ['GET', 'HEAD'],
//            'create' => ['POST'],
//            'update' => ['PUT', 'PATCH'],
//            'delete' => ['DELETE'],
        ];
    }

    public function actions(){
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function behaviors() {

        $behaviors = parent::behaviors();

        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
            'except' => ['index', 'login', 'glogin', 'flogin', 'llogin'],
        ];
        return $behaviors;
    }

    //Basic register
    public function actionIndex(){
        $model = new User();
        if($model->load(Yii::$app->request->post()) && $model->validate() && $model->signup(Yii::$app->request->post())){
            $imageName = uniqid();
            $model->photo = UploadedFile::getInstance($model, 'photo');
            if (isset($model->photo)) {
                $model->photo->saveAs('avatars/' . $imageName . '.' . $model->photo->extension);
                $model->avatar = 'avatars/' . $imageName . '.' . $model->photo->extension;
                $model->save(false);
            }
            $response = array(
                'status' => 200,
                'message' => 'User has been registered.',
                'token' => $model->getUserAuth(),
                'profile' => array(
                    'photo' => $model->avatar,
                    'name' => $model->username,
                    'email' => $model->email,
                    'phone' => $model->phone,
                    'country' => $model->country,
                    'city' => $model->city
                ),
            );
        } else {
            $response = array(
                'status' => 400,
                'message' => $model->getErrors(),
            );
        }

        return $response;
    }

    //Google SignUp
    public function actionGlogin()
    {
        $model = new User();
        if(Yii::$app->request->post('token') && $model->gregister(Yii::$app->request->post('token'))){
            if(isset($model->auth_key)){
                $user = $model->findIdentityByAccessToken($model->auth_key);
                $response = array(
                    'status' => 200,
                    'message' => 'Successfully login.',
                    'token' => $model->auth_key,
                    'profile' => array(
                        'photo' => $user->avatar,
                        'name' => $user->username,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'country' => $user->country,
                        'city' => $user->city
                    ),
                );
            } else {
                $response = array(
                    'status' => 200,
                    'message' => 'Successfully login.',
                    'token' => $model->getUserAuth(),
                    'profile' => array(
                        'photo' => $model->avatar,
                        'name' => $model->username,
                        'email' => $model->email,
                        'phone' => $model->phone,
                        'country' => $model->country,
                        'city' => $model->city
                    ),
                );
            }
        } else {
            $response = array(
                'status' => 400,
                'message' => 'Bad parameters or can\'t login.'
            );
        }

        return $response;
    }

    //Facebook SignUp
    public function actionFlogin()
    {
        $model = new User();
        if(Yii::$app->request->post('token') && $model->fregister(Yii::$app->request->post('token'))){
            if(isset($model->auth_key)){
                $user = $model->findIdentityByAccessToken($model->auth_key);
                $response = array(
                    'status' => 200,
                    'message' => 'Successfully login.',
                    'token' => $model->auth_key,
                    'profile' => array(
                        'photo' => $user->avatar,
                        'name' => $user->username,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'country' => $user->country,
                        'city' => $user->city
                    ),
                );
            } else {
                $response = array(
                    'status' => 200,
                    'message' => 'Successfully login.',
                    'token' => $model->getUserAuth(),
                    'profile' => array(
                        'photo' => $model->avatar,
                        'name' => $model->username,
                        'email' => $model->email,
                        'phone' => $model->phone,
                        'country' => $model->country,
                        'city' => $model->city
                    ),
                );
            }
        } else {
            $response = array(
                'status' => 400,
                'message' => 'Bad parameters or can\'t login.'
            );
        }

        return $response;
    }

    //LinkedIn SignUp
    public function actionLlogin()
    {
        $model = new User();
        if(Yii::$app->request->post('token') && $model->lregister(Yii::$app->request->post('token'))){
            if(isset($model->auth_key)){
                $user = $model->findIdentityByAccessToken($model->auth_key);
                $response = array(
                    'status' => 200,
                    'message' => 'Successfully login.',
                    'token' => $model->auth_key,
                    'profile' => array(
                        'photo' => $user->avatar,
                        'name' => $user->username,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'country' => $user->country,
                        'city' => $user->city
                    ),
                );
            } else {
                $response = array(
                    'status' => 200,
                    'message' => 'Successfully login.',
                    'token' => $model->getUserAuth(),
                    'profile' => array(
                        'photo' => $model->avatar,
                        'name' => $model->username,
                        'email' => $model->email,
                        'phone' => $model->phone,
                        'country' => $model->country,
                        'city' => $model->city
                    ),
                );
            }
        } else {
            $response = array(
                'status' => 400,
                'message' => 'Bad parameters or can\'t login.'
            );
        }

        return $response;
    }

    //Login
    public function actionLogin()
    {
        $model = new User();
        if(Yii::$app->request->post()){
            if($model->login(Yii::$app->request->post())){
                $user = $model->findByEmail(Yii::$app->request->post('email'));
                $response = array(
                    'status' => 200,
                    'message' => 'Successfully login.',
                    'token' => $user->auth_key,
                    'profile' => array(
                        'photo' => $user->avatar,
                        'name' => $user->username,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'country' => $user->country,
                        'city' => $user->city
                    ),
                );
            } else {
                $response = array(
                    'status' => 403,
                    'message' => 'Invalid username or password.',
                );
            }
        }
        
        return $response;
    }
    
    //LogOut
    public function actionLogout()
    {
        $model = new User();
        if($model->regenerateAuthKey(Yii::$app->user->getId())){
            $response = array(
                'status' => 200,
                'message' => 'Successfully logout.'
            );
        } else {
            $response = array(
                'status' => 500,
                'message' => 'Server error'
            );
        }
        return $response;
    }

    //Authorization test
    public function actionTest()
    {
        $user = Yii::$app->user->identity;
        return array(
            'message' => 'Auth OK!',
            'user' => $user
        );
    }
}
