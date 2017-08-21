<?php

namespace app\models;

use Yii;

class Report extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'report';
    }


    public function rules()
    {
        return [
            [['sender_id', 'post_id', 'text'], 'required'],
            [['sender_id', 'post_id'], 'integer'],
            [['text'], 'string'],
        ];
    }

    //Add report to Post
    public function PostReport($request, $user)
    {
        $this->sender_id = $user->id;
        $this->post_id = $request['post_id'];
        $this->text = $request['text'];
        if($this->save(false)){
            return array(
                'status' => 200,
                'message' => 'Successfully sent report!'
            );
        } else {
            return array(
                'status' => 500,
                'message' => 'Can\'t sent report!'
            );
        }
    }

}
