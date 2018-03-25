<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\SendForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Send money';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-send">
    <p>Please fill out the following fields to send money, <b>your balance: <?= Yii::$app->user->identity->balance ?></b>:</p>

    <?php $form = ActiveForm::begin([
        'id' => 'send-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
    ]); ?>

        <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

        <?= $form->field($model, 'amount')->textInput(['placeholder' => 'example: 12.58']) ?>

        <div class="form-group">
            <div class="col-lg-offset-1 col-lg-11">
                <?= Html::submitButton('Send', ['class' => 'btn btn-primary', 'name' => 'send-button']) ?>
            </div>
        </div>

        <?php if (!empty(Yii::$app->session->hasFlash('message'))) { $message = Yii::$app->session->getFlash('message')[0]; ?>
                <div class="alert alert-<?= $message['type'] ?>"><?= $message['message'] ?></div>
        <?php } ?>

    <?php ActiveForm::end(); ?>
</div>
