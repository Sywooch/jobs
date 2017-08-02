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
            $recepient = User::findOne(['id' => $request['recepient_id']]);
            $this->recepient_token_device = $recepient->token_device;
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
        $connection = Yii::$app->db;
        $query = $connection->createCommand(
            "SELECT message.*, user.avatar AS sender_avatar, u.avatar AS recepient_avatar
              FROM message
              INNER JOIN user ON user.id = message.sender_id
              INNER JOIN user as u ON u.id = message.recepient_id
              WHERE message.id = {$message_id}"
        )->queryAll();

        return $query;
    }

    //Upload image in chat
    public function ImageUpload($photo, $recepient_id, $sender, $text)
    {
        if(isset($photo)){
            $recepient = User::findOne(['id' => $recepient_id]);
            $this->recepient_token_device = $recepient->token_device;
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

    //Find all Inbox chat users
    public function InboxUsers($user)
    {
        $dataProvider = new SqlDataProvider([
            'sql' => "SELECT message.id, message.status, sender_id AS recepient_sender_id, user.username AS sender_username, message, image, date, user.avatar AS sender_avatar
              FROM (SELECT * FROM message ORDER BY message.id DESC) AS message
              JOIN user ON user.id = message.sender_id
              WHERE message.recepient_id = {$user->id}
              GROUP BY sender_id
              ORDER BY message.id DESC",
            'pagination' => [
                'pagesize' => 20
            ]
        ]);

        return $dataProvider;
    }

    //Find all Outbox chat users
    public function OutboxUsers($user)
    {
        $dataProvider = new SqlDataProvider([
            'sql' => "SELECT message.id, recepient_id AS recepient_sender_id, u.username AS recepient_username, message, image, date, u.avatar AS recepient_avatar
              FROM (SELECT * FROM message ORDER BY message.id DESC) AS message
              JOIN user as u ON u.id = message.recepient_id
              WHERE message.sender_id = {$user->id}
              GROUP BY recepient_id
              ORDER BY message.id DESC",
            'pagination' => [
                'pagesize' => 20
            ]
        ]);

        return $dataProvider;
    }

    //Get message story
    public function Story($id, $current_user)
    {
        $dataProvider = new SqlDataProvider([
            'sql' => "SELECT message.id, sender_id, message, image, message.status, date, user.avatar AS sender_avatar, u.avatar AS recepient_avatar
              FROM message
              JOIN user ON user.id = message.sender_id
              JOIN user as u ON u.id = message.recepient_id
              WHERE message.sender_id = {$id} AND message.recepient_id = {$current_user->id}
              OR message.sender_id = {$current_user->id} AND message.recepient_id = {$id}
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
              ORDER BY message.date DESC",
            'pagination' => [
                'pagesize' => 20
            ]
        ]);

        return $dataProvider;
    }

}
