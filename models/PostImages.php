<?php

namespace app\models;

use Yii;
use yii\web\UploadedFile;

class PostImages extends \yii\db\ActiveRecord
{

    public $photo;

    public static function tableName()
    {
        return 'post_image';
    }


    public function rules()
    {
        return [
            [['photo'], 'file'],
            [['image'], 'string', 'max' => 255],
            [['post_id'], 'integer']
        ];
    }
    
    public function upload($photos, $post_id)
    {
        if(isset($photos)){
            foreach($photos as $photo){
                $model = new PostImages();
                $imageName = uniqid();
                $photo->saveAs('post_image/' . $imageName . '.' . $photo->extension);
                $model->image = 'post_image/' . $imageName . '.' . $photo->extension;
                $model->post_id = $post_id;
                $model->save(false);
                $result[] = array(
                    'photo' => 'http://vlad.urich.org/web/'.$model->image
                );
            }
            return $result;
        } else {
            return null;
        }
    }

}
