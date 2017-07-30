<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;

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
        return static::findOne(['id' => $message_id]);
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
        $dataProvider = new ActiveDataProvider([
            'query' => static::find()
                ->select(['id', 'sender_id', 'message', 'image', 'status', 'date'])
                ->where(['recepient_id' => $user->id])
                ->groupBy('`sender_id` DESC'),
            'pagination' => [
                'pagesize' => 20
            ]
        ]);

        return $dataProvider;
    }

}
