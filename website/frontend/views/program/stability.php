<?php

/**
 * @var yii\web\View $this
 * @var common\models\Program $model
 * @var yii\widgets\ActiveForm $form
 */
use common\models\Program;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Change Stability - ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'My AI', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="program-upload">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php $form = ActiveForm::begin(); ?>
    <?=
    $form->field($model, 'stability')->dropDownList(
        [
            Program::STABILITY_STABLE => 'Stable',
            Program::STABILITY_BETA => 'Beta',
            Program::STABILITY_ALPHA => 'Alpha',
            Program::STABILITY_DEVELOPMENT => 'Development'
        ]
    ) ?>
    <div class="form-group">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'submit-button']) ?>
    </div>
<?php ActiveForm::end(); ?>