<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Exchange */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="exchange-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'api')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'RUBUSD')->textInput() ?>

    <?= $form->field($model, 'USDEUR')->textInput() ?>

    <?= $form->field($model, 'amount_currency')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
