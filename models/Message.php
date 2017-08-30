<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use yii\db\Command;

class Message extends \yii\db\ActiveRecord
{

    public $recepient_token_device;
    public $photo;

    public static function tableName()
    {
        return 'message';
    }


    public function rules()
    {
        return [
            [['recepient_id'], 'required'],
            [['sender_id'], 'integer'],
            [['message'], 'string'],
            [['image'], 'string', 'max' => 255],
            [['photo'], 'file']
        ];
    }

    public function getSender()
    {
        return $this->hasOne(User::className(), ['id' => 'sender_id']);
    }

    public function getRecepient()
    {
        return $this->hasOne(User::className(), ['id' => 'recepient_id']);
    }

    public function SendMessage($sender, $request)
    {
        if(isset($request['recepient_id'])){
            $this->sender_id = $sender->id;
            $this->recepient_id = $request['recepient_id'];
            $this->message = $request['message'];
            return $this->save();
        } else {
            return false;
        }
    }

    //Find Message by ID
    public function FindMessage($message_id)
    {
        $user = Yii::$app->user->identity;

        $message = static::findOne(['id' => $message_id]);
        if($message->sender_id != $user->id && $message->recepient_id == $user->id){
            if($message->status == 0){
                $message->status = 1;
                $message->save(false);
            }
        }

        $connection = Yii::$app->db;
        $query = $connection->createCommand(
            "SELECT message.*, user.avatar AS sender_avatar, u.avatar AS recepient_avatar
              FROM message
              INNER JOIN user ON user.id = message.sender_id
              INNER JOIN user as u ON u.id = message.recepient_id
              WHERE message.id = {$message_id} AND message.status <> 10"
        )->queryAll();

        return $query;
    }

    //Upload image in chat
    public function ImageUpload($photo, $recepient_id, $sender, $text)
    {
        if(isset($photo)){
            $imageName = uniqid();
            $photo->saveAs('message_image/' . $imageName . '.' . $photo->extension);
            $this->image = 'message_image/' . $imageName . '.' . $photo->extension;
            $this->message = $text;
            $this->recepient_id = $recepient_id;
            $this->sender_id = $sender->id;
            $this->save();
            $result[] = array(
                'id' => $this->id,
                'sender_id' => $sender->id,
                'recepient_id' => $this->recepient_id,
                'text' => $this->message,
                'photo' => 'http://vlad.urich.org/web/'.$this->image
            );
            return $result;
        } else {
            return false;
        }
    }

//    //Find all Inbox chat users
//    public function InboxUsers($user)
//    {
//        $dataProvider = new SqlDataProvider([
//            'sql' => "SELECT message.id, message.status, sender_id AS recepient_sender_id, user.username AS sender_username, message, image, date, user.avatar AS sender_avatar
//              FROM (SELECT * FROM message ORDER BY message.id DESC) AS message
//              JOIN user ON user.id = message.sender_id
//              WHERE message.recepient_id = {$user->id} AND message.status <> 10
//              GROUP BY sender_id
//              ORDER BY message.id DESC",
//            'pagination' => [
//                'pagesize' => 20
//            ]
//        ]);
//
//        return $dataProvider;
//    }
//
//    //Find all Outbox chat users
//    public function OutboxUsers($user)
//    {
//        $dataProvider = new SqlDataProvider([
//            'sql' => "SELECT message.id, recepient_id AS recepient_sender_id, u.username AS recepient_username, message, image, date, u.avatar AS recepient_avatar
//              FROM (SELECT * FROM message ORDER BY message.id DESC) AS message
//              JOIN user as u ON u.id = message.recepient_id
//              WHERE message.sender_id = {$user->id}
//              AND message.status <> 10
//              GROUP BY recepient_id
//              ORDER BY message.id DESC",
//            'pagination' => [
//                'pagesize' => 20
//            ]
//        ]);
//
//        return $dataProvider;
//    }

    //Get all Chat Users
    public function ChatUsers($user)
    {
        $dataProvider = new SqlDataProvider([
            'sql' => "SELECT sender_id, u.username AS chat_user_username, u.avatar AS chat_user_avatar, message.id AS message_id, message, image, date, message.status, IF(recepient_id={$user->id}, sender_id, recepient_id) as chat_user
                FROM (SELECT * FROM message where {$user->id} in (recepient_id, sender_id) ORDER BY message.id DESC) AS message
                JOIN user as u ON u.id = (IF(sender_id = {$user->id}, recepient_id, sender_id))
                GROUP BY chat_user
                ORDER BY message.id DESC",
            'pagination' => [
                'pageSize' => 20
            ]
        ]);

        return $dataProvider;
    }

    //Get message story
    public function Story($id, $current_user)
    {
        $dataProvider = new SqlDataProvider([
            'sql' => "SELECT message.id, sender_id, recepient_id, message, image, message.status, date, user.avatar AS sender_avatar, u.avatar AS recepient_avatar
              FROM message
              JOIN user ON user.id = message.sender_id
              JOIN user as u ON u.id = message.recepient_id
              WHERE message.sender_id = {$id} AND message.recepient_id = {$current_user->id}
              OR message.sender_id = {$current_user->id} AND message.recepient_id = {$id}
              AND message.status <> 10
              ORDER BY message.date DESC",
            'pagination' => [
                'pagesize' => 20
            ]
        ]);

        return $dataProvider;
    }

