<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var frontend\models\ChangePasswordForm $model
 */
$this->title = 'Change Password';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-change-password">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'change-password-form']); ?>
            <?= $form->field($model, 'oldPassword')->passwordInput() ?>
            <?= $form->field($model, 'newPassword')->passwordInput() ?>
            <?= $form->field($model, 'newPasswordRepeat')->passwordInput() ?>
            <div class="form-group">
                <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
