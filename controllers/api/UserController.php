<?php

namespace app\controllers\api;

use app\models\Post;
use app\models\PushNotifications;
use app\models\TokenDevices;
use paragraph1\phpFCM\Recipient\Device;
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
            'avatar-upload' => ['POST'],
            'account-delete' => ['POST']
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

            if($model->avatar) {
                if(!preg_match('/^(http|https):\\/\\/[a-z0-9_]+([\\-\\.]{1}[a-z_0-9]+)*\\.[_a-z]{2,5}'.'((:[0-9]{1,5})?\\/.*)?$/i', $model->avatar) && file_exists(getcwd().'/'.$model->avatar)){
                    $model->avatar = 'http://vlad.urich.org/web/'.$model->avatar;
                }
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

    //Upload Avatar
    public function actionAvatarUpload()
    {
        $user = Yii::$app->user->identity;
        $photo = UploadedFile::getInstanceByName("photo");
        $imageName = uniqid();

        if($photo){
            if($user->avatar) {
                if(!preg_match('/^(http|https):\\/\\/[a-z0-9_]+([\\-\\.]{1}[a-z_0-9]+)*\\.[_a-z]{2,5}'.'((:[0-9]{1,5})?\\/.*)?$/i', $user->avatar) && file_exists(getcwd().'/'.$user->avatar)){
                    unlink(getcwd().'/'.$user->avatar);
                }
            }
            $photo->saveAs('avatars/' . $imageName . '.' . $photo->extension);
            $user->avatar = 'avatars/' . $imageName . '.' . $photo->extension;
            $user->save(false);

            return array(
                'status' => 200,
                'message' => 'Image successfully uploaded.',
                'photo' => 'http://vlad.urich.org/web/'.$user->avatar
            );
        } else {
            return array(
                'status' => 400,
                'message' => 'Bad request.'
            );
        }
    }

    //Google SignUp
    public function actionGlogin()
    {
        $model = new User();
        if(Yii::$app->request->post('token') && $model->gregister(Yii::$app->request->post('token'), Yii::$app->request->post('token_device'), Yii::$app->request->post('android'))){
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
        if(Yii::$app->request->post('token') && $model->fregister(Yii::$app->request->post('token'), Yii::$app->request->post('token_device'), Yii::$app->request->post('android'))){
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
        if(Yii::$app->request->post('token') && $model->lregister(Yii::$app->request->post('token'), Yii::$app->request->post('token_device'), Yii::$app->request->post('android'))){
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
                
                if($user->avatar) {
                    if(!preg_match('/^(http|https):\\/\\/[a-z0-9_]+([\\-\\.]{1}[a-z_0-9]+)*\\.[_a-z]{2,5}'.'((:[0-9]{1,5})?\\/.*)?$/i', $user->avatar) && file_exists(getcwd().'/'.$user->avatar)){
                        $user->avatar = 'http://vlad.urich.org/web/'.$user->avatar;
                    }
                }
                
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
                    'message' => 'Invalid email or password.',
                );
            }
        }
        
        return $response;
    }
    
    //LogOut
    public function actionLogout()
    {
        $model = new User();
        $token = Yii::$app->request->post('token_device');

//        if($model->regenerateAuthKey(Yii::$app->user->getId()) && $model->deleteToken(Yii::$app->user->getId(), $token)){
        if($model->deleteToken(Yii::$app->user->getId(), $token)){
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
    
    //Change push notifications for user
    public function actionPushNotification()
    {
        $model = new PushNotifications();
        $user = Yii::$app->user->identity;
        
        if(Yii::$app->request->post('push')){
            return $model->ChangePush(Yii::$app->request->post('push'), $user);
        } else {
            return array(
                'status' => 400,
                'message' => 'Bad parameters.'
            );
        }
    }

    //get User info by ID
    public function actionGetUserData()
    {
        $model = new User();
        
        if(Yii::$app->request->post('user_id')){
            return $model->UserData(Yii::$app->request->post('user_id'));
        } else {
            return array(
                'status' => 400,
                'message' => 'Bad parameters.'
            );
        }
    }

    //Delete account
    public function actionAccountDelete()
    {
        $user = Yii::$app->user->identity;

        if(Yii::$app->request->post('data') == "delete" && $user){
            $user->auth_key = '';
            $user->status = 0;
            $user->email = $user->email.'(deleted)';
            $user->phone = '';
            if($user->save(false)){
                $posts = Post::find()->where(['user_id' => $user->id])->all();
                if(isset($posts)){
                    foreach ($posts as $post){
                        $post->status = 2;
                        $post->save(false);
                    }
                }
                $toked_devices = TokenDevices::find()->where(['user_id' => $user->id])->all();
                if(isset($token_devices)){
                    foreach($token_devices as $t_d){
                        $t_d->delete();
                    }
                }
            }
            return array(
                'status' => 200,
                'message' => 'Account successfully deleted!'
            );
        } else {
            return array(
                'status' => 400,
                'message' => 'Bad parameters.'
            );
        }
    }
}