    //Search message by text
    public function MessageSearch($request, $user)
    {
        $dataProvider = new SqlDataProvider([
            'sql' => "SELECT message.id, sender_id, message, image, date, user.avatar AS sender_avatar, u.avatar AS recepient_avatar
              FROM message
              JOIN user ON user.id = message.sender_id
              JOIN user as u ON u.id = message.recepient_id
              WHERE ((message.sender_id = {$user->id}) OR (message.recepient_id = {$user->id})) AND (message.message LIKE '%{$request}%')
              AND message.status <> 10
              ORDER BY message.date DESC",
            'pagination' => [
                'pagesize' => 20
            ]
        ]);

        return $dataProvider;
    }

    public function DeleteMessageByUserId($request, $user)
    {
        if(isset($request)){
            foreach ($request as $item){
                $messages = static::find()
                    ->where(['recepient_id' => $user->id, 'sender_id' => $item])
                    ->orWhere(['recepient_id' => $item, 'sender_id' => $user->id])
                    ->all();
                if(isset($messages)){
                    foreach ($messages as $message){
                        $message->delete();
                    }
                }
            }
            return array(
                'status' => 200,
                'message' => 'Successfully deleted.'
            );
        } else {
            return array(
                'status' => 400,
                'message' => 'Invalid request.'
            );
        }
    }

    //Search InBox Users
    public function UserSearch($request)
    {
        $user = Yii::$app->user->identity;

        $dataProvider = new SqlDataProvider([
            'sql' => "SELECT sender_id, u.username AS chat_user_username, u.avatar AS chat_user_avatar, message.id AS message_id, message, image, date, message.status, IF(recepient_id={$user->id}, sender_id, recepient_id) as chat_user
                FROM (SELECT * FROM message where {$user->id} in (recepient_id, sender_id) ORDER BY message.id DESC) AS message
                JOIN user as u ON u.id = (IF(sender_id = {$user->id}, recepient_id, sender_id))
                AND u.username LIKE '%{$request}%'
                GROUP BY chat_user
                ORDER BY message.id DESC",
            'pagination' => [
                'pageSize' => 20
            ]
        ]);

        return $dataProvider;
    }
    
    //Send message push 
    public function SendMessagePush($sender, $model)
    {
        $token_devices = TokenDevices::findAll(['user_id'=>$model->recepient_id]);
        $push_text = $sender->username.' sent you '.$model->message;
        if(isset($token_devices)){
            foreach ($token_devices as $t_d){
                if($t_d->token_device != 'SIMULATOR' && $t_d->is_ios == 1){
                    $tokens_ios[] = $t_d->token_device;
                }
                if($t_d->token_device != 'SIMULATOR' && $t_d->is_ios == 0){
                    $tokens_android[] = $t_d->token_device;
                }
            }
            if(isset($tokens_ios)) {
                $apns = Yii::$app->apns;
                $apns->sendMulti($tokens_ios, $push_text,
                    [
                        'message_id' => $model->id,
                        'sender_id' => $sender->id
                    ],
                    [
                        'sound' => 'default',
                        'badge' => 1
                    ]
                );
            }
            if(isset($tokens_android)){
                $note = Yii::$app->fcm->createNotification("New message", $push_text);
                $note->setColor('#ffffff')
                    ->setBadge(1);

                $message = Yii::$app->fcm->createMessage();
                foreach($tokens_android as $t_a){
                    $message->addRecipient(new Device($t_a));
                }
                $message->setNotification($note)
                    ->setData([
                        'message_id' => $model->id,
                        'sender_id' => $sender->id
                    ]);
            }

        }
        return true;
    }

    //Send message photo push 
    public function SendMessagePhotoPush($sender, $model)
    {
        $token_devices = TokenDevices::find()->where(['user_id' => $model->recepient_id])->all();
        $push_text = $sender->username.' sent you photo';
        if(isset($token_devices)){
            foreach ($token_devices as $t_d){
                if($t_d->token_device != 'SIMULATOR' && $t_d->is_ios == 1){
                    $tokens_ios[] = $t_d->token_device;
                }
                if($t_d->token_device != 'SIMULATOR' && $t_d->is_ios == 0){
                    $tokens_android[] = $t_d->token_device;
                }
            }
            if(isset($tokens_ios)){
                $apns = Yii::$app->apns;
                $apns->sendMulti($tokens_ios, $push_text,
                    [
                        'message_id' => $model->id,
                        'sender_id' => $sender->id
                    ],
                    [
                        'sound' => 'default',
                        'badge' => 1
                    ]
                );
            }
            if(isset($tokens_android)){
                $note = Yii::$app->fcm->createNotification("New photo", $push_text);
                $note->setColor('#ffffff')
                    ->setBadge(1);

                $message = Yii::$app->fcm->createMessage();
                foreach($tokens_android as $t_a){
                    $message->addRecipient(new Device($t_a));
                }
                $message->setNotification($note)
                    ->setData([
                        'message_id' => $model->id,
                        'sender_id' => $sender->id
                    ]);
            }
        }
        return true;
    }

}
