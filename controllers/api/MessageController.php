<?php

namespace app\controllers\api;

use app\models\Message;
use Yii;
use yii\web\Response;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBearerAuth;
use yii\data\ActiveDataProvider;
use yii\web\UploadedFile;

class MessageController extends ActiveController
{

    public $modelClass = 'app\models\User';

    protected function verbs()
    {
        return [
            'send-message' => ['POST'],
            'get-message' => ['POST'],
            'upload-message-photo' => ['POST']
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


    //Send message
    public function actionSendMessage()
    {
        $model = new Message();
        $sender = Yii::$app->user->identity;

        if(Yii::$app->request->post('Message')){
            if($model->SendMessage($sender, Yii::$app->request->post('Message'))){
                $apns = Yii::$app->apns;
                $apns->send($model->recepient_token_device, $model->message,
                    [
                        'sound' => 'default',
                        'badge' => 1
                    ]);
                return array(
                    'status' => 200,
                    'message' => 'Successfully send message.',
                    'data' => array(
                        'id' => $model->id,
                        'sender_id' => $sender->id,
                        'recepient_id' => $model->recepient_id,
                        'text' => $model->message
                    )
                );
            } else {
                return array(
                    'status' => 400,
                    'message' => 'Can\'t send message.'
                );
            }
        } else {
            return array(
                'status' => 400,
                'message' => 'Bad parameters.'
            );
        }
    }

    //Upload Photo in Chat
    public function actionUploadMessagePhoto()
    {
        $model = new Message();
        $sender = Yii::$app->user->identity;
        
        if(Yii::$app->request->post('recepient_id')){
            $image = UploadedFile::getInstanceByName("photo");
            if($image){
                $result = $model->ImageUpload($image, Yii::$app->request->post('recepient_id'), $sender, Yii::$app->request->post('message'));
                return array(
                    'status' => 200,
                    'message' => 'Photo successfully saved.',
                    'data' => $result
                );
            } else {
                return array(
                    'status' => 400,
                    'message' => 'Image not found.'
                );
            }
        } else {
            return array(
                'status' => '400',
                'message' => 'Missing recepient_id.'
            );
        }
    }
    
    //Get Message by ID
    public function actionGetMessage()
    {
        $model = new Message();

        if(Yii::$app->request->post('message_id')){
            return $model->FindMessage(Yii::$app->request->post('message_id'));
        } else {
            return array(
                'status' => 400,
                'message' => 'Bad parameters.'
            );
        }
    }

}
