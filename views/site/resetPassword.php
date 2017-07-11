<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Reset password';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-reset-password">
    <h1 style="text-align: center"><?= Html::encode($this->title) ?></h1>
    <p style="text-align: center">Please choose your new password:</p>
    <div class="row">
        <div class="col-lg-4"></div>
        <div class="col-lg-4 ">
            <?php $form = ActiveForm::begin([
                'action' => '/site/reset-password?token='.Yii::$app->request->get('token'),
                'id' => 'reset-password-form'
            ]); ?>
            <?= $form->field($model, 'password')->passwordInput(['autofocus' => true]) ?>
            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <div class="col-lg-4"></div>
    </div>
</div>