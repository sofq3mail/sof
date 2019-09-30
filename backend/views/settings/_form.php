<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Settings */
/* @var $form yii\widgets\ActiveForm */

if ($model->key == 'base_distribution' || $model->key == 'new_distribution'){
    $model->usd = $model->getCurrency('USD');
    $model->eur = $model->getCurrency('EUR');
    $model->rub = $model->getCurrency('RUB');
}
?>

<div class="settings-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'key')->textInput(['maxlength' => true]) ?>

    <?php if ($model->key == 'base_distribution' || $model->key == 'new_distribution'): ?>
        <?= $form->field($model, 'usd')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'rub')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'eur')->textInput(['maxlength' => true]) ?>
    <?php else: ?>
        <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>
    <?php endif; ?>
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
