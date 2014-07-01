<?php

/**
 * @var yii\web\View $this
 * @var common\models\SourceCode $code
 * @var common\models\Program $program
 * @var yii\widgets\ActiveForm $form
 */
use common\models\Program;
use common\models\SourceCode;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Upload AI';
$this->params['breadcrumbs'][] = ['label' => 'My AI' , 'url' => ['index'] ];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="program-upload">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($program, 'name')->textInput() ?>
    <?=
    $form->field($program, 'stability')->dropDownList(
        [
            Program::STABILITY_STABLE => 'Stable',
            Program::STABILITY_BETA => 'Beta',
            Program::STABILITY_ALPHA => 'Alpha',
            Program::STABILITY_DEVELOPMENT => 'Development'
        ]
    ) ?>
    <?=
    $form->field($code, 'language')->dropDownList(
        [
            SourceCode::LANGUAGE_C => 'C',
            SourceCode::LANGUAGE_CPP => 'C++'
        ]
    ) ?>
    <?= $form->field($code, 'code')->textarea(['rows' => 10]) ?>
    <div class="form-group">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'submit-button']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>