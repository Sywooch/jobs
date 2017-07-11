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
    
    public function deletePhoto($request)
    {
        if(isset($request)){
            foreach($request as $item){
                $image = PostImages::findOne(['id' => $item['id']]);
                if(file_exists(getcwd().'/'.$image->image)){
                    unlink(getcwd().'/'.$image->image);
                    $image->delete();
                }
            }
            return true;
        } else {
            return false;
        }
    }
    
    public function upload($photos, $post_id)
    {
        if(isset($photos)){
            $photos = $this->sortPhoto($photos);
            foreach($photos as $photo){
                $model = new PostImages();
                $imageName = uniqid();
                $photo->saveAs('post_image/' . $imageName . '.' . $photo->extension);
                $model->image = 'post_image/' . $imageName . '.' . $photo->extension;
                $model->post_id = $post_id;
                $model->save(false);
                $result[] = array(
                    'id' => $model->id,
                    'photo' => 'http://vlad.urich.org/web/'.$model->image
                );
            }
            return $result;
        } else {
            return null;
        }
    }

    public function sortPhoto($photos){
        usort($photos, array($this, "usort_callback"));

        return $photos;
    }

    static function usort_callback($param_1, $param_2) {
        $order_param_1 = substr($param_1->name, 0, strpos($param_1->name, "-+jobs+"));
        $order_param_2 = substr($param_2->name, 0, strpos($param_2->name, "-+jobs+"));
        if ($order_param_1 == $order_param_2) {
            return 0;
        }
        return ($order_param_1 < $order_param_2) ? -1 : 1;
    }

}
