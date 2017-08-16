<?php

namespace app\controllers\api;

use app\models\PushNotifications;
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
            'except' => ['index', 'login', 'glogin', 'flogin', 'llogin', 'push'],
        ];
        return $behaviors;
    }

    public function actionPush()
    {
        $apns = Yii::$app->apns;
        $token = '9d173d4d98720d9b650c083f5dec5628273b38cfd2e15a5e937581a8916ad147';
        if($apns->send($token, 'Ho ho ho!!!',
            [
                'sound' => 'default',
                'badge' => 1
            ]
        )) {
            return 1;
        } else {
            return 0;
        }
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
        if(Yii::$app->request->post('token') && $model->gregister(Yii::$app->request->post('token'), Yii::$app->request->post('token_device'))){
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
        if(Yii::$app->request->post('token') && $model->fregister(Yii::$app->request->post('token'), Yii::$app->request->post('token_device'))){
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
        if(Yii::$app->request->post('token') && $model->lregister(Yii::$app->request->post('token'), Yii::$app->request->post('token_device'))){
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

    //Authorization test
    public function actionTest()
    {
//        $user = Yii::$app->user->identity;
//        return array(
//            'message' => 'Auth OK!',
//            'user' => $user
//        );
        $note = Yii::$app->fcm->createNotification("Title", "Working?");
        $note->setColor('#ffffff')
            ->setBadge(1);

        $message = Yii::$app->fcm->createMessage();
        $message->addRecipient(new Device('123'));
        $message->setNotification($note)
            ->setData(['test_id' => 1]);

        $response = Yii::$app->fcm->send($message);

        return $response->getStatusCode();

    }
}
